<?hh //strict

namespace codeneric\phmm;
require_once plugin_dir_path(dirname(__FILE__)).'includes/requires.php';


use \codeneric\phmm\Configuration as Configuration;

use \codeneric\phmm\Logger;

class Migrator {

  const page_name = 'cc_phmm_migration';

  public static function init(): void {
    add_action(
      'admin_notices',
      function() {
        self::render_notice();
      },
    );

    add_action(
      "wp_ajax_phmm_migration_progress",
      function() {
        self::request();
      },
    );
    add_action(
      "wp_ajax_phmm_migration_running",
      function() {
        self::request_is_running();
      },
    );

  }

  public static function request(): void {
    try {

      $progress = DBUpdater::update(Configuration::get());
      if (!is_null($progress))
        $progress = (int) ($progress * 100);
      Logger::debug("Progress", $progress);

      wp_send_json_success($progress);
    } catch (\Exception $e) {
      Logger::error("DB Updater raised exception", $e);
      wp_send_json_error(0);
    }

  }
  public static function request_is_running(): void {
    $running = \codeneric\phmm\Semaphore::is_running(DBUpdater::$mutex_name);

    wp_send_json_success($running);
  }

  public static function render_notice(): void {

    $pluginsDir = plugins_url("assets/js/", dirname(__FILE__));

    $class = "error";

    $message =
      "<div id='cc_phmm_migration_page'>
       <div style=\"background:url('images/spinner.gif') no-repeat;background-size: 20px 20px;vertical-align: middle;margin: 0 auto;height: 20px;width: 20px;display:block;\"></div>
    </div>";

    $localize = "<script type='text/javascript' >
        codeneric_phmm_plugins_dir = '$pluginsDir';
    </script>";
    $src = Configuration::get()['assets']['js']['admin']['migration'];
    $script = "<script src='$src'></script>";

    echo "<div>$message$localize$script</div>";

  }

}
