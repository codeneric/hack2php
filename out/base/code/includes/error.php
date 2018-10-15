<?php //strict
namespace codeneric\phmm\base\includes;
final class ErrorSeverity  { private function __construct() {} 
private static $hacklib_values = array(
"WARNING" => "WARNING" ,
"CRITICAL" => "CRITICAL" ,
"CONSTRUCTOR" => "CONSTRUCTOR" 
);
use \HH\HACKLIB_ENUM_LIKE;
const WARNING = "WARNING";
const CRITICAL = "CRITICAL";
const CONSTRUCTOR = "CONSTRUCTOR";
 }
final class RecoverEnum  { private function __construct() {} 
private static $hacklib_values = array(
"recoverOption" => "recoverOption" 
);
use \HH\HACKLIB_ENUM_LIKE;
const recoverOption = "recoverOption";
 }

class RecoverFunctions {
  public static function recoverOption($args){

    return false;
  }
}

class Error {
  const MAX_ERROR_COUNT = 100;
  public $message;
  public $severity;
  public $recoverFn;
  public $recoverFnParams;
  public $failedVariables;

  public function __construct(
$message,
$failedVariables = [],
$severity = ErrorSeverity::CRITICAL,
$recoverFn = null,
$recoverFnParams = null  ) {
    $this->message = $message;
    $this->severity = $severity;
    $this->recoverFn = $recoverFn;
    $this->failedVariables = $failedVariables;
    $this->recoverFnParams =
      is_null($recoverFnParams) ? [] : $recoverFnParams;

  }

  /*
   * Recover from an error when recoverFn reference is given
   */
  public function recover(){
    $fnRef = $this->recoverFn;

    if (is_null($fnRef) || !RecoverEnum::isValid($fnRef))
      return false;
    /* HH_IGNORE_ERROR[4009] */
    return call_user_func(
      [RecoverFunctions::class, $fnRef],
      $this->recoverFnParams    );
  }
  /*
   * Magic php function which stringifies the class in the invariant call
   */
  public function __toString(){
    return serialize($this);
  }

  /*
   * Maybe parse a string to the Error instance
   * On failure unserialze returns false, which we map to null
   */
  public static function unseralize($serialized){
    $us = unserialize($serialized);
    if ($us instanceof Error)
      return $us;

    return null;
  }

  public static function handle_error_case(
$procentS,
$encodedError = null  ){

    // $args = func_get_arg();
    // $encodedError = $args[0];

    if (!is_object($encodedError)) {
      return self::kernel_panic("Encoded error not expected shape");
    }
    $error = self::unseralize($encodedError);

    if (is_null($error))
      return self::kernel_panic("Encoded error null");

    $error->recover(); // try to recover

    $id = self::getTransientErrorID();
    if (is_null($id))
      return self::kernel_panic("Transient ID null. ".$error->message); //TODO: handle this less fatal?

    $transient = get_transient($id);

    if ("%%PLUGIN_ENV%%" === 'development') {
      $vars = json_encode($error->failedVariables);
      if (class_exists('WPDieException')) {
        throw new \WPDieException(
          $error->message.' ***** Failed variables: '.$vars        );
      } else {
        throw new \PhmmFatalInvariantException(
          $error->message.' ***** Failed variables: '.$vars        );
      }

      if (class_exists("\WPDieException")) {
        throw new \WPDieException(
          $error->message.' ***** Failed variables: '.$vars        );
      } else {
        throw new \PhmmFatalInvariantException(
          $error->message.' ***** Failed variables: '.$vars        );
      }

    }
    $count = $transient === false ? 0 : (int) $transient;

    switch ($error->severity) {
      case ErrorSeverity::CRITICAL:
        if ($count >= self::MAX_ERROR_COUNT) {
          self::deleteTransient($id);
          self::kernel_panic($error->message);
        } else {
          self::updateTransientCount($id, $count + 1);
          self::print_error($error->message, $error->failedVariables);
        }
        break;
      case ErrorSeverity::CONSTRUCTOR:
        // We have an error in constructor
        throw new \PhmmFatalInvariantException($error->message);
        break;
      case ErrorSeverity::WARNING:
        self::updateTransientCount($id, $count + 1);
        self::print_error($error->message, $error->failedVariables);
        break;
    }

  }

  public static function deleteTransient($id){
    return delete_transient($id);
  }
  public static function updateTransientCount($id, $count){
    return set_transient($id, $count, 60 * 60 /* seconds */);
  }
  /*
   * Read the file name and line number of throwing invarinat to use as non-chaning ID for that invariant
   */
  public static function getTransientErrorID(){
    $bt = debug_backtrace();

    if (!is_array($bt))
      return null;
    $needle = "HH\invariant";

    foreach ($bt as $entry) {
      if (array_key_exists("function", $entry) &&
          $entry['function'] === $needle &&
          array_key_exists("file", $entry) &&
          array_key_exists("line", $entry)) {
        return "codeneric/phmm/error/".md5($entry['file'].$entry['line']);
      }

    }

    return null;
  }

  private static function deactivate_plugin(){
    $name = 'photography-management/photography_management.php';

    deactivate_plugins(
      ['photography-management/photography_management.php'],
      true    );
  }

  private static function print_error(
$error = null,
$failedVariables = null  ){
    if (!current_user_can('administrator')) {

      return;
      // wp_die();
    }

    $title = "<h1>Photography Management</h1>";

    // If the logger class has been enqueued already, use it to log the error
    try {
      if (class_exists("\codeneric\phmm\Logger", false)) {
        \codeneric\phmm\Logger::urgent(
          is_null($error) ? "Fatal Error with no name" : $error,
          $failedVariables        );
      }
    } catch (\Exception $e) {
      //
    }

    try {
      $backtrace = debug_backtrace();
    } catch (\Exception $e) {
      $backtrace = null;
    }
    if (is_null($backtrace))
      $backtrace = [];

    try {

      ob_start();
      phpinfo(-1);
      $phpinfo = ob_get_contents();
      ob_get_clean();
    } catch (\Exception $e) {
      $phpinfo = "";
    }

    $data = ["backtrace" => $backtrace, "phpinfo" => $phpinfo];

    // If the logger class has been enqueued already, use it to log the error
    try {
      if (class_exists("\codeneric\phmm\Logger", false)) {
        \codeneric\phmm\Logger::urgent(
          is_null($error) ? "Fatal Error with no name" : $error,
          [
            "backtrace" => $backtrace,
            "phpinfo" => $phpinfo,
            "failedVariables" => $failedVariables,
          ]        );
      }
    } catch (\Exception $e) {
      //
    }

    $backtraceJSON =
      htmlspecialchars(json_encode($backtrace), ENT_QUOTES, 'UTF-8'); //json_encode($data, true);
    $phpinfoEscaped = htmlspecialchars($phpinfo, ENT_QUOTES, 'UTF-8'); //json_encode($data, true);
    $body =
      "<div id='cc_phmm_fatal_error' data-phpinfo='$phpinfoEscaped'  data-backtrace='$backtraceJSON' >
            <div style=\"background:url('images/spinner.gif') no-repeat;background-size: 20px 20px;vertical-align: middle;margin: 0 auto;height: 20px;width: 20px;display:block;\"></div>
      </div>";

    if ("%%PLUGIN_ENV%%" === 'development') {
      $path = "http://192.168.0.43:4242/base/phmm.fatal.error.js";
    } else {

      $filename = "phmm.fatal.error.js";

      $path = plugins_url('../assets/js/'.$filename, __FILE__);

    }

    $rand = function() {
      if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes(20));
      } else {
        return strval(mt_rand());
      }
    };

    $nonce = $rand(); //get a random nonce
    if (function_exists('current_user_can')) { //luckely we failed quite late
      if (current_user_can('administrator')) { // in this case only admin can deactivate the plugin
        set_transient('codeneric_phmm_deactivate', $nonce);
      } else {
        // delete_transient('codeneric_phmm_deactivate');
        $nonce = '';
      }
    } else { //everybody can deactivate the plugin
      set_transient('codeneric_phmm_deactivate', $nonce);
    }

    $failedVariablesStringified = json_encode($failedVariables);
    if ($failedVariablesStringified === false) // encoding failed
      $failedVariablesStringified = "";

    $errorName = self::get_error_name($error, $backtrace);
    $pluginsDir = plugins_url("assets/js/", dirname(__FILE__));
    $baseUrl = get_site_url();
    $localize = "<script type='text/javascript' >
        codeneric_phmm_plugins_dir = '$pluginsDir';
        codeneric_phmm_nonce = '$nonce';
        codeneric_error_name = '$errorName';
        codeneric_failed_variables = $failedVariablesStringified;
        codeneric_base_url = '$baseUrl';
    </script>";
    $script = "<script type='text/javascript' src='$path'></script>";
    wp_die($body.$localize.$script);
  }
  private static function kernel_panic($error = null){

    self::deactivate_plugin();
    $title = "<h1>Photography Management Kernel Panic</h1>";
    wp_die($title.(!is_null($error) ? $error : ""));

  }

  public static function get_error_name(
$error,
$backtrace  ){
    $unknownErrorName = "Unknown error";

    if (!is_array($backtrace))
      return is_null($error) ? $unknownErrorName : $error;

    $e = is_null($error) ? "" : $error." ";

    foreach ($backtrace as $trace) {
      if (is_array($trace) &&
          array_key_exists('function', $trace) &&
          array_key_exists('file', $trace) &&
          array_key_exists('line', $trace) &&
          $trace['function'] === "HH\invariant") {
        $path = $trace["file"].":".$trace["line"];

        $result = [];
        preg_match("/.*(photography\-management.*)/", $path, $result);

        if (count($result) >= 2)
          return $e.$result[1]; else
          return $e.$path;

      }
    }

    return is_null($error) ? $unknownErrorName : $error;

  }

}
