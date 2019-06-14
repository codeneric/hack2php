<?php //strict
namespace codeneric\phmm\legacy\v4_0_0;
use \codeneric\phmm\legacy\type;
use \codeneric\phmm\base\includes\Error;

//project

function save_project(
$id,
$data){

  // update_post_meta($id, 'version', $data['version']);

  \update_post_meta($id, 'gallery', $data['gallery']);
  \update_post_meta($id, 'protection', $data['protection']);

  \update_post_meta($id, "post_password", $data['protection']['password']);

  if (!\is_null($data['thumbnail'])) // optional field
    \update_post_meta($id, 'thumbnail', $data['thumbnail']);

  // the $POST has no thumbnail and we have one in storage, that means we have to delete our stored reference
  if (\is_null($data['thumbnail']) && !\is_null(get_thumbnail_meta($id)))
    \delete_post_meta($id, 'thumbnail');

  // update_post_meta($id, 'is_private', $project['is_private']);

  \update_post_meta($id, 'configuration', $data['configuration']);
}

function get_thumbnail_meta($id){
  $raw = \get_post_meta($id, 'thumbnail', true);

  if (is_string($raw) && $raw !== "")
    return (int)$raw;

  return null;
}

//client

function save_client(
$post_id,
$data){

  \update_post_meta($post_id, 'project_access', $data['project_access']);
  \update_post_meta($post_id, 'internal_notes', $data['internal_notes']);

  if (
    \is_null($data['plain_pwd']) &&
    \get_post_meta($post_id, 'plain_pwd', true) === ""
  )
    $data['plain_pwd'] = \wp_generate_password(10);

  if (!\is_null($data['plain_pwd'])) // only update if actively set or generated
    \update_post_meta($post_id, 'plain_pwd', $data['plain_pwd']);

  $wp_user = get_client_wp_user_id($post_id);

  // already has a wp_user assigned
  if (is_int($wp_user)) {
    update_wp_user($wp_user, $data);
  } // new user has to be created
  else {
    create_and_save_wp_user($post_id, $data);
  }

}

function get_client_wp_user_id($clientID){
  $id = \get_post_meta($clientID, 'wp_user', true);

  if ($id === "")
    return null;

  return (int)$id;
}

function update_wp_user(
$wpUserID,
$data){
  $plain_pwd = $data['plain_pwd'];
  $userdata = array(
    'display_name' => $data['post_title'],
    'user_email' => $data['email'],
    'user_login' => $data['user_login'],
    'ID' => $wpUserID,
    'user_pass' => \is_null($plain_pwd) ? null : \wp_hash_password($plain_pwd),
    // need to hash in on updates   
  );

  \wp_insert_user($userdata);
}

function create_and_save_wp_user(
$post_id,
$data){
  //  var_dump($data['plain_pwd']);
  $userdata = array(
    'user_login' => $data['user_login'],
    'user_email' => $data['email'],
    'display_name' => $data['post_title'],
    'role' => "phmm_client",
    'show_admin_bar_front' => false,
    'user_pass' => $data['plain_pwd'],
  );
  $userID = \wp_insert_user($userdata);
\HH\invariant(    is_int($userID),
    '%s',
    new Error("Failed to create a user.", [array('data', \json_encode($data))]));

  $updated = \update_post_meta($post_id, 'wp_user', $userID);
\HH\invariant(    is_int($updated),
    '%s',
    new Error("Failed to save wp_user meta to client post"));
  return $userID;
}

//comment

function save_comment($comment){
  // init res, hack compiler demands it
  $res = 0;

  // get the current date and time
  // format it into '0000-00-00 00:00:00'
  $current_date = \date("Y-m-d H:i:s");

  // call wpdb and insert row
  $wpdb = \codeneric\phmm\base\globals\Superglobals::Globals('wpdb');
\HH\invariant(    $wpdb instanceof \wpdb,
    '%s',
    new Error('Can not get global wpdb object!'));

  $res = $wpdb->insert(
    "codeneric_phmm_comments",
    array(
      'time' => $current_date,
      'content' => $comment['content'],
      'project_id' => $comment['project_id'],
      'attachment_id' => $comment['attachment_id'],
      'wp_user_id' => $comment['wp_user_id'],
      'client_id' => $comment['client_id'],
      'wp_author_id' => $comment['wp_author_id'],
    )  );
  // res is 1 (number of rows inserted in db) if successful
  // false otherwise
  if (is_int($res) && $res === 1) {
    return true;
  } else {
    return false;
  }
}

//lables

function save_lable_set(
$clientID,
$projectID,
$imageIDs,
$labelID = "1111111111111"){
\HH\invariant(    is_array($imageIDs),
    '%s',
    new Error('Expected labels to be of type array.'));

  // check if labelID exists
  $optionName = get_option_name($clientID, $projectID, $labelID);

  return \update_option($optionName, $imageIDs);
}

function get_option_name(
$clientID,
$projectID,
$labelID){
\HH\invariant(is_int($clientID), '%s', new Error("clientID must be int"));
\HH\invariant(is_int($projectID), '%s', new Error("projectID must be int"));
\HH\invariant(is_string($labelID), '%s', new Error("labelID must be string"));
\HH\invariant($labelID !== "", '%s', new Error("labelID cannot be empty string"));

  $hash = \md5("$clientID/$projectID/$labelID");

  return "codeneric/phmm/labels/$hash";
}
