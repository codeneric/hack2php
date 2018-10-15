<?php //strict
namespace codeneric\phmm\base\admin\ajax;

class Helper {

  public static function validate_username_fallback($un){
    if (strlen($un) === 0)
      return false;
    $char_white_list =
      "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890 ";
    if ($un[0] === ' ' || $un[strlen($un) - 1] === ' ')
      return false;
    for ($i = 0; $i < strlen($un); $i++) {
      if (strpos($char_white_list, $un[$i]) === false)
        return false;
    }
    return true;
  }
}
