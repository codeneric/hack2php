<?php //strict
namespace codeneric\phmm\base\admin\ajax;

use \codeneric\phmm\base\includes\Error;

use \codeneric\phmm\base\globals\Superglobals;


/*
 * A shared class with basic logic for base and premium usage
 */
class Request {

  public static function getPayload(){
    $P = Superglobals::Post();
\HH\invariant(      array_key_exists('payload', $P),
      '%s',
      new Error('Payload not set'));
\HH\invariant(      is_string($P['payload']),
      '%s',
      new Error('Payload not string'));

    //It's okay to use UE here because: 
    //we do not know the exact shape at this point in time and we also don't need to.
    try{
      return /* UNSAFE_EXPR */ (array) json_decode(stripslashes($P['payload']));
    }catch(\Exception $e){
      return null;
    }
    

    // return json_decode(/* UNSAFE_EXPR */ $_POST['payload'], true);
  }

  public static function rejectInvalidRequest(
$error = null,
$statusCode = 422  ){

    $e = is_string($error) ? self::makeError($error) : $error;
    wp_send_json_error(array("error" => $e), $statusCode);

  }

  public static function resolveValidRequest($response){
    wp_send_json_success(["data" => $response]);

    return $response;
  }

  public static function makeError($msg){
    return array((object) array('message' => $msg));
  }

}
