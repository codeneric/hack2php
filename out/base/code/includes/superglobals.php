<?php //strict
namespace codeneric\phmm\base\globals;

class Superglobals {

  static function Server(){
    return /*UNSAFE_EXPR*/ $_SERVER;
  }

  static function Get(){
    return /*UNSAFE_EXPR*/ $_GET;
  }

  static function Post(){
    return /*UNSAFE_EXPR*/ $_POST;
  }

  static function Files(){
    return /*UNSAFE_EXPR*/ $_FILES;
  }

  static function Cookie(){
    return /*UNSAFE_EXPR*/ $_COOKIE;
  }

  static function Session(){
    return /*UNSAFE_EXPR*/ $_SESSION;
  }

  static function Request(){
    return /*UNSAFE_EXPR*/ $_REQUEST;
  }

  static function Env(){
    return /*UNSAFE_EXPR*/ $_ENV;
  }
  static function Globals($key){
    $global = /*UNSAFE_EXPR*/ $GLOBALS;

    if (array_key_exists($key, $global))
      return $global[$key];

    return null;
  }
}
