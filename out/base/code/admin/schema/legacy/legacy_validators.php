<?php
namespace codeneric\phmm\legacy\validate;
use \codeneric\phmm\legacy;
use \codeneric\phmm\base\includes\Error;

class Handler {

  private static function init($schema, $baseUrl = null) {

    if (is_null($baseUrl))
      $baseUrl = plugin_dir_path(__FILE__);

    // map over schemas
    // required: enum value is equal to file name
    $schemaStorage = new \JsonSchema\SchemaStorage();
    foreach (glob("$baseUrl*.json") as $filename) {
      $json = json_decode(file_get_contents($filename));
      $temp_s = basename($filename);
      $schemaStorage->addSchema('file://'.$temp_s, $json);
    }

    $validatorFactory = new \JsonSchema\Constraints\Factory($schemaStorage);

    return array($schemaStorage, $validatorFactory);
  }

  public static function validate(
$data,
$schema  ){
    list($schemaStorage, $validatorFactory) = self::init($schema);

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

function client_data_representation_4_0_0(
$data){
  $func = function(
$data  ){
    list($d, $v) =
      Handler::validate($data, "client_data_representation_4_0_0.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function project_data_representation_4_0_0(
$data){
  $func = function(
$data  ){
    list($d, $v) =
      Handler::validate($data, "project_data_representation_4_0_0.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function client_data_representation_3_6_5(
$data){
  $func = function(
$data  ){
    list($d, $v) =
      Handler::validate($data, "client_data_representation_3_6_5.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function project_data_representation_3_6_5(
$data){
  $func = function(
$data  ){
    list($d, $v) =
      Handler::validate($data, "project_data_representation_3_6_5.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function comment_data_representation_3_6_5(
$data){
  $func = function(
$data  ){
    list($d, $v) =
      Handler::validate($data, "comment_data_representation_3_6_5.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}

function plugin_settings_data_representation_3_6_5(
$data){
  $func = function(
$data  ){
    list($d, $v) =
      Handler::validate($data, "plugin_settings_data_representation_3_6_5.json");
    return /*UNSAFE_EXPR*/ array($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
\HH\invariant(    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()));
  return $d;
}
