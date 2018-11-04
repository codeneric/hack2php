<?hh

use codeneric\phmm\base\globals\Superglobals;
use codeneric\phmm\base\includes\Error;

function codeneric_send_image_if_allowed(): void {

  $upload_dir = wp_upload_dir();
  $upload_dir = $upload_dir['basedir'];

  $G = Superglobals::Get();

  $data = \codeneric\phmm\validate\protected_file_request($G);

  $f = $data['f'];
  $attach_id = $data['attach_id'];
  $project_id = $data['project_id'];
  $part = $data['part'];

  if ($f !== 'zip-favs' &&
      $f !== 'zip-all' &&
      !file_exists($upload_dir.'/photography_management/'.$f)) {
    if (function_exists('http_response_code'))
      http_response_code(404);
    exit;
  }

  if (\codeneric\phmm\base\protect_images\Main::user_is_permitted(
        $f,
        $attach_id,
        $project_id,
      ) ===
      false) {
    if (function_exists('http_response_code'))
      http_response_code(401);
    exit;
  }

  $file_path = $upload_dir.'/photography_management/'.$f;
  \codeneric\phmm\base\protect_images\Main::provide_file(
    $f,
    $file_path,
    $project_id,
    $part,
  );
}

