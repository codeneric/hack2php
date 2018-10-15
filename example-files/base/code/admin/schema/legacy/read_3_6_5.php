<?hh //strict

namespace codeneric\phmm\legacy\v3_6_5;
use codeneric\phmm\legacy\type as Type;
use codeneric\phmm\base\includes\Error;

function read_client(int $id): Type\client_data_representation_3_6_5 {
  $client = get_post_meta($id, "client", true);
  return
    \codeneric\phmm\legacy\validate\client_data_representation_3_6_5($client);
}

function read_projects(
  int $client_id,
): array<Type\project_data_representation_3_6_5> {
  $projects = get_post_meta($client_id, "projects", true);
  if (!is_array($projects))
    return [];
  $res = [];
  foreach ($projects as $p) {
    if (array_key_exists('thumbnail', $p) && !is_numeric($p['thumbnail']))
      $p['thumbnail'] = 0;

    if (array_key_exists('gallery', $p)) {

      if (is_array($p['gallery'])) {
        $p['gallery'] = array_values($p['gallery']);

      } else if (is_string($p['gallery']) && $p['gallery'] !== "") {
        $p['gallery'] = explode(',', $p['gallery']);

      } else {
        $p['gallery'] = [];
      }

    }

    $res[] =
      \codeneric\phmm\legacy\validate\project_data_representation_3_6_5($p);
  }
  return $res;
}

function read_comments(
  int $image_id,
): array<Type\comment_data_representation_3_6_5> {
  $comments = get_post_meta($image_id, 'codeneric/phmm/comments', false);
  $res = [];
  if (is_array($comments)) {
    foreach ($comments as $c) {
      $res[] =
        \codeneric\phmm\legacy\validate\comment_data_representation_3_6_5($c);
    }

  }

  return $res;
}

function read_plugin_settings(
): Type\plugin_settings_data_representation_3_6_5 {
  $options = get_option('cc_photo_settings', array());

  if (is_array($options) &&
      array_key_exists("cc_photo_portal_page", $options) &&
      !is_numeric($options["cc_photo_portal_page"]))
    $options["cc_photo_portal_page"] = null;

  $res =
    \codeneric\phmm\legacy\validate\plugin_settings_data_representation_3_6_5(
      $options,
    );

  return $res;
}
