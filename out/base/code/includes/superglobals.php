<?php //strict
namespace codeneric\phmm\base\globals;

class Superglobals {

  public static function Server(){
    return /*UNSAFE_EXPR*/ $_SERVER;
  }

  public static function Get(){
    return /*UNSAFE_EXPR*/ $_GET;
  }

  public static function Post(){
    return /*UNSAFE_EXPR*/ $_POST;
  }

  public static function Files(){
    return /*UNSAFE_EXPR*/ $_FILES;
  }

  public static function Cookie(){
    return /*UNSAFE_EXPR*/ $_COOKIE;
  }

  public static function Session(){
    return /*UNSAFE_EXPR*/ $_SESSION;
  }

  public static function Request(){
    return /*UNSAFE_EXPR*/ $_REQUEST;
  }

  public static function Env(){
    return /*UNSAFE_EXPR*/ $_ENV;
  }
  public static function Globals($key){
    $global = /*UNSAFE_EXPR*/ $GLOBALS;

    if (\array_key_exists($key, $global))
      return $global[$key];

    return null;
  }
}
