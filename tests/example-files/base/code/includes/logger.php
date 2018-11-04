<?hh //strict

namespace codeneric\phmm;
use studio24\Rotate\Rotate;
use \Analog as AnalogLogger;

/* 
 Class for initializing and customizing Analog logger funcs (ability to pass additional data safely)
 */
class Logger {
  static int $jsonOptions = 0;
  /*
   This function must be called before any logs are made
   */
  public static function init(): void {
    if ("%%TEST_ENV%%" !== "true" || "%%PLUGIN_ENV%%" === "production") {

      $customHandler =
        function() {

          $upload_dir = wp_upload_dir();

          $upload_dir = $upload_dir['basedir'].'/photography_management';
          if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
          }

          $log_file = $upload_dir.'/phmm.log';
          $maxLogSize = "2MB";

          /* HH_IGNORE_ERROR[2049] Class Rotate not known by hack. Mini dependency, not included in hack for now */
          $rotate = new Rotate($log_file);
          $rotate->keep(5);
          $rotate->size($maxLogSize);
          $rotate->run();

          return AnalogLogger\Handler\File::init($log_file);

        };

      AnalogLogger::$format = "%s - %s - %s - %s\n"; // required for LevelName
      AnalogLogger::handler(
        // Analog\Handler\Threshold::init(
        AnalogLogger\Handler\LevelName::init($customHandler()),
      // ),
      // Analog::DEBUG,
      );

    } else {

      self::$jsonOptions = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
      AnalogLogger::handler(AnalogLogger\Handler\ChromeLogger::init());

    }

  }

  private static function parseAdditionalInfo(
    mixed $additional = null,
  ): string {
    if (is_null($additional))
      return "";

    $additionalInfoString = json_encode($additional, self::$jsonOptions);

    if ($additionalInfoString === false)
      return "";

    return " \n ".$additionalInfoString;

  }
  private static function formatString(
    string $msg,
    mixed $additional = null,
  ): string {

    return $msg.self::parseAdditionalInfo($additional);

  }

  public static function info(
    string $message,
    mixed $additional = null,
  ): void {

    try {
      AnalogLogger::info(self::formatString($message, $additional));
    } catch (\Exception $e) {
      // do nothing
    }
  }
  public static function error(
    string $message,
    mixed $additional = null,
  ): void {

    try {
      AnalogLogger::error(self::formatString($message, $additional));
    } catch (\Exception $e) {
      // do nothing
    }

  }
  public static function urgent(
    string $message,
    mixed $additional = null,
  ): void {

    try {
      AnalogLogger::urgent(self::formatString($message, $additional));
    } catch (\Exception $e) {
      // do nothing
    }
  }
  public static function alert(
    string $message,
    mixed $additional = null,
  ): void {

    try {
      AnalogLogger::alert(self::formatString($message, $additional));
    } catch (\Exception $e) {
      // do nothing
    }
  }
  public static function notice(
    string $message,
    mixed $additional = null,
  ): void {

    try {
      AnalogLogger::notice(self::formatString($message, $additional));
    } catch (\Exception $e) {
      // do nothing
    }
  }
  public static function debug(
    string $message,
    mixed $additional = null,
  ): void {

    try {
      AnalogLogger::debug(self::formatString($message, $additional));
    } catch (\Exception $e) {
      // do nothing
    }
  }
  public static function warning(
    string $message,
    mixed $additional = null,
  ): void {
    try {
      AnalogLogger::warning(self::formatString($message, $additional));
    } catch (\Exception $e) {
      // do nothing
    }
  }
}

/* HH_IGNORE_ERROR[1002] Initialize logger on file require so every log gets caught */
Logger::init();

