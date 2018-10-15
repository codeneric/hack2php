<?hh

namespace codeneric\phmm\validate;
use codeneric\phmm\base\includes\Error;

class Handler {
  static $storage = null;
  private static function init(string $schema, ?string $baseUrl = null) {
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

  public static function validate<T>(
    T $data,
    string $schema,
  ): (T, \JsonSchema\Validator) {
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

    return tuple($data, $validator);

  }

}

function send_feedback(mixed $data): \codeneric\phmm\type\send_feedback {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\send_feedback, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "send_feedback.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('POST params are not valid!', $v->getErrors()),
  );
  return $d;
}

function fetch_images(mixed $data): \codeneric\phmm\type\fetch_images {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\fetch_images, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "fetch_images.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('POST params are not valid!', $v->getErrors()),
  );
  return $d;
}

function label_photo(mixed $data): \codeneric\phmm\type\label_photo {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\label_photo, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "label_photo.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function check_username(mixed $data): \codeneric\phmm\type\check_username {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\check_username, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "check_username.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function update_premium(mixed $data): \codeneric\phmm\type\update_premium {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\update_premium, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "update_premium.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function check_email(mixed $data): \codeneric\phmm\type\check_email {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\check_email, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "check_email.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function protected_file_request(
  mixed $data,
): \codeneric\phmm\type\protected_file_request {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\protected_file_request, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "protected_file_request.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function post_comment(mixed $data): \codeneric\phmm\type\post_comment {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\post_comment, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "post_comment.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function get_comments(mixed $data): \codeneric\phmm\type\get_comments {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\get_comments, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "get_comments.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function get_comments_count(
  mixed $data,
): \codeneric\phmm\type\get_comments_count {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\get_comments_count, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "get_comments_count.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function get_canned_email_preview(
  mixed $data,
): \codeneric\phmm\type\get_canned_email_preview {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\get_canned_email_preview, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "get_canned_email_preview.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function send_canned_email(
  mixed $data,
): \codeneric\phmm\type\send_canned_email {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\send_canned_email, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "send_canned_email.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function client_from_client(
  mixed $data,
): \codeneric\phmm\type\client_from_client {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\client_from_client, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "client_from_client.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function project_from_admin(
  mixed $data,
): \codeneric\phmm\type\project_from_admin {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\project_from_admin, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "project_from_admin.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function configuration(mixed $data): \codeneric\phmm\type\configuration {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\configuration, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "configuration.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);

  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('configuration Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function project_to_admin(mixed $data): \codeneric\phmm\type\project_to_admin {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\project_to_admin, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "project_to_admin.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function image(mixed $data): \codeneric\phmm\type\image {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\image, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "image.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function project_to_client(
  mixed $data,
): \codeneric\phmm\type\project_to_client {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\project_to_client, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "project_to_client.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function project_protection(
  mixed $data,
): \codeneric\phmm\type\project_protection {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\project_protection, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "project_protection.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}
function get_interactions(mixed $data): \codeneric\phmm\type\get_interactions {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\get_interactions, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "get_interactions.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function comment(mixed $data): \codeneric\phmm\type\comment {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\comment, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "comment.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function save_comment(mixed $data): \codeneric\phmm\type\save_comment {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\save_comment, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "save_comment.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function plugin_settings(mixed $data): \codeneric\phmm\type\plugin_settings {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\plugin_settings, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "plugin_settings.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function canned_email(mixed $data): \codeneric\phmm\type\canned_email {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\canned_email, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "canned_email.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function get_download_zip_parts(
  mixed $data,
): \codeneric\phmm\type\get_download_zip_parts {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\get_download_zip_parts, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "get_download_zip_parts.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}
function client_to_db(mixed $data): \codeneric\phmm\type\client_to_db {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\client_to_db, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "client_to_db.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}
function client_project_access(
  mixed $data,
): \codeneric\phmm\type\client_project_access {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\client_project_access, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "client_project_access.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}
function canned_email_history(
  mixed $data,
): \codeneric\phmm\type\canned_email_history {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\canned_email_history, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "canned_email_history.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}
function get_proofing_csv(mixed $data): \codeneric\phmm\type\get_proofing_csv {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\get_proofing_csv, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "get_proofing_csv.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}
function watermark(mixed $data): \codeneric\phmm\type\watermark {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\watermark, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "watermark.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}

function event(mixed $data): \codeneric\phmm\type\event {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\event, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "event.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}


function get_original_image_url_request(mixed $data): \codeneric\phmm\type\get_original_image_url_request {
  $func = function(
    mixed $data,
  ): (\codeneric\phmm\type\get_original_image_url_request, \JsonSchema\Validator) {
    list($d, $v) = Handler::validate($data, "get_original_image_url_request.json");
    return /*UNSAFE_EXPR*/ tuple($d, $v);
  };
  //use the magic function
  list($d, $v) = $func($data);
  //ATTENTION: hack might currently believe something wrong. Do not code here!
  invariant(
    $v->isValid(),
    '%s',
    new Error('Params are not valid!', $v->getErrors()),
  );
  return $d;
}