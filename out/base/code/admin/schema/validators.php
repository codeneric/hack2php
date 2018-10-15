<?php
namespace codeneric\phmm\validate;
use \codeneric\phmm\base\includes\Error;

class Handler {
  static $storage = null;
  private static function init($schema, $baseUrl = null) {
    if (!is_null(self::$storage))
      return self::$storage;
    if (is_null($baseUrl))
      $baseUrl = plugin_dir_path(__FILE__);

    // map over schemas
    // required: enum value is equal to file name

    $schemaStorage = new \JsonSchema\SchemaStorage();
    foreach (glob("$baseUrl*.json") as $filename) {
      $json = json_decode(file_get_contents($filename));
      $temp_s = basename($filename);
      // var_dump($temp_s);

      $schemaStorage->addSchema('file://'.$temp_s, $json);
    }

    self::$storage = $schemaStorage;
    return self::$storage;
    // $validatorFactory = new \JsonSchema\Constraints\Factory($schemaStorage);

    // return tuple($schemaStorage, $validatorFactory);
  }

  public static function validate(
$data,
$schema  ){
    $schemaStorage = self::init($schema);
    
    $validatorFactory = new \JsonSchema\Constraints\Factory($schemaStorage);
    // list($schemaStorage, $validatorFactory) = self::init($schema);

    $validator = new \JsonSchema\Validator($validatorFactory);
    $json = $schemaStorage->getSchema('file://'.$schema);

    $validator->validate(
      $data,
      $json,
      // \JsonSchema\Constraints\Constraint::CHECK_MODE_TYPE_CAST |
      \JsonSchema\Constraints\Constraint::CHECK_MODE_APPLY_DEFAULTS |
      \JsonSchema\Constraints\Constraint::CHECK_MODE_COERCE_TYPES // \JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS,
    );
    $validator->reset();

    $validator->validate(
      $data,
      $json,
      \JsonSchema\Constraints\Constraint::CHECK_MODE_NORMAL |
      \JsonSchema\Constraints\Constraint::CHECK_MODE_TYPE_CAST // \JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS,
    );

    return array($data, $validator);

  }

}

function send_feedback($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "send_feedback.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('POST params are not valid!', $v->getErrors()));
  return $d;
}

function fetch_images($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "fetch_images.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('POST params are not valid!', $v->getErrors()));
  return $d;
}

function label_photo($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "label_photo.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function check_username($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "check_username.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function update_premium($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "update_premium.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function check_email($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "check_email.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function protected_file_request(
$data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "protected_file_request.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function post_comment($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "post_comment.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function get_comments($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "get_comments.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function get_comments_count(
$data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "get_comments_count.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function get_canned_email_preview(
$data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "get_canned_email_preview.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function send_canned_email(
$data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "send_canned_email.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function client_from_client(
$data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "client_from_client.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function project_from_admin(
$data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "project_from_admin.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function configuration($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "configuration.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('configuration Params are not valid!', $v->getErrors()));
  return $d;
}

function project_to_admin($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "project_to_admin.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function image($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "image.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function project_to_client(
$data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "project_to_client.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function project_protection(
$data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "project_protection.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}
function get_interactions($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "get_interactions.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function comment($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "comment.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function save_comment($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "save_comment.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function plugin_settings($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "plugin_settings.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function canned_email($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "canned_email.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function get_download_zip_parts(
$data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "get_download_zip_parts.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}
function client_to_db($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "client_to_db.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}
function client_project_access(
$data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "client_project_access.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}
function canned_email_history(
$data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "canned_email_history.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}
function get_proofing_csv($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "get_proofing_csv.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}
function watermark($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "watermark.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function event($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "event.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}


function get_original_image_url_request($data){
  $func = function(
$data  ){
    list($d, $v) = Handler::validate($data, "get_original_image_url_request.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}