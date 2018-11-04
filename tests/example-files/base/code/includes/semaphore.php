<?hh //strict

namespace codeneric\phmm;
use \NinjaMutex\Lock\FlockLock;
use \NinjaMutex\Mutex;
use \codeneric\phmm\enums\SemaphoreExecutorReturn;
use codeneric\phmm\base\includes\Error;
use codeneric\phmm\base\globals\Superglobals;
use \codeneric\phmm\Logger;

class Semaphore {
  static string $migration_flag_key = "cc_phmm_migration_flag";
  static string $failed_migration_flag_key = "cc_phmm_failed_migration_flag";

  static float $safety_padding_factor = 0.33;

  private static function get_execution_time(float $start): float {
    return microtime(true) - $start;
  }

  private static function get_lock_dir(): string {
    $upload_dir = wp_upload_dir();
    return $upload_dir['basedir'];

  }

  public static function progress(string $mutex_name): num {
    $semaphore_state = self::get_state(
      $mutex_name,
      function() {
        return [];
      },
    );
    $sum = (count($semaphore_state['outstanding']) +
            count($semaphore_state['finished']) +
            count($semaphore_state['failed']));
    return $sum > 0 ? 1 - count($semaphore_state['outstanding']) / $sum : 1;
  }

  private static function get_max_execution_time(): num {
    $max = null;
    if (function_exists('ini_get'))
      $max = ini_get("max_execution_time");

    $actual = 30;
    if (is_numeric($max)) {
      $num = (int) $max;
      $actual = $num === 0 ? INF : $num;
    }

    Logger::debug('get_max_execution_time', $actual);

    return $actual;

  }

  private static function time_exceeded(float $start): bool {
    $res =
      self::get_execution_time($start) >=
      self::get_max_execution_time() * self::$safety_padding_factor;
    Logger::debug('time_exceeded:', $res);
    return $res;
  }

  private static function memory_exceeded(): bool {
    $memory_limit = self::get_memory_limit() * 0.95;
    $current_memory = memory_get_usage(false);
    $res = $current_memory >= $memory_limit;
    Logger::debug('current_memory:', $current_memory);
    Logger::debug('memory_limit:', $memory_limit);
    Logger::debug('memory_exceeded:', $res);
    return $res;
  }

  private static function get_memory_limit(): int {
    if (function_exists('ini_get')) {
      $memory_limit = ini_get('memory_limit');
    } else {
      // Sensible default.
      $memory_limit = '128M';
    }
    if (!$memory_limit || -1 === intval($memory_limit)) {
      // Unlimited, set to 32GB.
      $memory_limit = '32000M';
    }
    return intval($memory_limit) * 1024 * 1024;
  }

  static function is_running(string $mutex_name): bool {
    $lock = new FlockLock(self::get_lock_dir());
    $mutex = new Mutex($mutex_name, $lock);

    return $mutex->isLocked();
  }

  static function get_state<T>(
    string $state_name,
    (function(): array<T>) $get_all_items,
  ): shape(
    "failed" => array<T>,
    "finished" => array<T>,
    "outstanding" => array<T>,
  ) {

    $state = get_transient($state_name);
    if ($state !== false) {
      return /*UNSAFE_EXPR*/ $state; //IN GOD WE TRUST!
    } else if (!is_null($get_all_items))
      return shape(
        "failed" => [],
        "finished" => [],
        "outstanding" => $get_all_items(),
      ); else {
      invariant(
        false,
        '%s',
        new Error(
          "There is no state with statename '$state_name' stored and no function 'get_all_items' specified!",
        ),
      );
    }
  }

  static function set_state<T>(
    string $state_name,
    shape(
      "failed" => array<T>,
      "finished" => array<T>,
      "outstanding" => array<T>,
    ) $state,
  ): void {

    set_transient($state_name, $state, 60 * 60 * 24);

  }

  static function delete_state(string $state_name): void {
    delete_transient($state_name);
  }

  /*
   This function will be called n times in arbitrary intervals.
   While a previous task is running, a new call should not start
   a new task. This will be handled by a mutex lock
   At any case, a progress int between 0 and 100 should be returned
   In this way, frontend can call this function as often as it likes
   to get the updated progress
   */
  static function run<T>(
    string $mutex_name,
    shape(
      "failed" => array<T>,
      "finished" => array<T>,
      "outstanding" => array<T>,
    ) $state,
    (function(T): SemaphoreExecutorReturn) $fn,
  ): ?shape(
    "failed" => array<T>,
    "finished" => array<T>,
    "outstanding" => array<T>,
  ) {

    $lock = new FlockLock(self::get_lock_dir());
    $mutex = new Mutex($mutex_name, $lock);

    $max_wait =
      self::get_max_execution_time() *
      (1 - self::$safety_padding_factor) *
      1000;

    if ($mutex->acquireLock(0)) {

      // Process locked. Start migration

      // this try catch might be a safety measure for releasing the lock when anything fails
      try {
        $arr = $state["outstanding"];
        $server = Superglobals::Server();
        $start = (float) $server["REQUEST_TIME_FLOAT"];
        Logger::debug('REQUEST_TIME_FLOAT:', $start);
        $res = shape("failed" => [], "finished" => [], "outstanding" => []);

        /*
         While there are any clients to migrate AND the script does not run for
         too long already AND memory is not full,  migrate clients
         */
        while (count($arr) > 0 &&
               !self::time_exceeded($start) &&
               !self::memory_exceeded()) {
          // pull out the first entry. this removes it from the array
          $item = array_shift($arr);

          // try {
          //   $fn($client);
          //   self::flag_migrated_client($client);
          // } catch (\Exception $e) {
          //   self::flag_client_failed_migration($client);
          // }
          $r = $fn($item);
          switch ($r) {
            case SemaphoreExecutorReturn::Finished:
              $res['finished'][] = $item;
              break;
            case SemaphoreExecutorReturn::Failed:
              $res['failed'][] = $item;
              break;
            case SemaphoreExecutorReturn::Outstanding:
              $res['outstanding'][] = $item;
              break;
          }

        }

        $mutex->releaseLock();
        Logger::debug('temp res:', $res);
        Logger::debug('old state:', $state);
        $state['finished'] =
          array_merge($state['finished'], $res['finished']);
        $state['failed'] = array_merge($state['failed'], $res['failed']);
        $state['outstanding'] = array_merge($res['outstanding'], $arr); // arr only contains non-processed items, because of array_shift!
        Logger::debug('new state:', $state);
        return $state;
      } catch (\Exception $e) {

        $mutex->releaseLock();
        throw $e;
      }
    } else {
      // lock not aquired

      return null;
    }
  }
}
