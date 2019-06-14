<?php //strict
namespace codeneric\phmm\base\includes;

use \codeneric\phmm\Configuration;
use \codeneric\phmm\Utils;
use \codeneric\phmm\enums\GuestReviewURLParams;
use \codeneric\phmm\enums\GuestReviewDecisionValues;
use \codeneric\phmm\base\includes\Email;


use \codeneric\phmm\Logger as Logger;
// require_once plugin_dir_path(__FILE__).'../includes/image.php';

// enum Properties: string {
//   title =  'title';
//   description = 'description';
//   gallery = 'gallery';
//   is_private = 'is_private';
//   permalink = 'permalink';
//   id = 'id';
//   thumbnail = 'thumbnail';
//   pwd =  'pwd';
//   configuration = 'configuration';
// }

class Client {

  /**
   * Get client by id
   * @param $id - The client id
   */
  public static function get(
$clientID  ){

    if (\get_post_status($clientID) === false)
      return null;

    $projectAccess = self::get_meta_project_access($clientID);

    $internalNotes = self::get_meta_internal_notes($clientID);

    $pwd = self::get_meta_plain_pwd($clientID);

    $user = null;
    if (self::has_client_wp_user($clientID))
      $user = self::get_wp_user_from_client_id($clientID);

    $client = array(
      'ID' => $clientID,
      'wp_user' => $user,
      'project_access' => $projectAccess,
      'internal_notes' => $internalNotes,
      'canned_email_history' => Utils::apply_filter_or(
        "codeneric/phmm/get_canned_email_history",
        $clientID,
        []      ),
      'plain_pwd' => $pwd,
    );

    //Logger::debug("Getting client", $client);

    return $client;
  }

  public static function get_current(){
    $c = self::get_current_id();
    if (\is_null($c))
      return null;
    return self::get($c);
  }

  public static function get_current_id(){
    $current_user = \wp_get_current_user();
    if ($current_user === false)
      return null; //simple...user is not logged in -> dismiss
    return self::get_client_id_from_wp_user_id($current_user->ID);
  }

  private static function update_wp_user(
$wpUserID,
$data  ){
    $plain_pwd = $data['plain_pwd'];
    $userdata = array(
      'display_name' => $data['post_title'],
      'user_email' => $data['email'],
      'user_login' => $data['user_login'],
      'ID' => $wpUserID,
      'user_pass' => \is_null($plain_pwd)
        ? null
        : \wp_hash_password($plain_pwd), // need to hash in on updates
    );

    \wp_insert_user($userdata);
  }
  private static function create_and_get_wp_user(
$post_id,
$data  ){
    //  var_dump($data['plain_pwd']);
    $userdata = array(
      'user_login' => $data['user_login'],
      'user_email' => $data['email'],
      'display_name' => $data['post_title'],
      'role' => Configuration::get()['client_user_role'],
      'show_admin_bar_front' => false,
      'user_pass' => $data['plain_pwd'],
    );
    $userID = \wp_insert_user($userdata);
\HH\invariant(      is_int($userID),
      '%s',
      new Error(
        "Failed to create a user.",
        [array('data', \json_encode($data))]      ));

    // $updated = update_post_meta($post_id, 'wp_user', $userID);

    // invariant(
    //   is_int($updated),
    //   '%s',
    //   new Error("Failed to save wp_user meta to client post"),
    // );
    return $userID;
  }

  public static function typesafe_save(
$ID,
$data  ){

    $data = \codeneric\phmm\validate\client_to_db($data);

    \update_post_meta($ID, 'project_access', $data['project_access']);
    \update_post_meta($ID, 'internal_notes', $data['internal_notes']);
    \update_post_meta($ID, 'plain_pwd', $data['plain_pwd']);
    \update_post_meta($ID, 'wp_user', $data['wp_user']);
  }

  public static function typesafe_project_access_save(
$ID,
$data  ){
    \update_post_meta($ID, 'project_access', $data);
  }

  public static function get_meta_plain_pwd($post_id){
    $mix =
      Utils::get_post_meta_ONLY_USE_IN_HELPER_FUNCTIONS($post_id, "plain_pwd");
    if (is_string($mix))
      return $mix;
    else
      return null;
  }
  public static function get_meta_wp_user($post_id){
    $mix =
      Utils::get_post_meta_ONLY_USE_IN_HELPER_FUNCTIONS($post_id, "wp_user");

    if (!\is_null($mix))
      return (int)$mix;
    else
      return null;
  }
  public static function get_meta_project_access(
$post_id  ){
    $mix = Utils::get_post_meta_ONLY_USE_IN_HELPER_FUNCTIONS(
      $post_id,
      "project_access"    );
    if (is_array($mix)) {
      return /*HH_IGNORE_ERROR[4110]*/\array_map(
        function($e) {
          return \codeneric\phmm\validate\client_project_access($e);
        },
        $mix      );
    } else
      return [];
  }
  public static function get_meta_internal_notes($post_id){
    $mix = Utils::get_post_meta_ONLY_USE_IN_HELPER_FUNCTIONS(
      $post_id,
      "internal_notes"    );
    if (is_string($mix))
      return $mix;
    else
      return null;
  }

  public static function save(
$post_id,
$data  ){

    $pwd = null;

    $plain_pwd = $data['plain_pwd'];

    $oldPwd = self::get_meta_plain_pwd($post_id);
    if (\is_null($plain_pwd)) {
      if (\is_null($oldPwd)) {
        $pwd = \wp_generate_password(10);
      } else {
        $pwd = $oldPwd;
      }

    } else {
      $pwd = $plain_pwd;
    }

    $data['plain_pwd'] = $pwd;
    $wp_user_id = self::get_client_wp_user_id($post_id);

    // already has a wp_user assigned
    if (is_int($wp_user_id)) {
      self::update_wp_user($wp_user_id, $data);
    } // new user has to be created
    else {
      $wp_user_id = self::create_and_get_wp_user($post_id, $data);
    }

    self::typesafe_save(
      $post_id,
      array(
        "project_access" => $data['project_access'],
        "wp_user" => $wp_user_id,
        "internal_notes" => $data['internal_notes'],
        'plain_pwd' => $data['plain_pwd'],
      )    );

  }


  public static function create_guest_without_project_access(
$data  ){


    $userdata = array(
      'user_login' => $data['user_login'],
      'user_email' => $data['user_email'],
      'display_name' => $data['first_name'].' '.$data['last_name'],
      'role' => Configuration::get()['guest_user_role'],
      'show_admin_bar_front' => false,
      'user_pass' => $data['user_pass'],
    );
    $wp_user_id = \wp_insert_user($userdata);

    if ($wp_user_id instanceof \WP_Error) {
      $msgs = $wp_user_id->get_error_messages();
      $str_msgs = array();
\HH\invariant(        is_array($msgs),
        '%s',
        new Error('Method get_error_messages did not returned array!'));

      foreach ($msgs as $k => $m) {
\HH\invariant(          is_string($m),
          '%s',
          new Error('Error message is not string!'));
        $str_msgs[] = $m;
      }
      return array(
        'success' => false,
        'error_messages' => $str_msgs,
        'client_id' => null,
      );
    }
\HH\invariant(      is_int($wp_user_id),
      '%s',
      new Error('wp_user_id is not int! But it has to be -> WP core error!'));

    $projects = Project::get_project_titles_by_registration_code($data['code']);
    $project_access = array();
    $guest_status = 'publish';
    $manager_email = null;
    foreach ($projects as $p) {
      $prot = Project::get_protection($p['id']);
      $r = $prot['registration'];
      if (!\is_null($r)) {
        $approved = true;
        if (!\is_null($r['manager_email'])) {
          $guest_status = 'pending';
          $approved = false;
          $manager_email = $r['manager_email'];
        }
        $project_access[] = array(
          'active' => $approved,
          'configuration' => null,
          'id' => $p['id'],
        );
      }

    }


    $post_id = (int)\wp_insert_post(
      array(
        'post_title' => $data['first_name'].' '.$data['last_name'],
        'post_type' => Configuration::get()['client_post_type'],
        'post_status' => $guest_status,
      ),
      true    );

    if ($post_id instanceof \WP_Error) {
      $cleanup_wp_user =
        \wp_delete_user($wp_user_id); //clean up the dengling wp_user!
      $str_msgs = array();
      $msgs = $post_id->get_error_messages();
\HH\invariant(        is_array($msgs),
        '%s',
        new Error('Method get_error_messages did not returned array!'));

      foreach ($msgs as $k => $m) {
\HH\invariant(          is_string($m),
          '%s',
          new Error('Error message is not string!'));
        $str_msgs[] = $m;
      }
      if (!$cleanup_wp_user) {
        $str_msgs[] = 'PHMM: clean up the dengling wp_user failed.';
      }
      return array(
        'success' => false,
        'error_messages' => $str_msgs,
        'client_id' => null,
      );

    }
\HH\invariant(      is_int($post_id),
      '%s',
      new Error('post_id is not int! But it has to be -> WP core error!'));


    self::typesafe_save(
      $post_id,
      array(
        "project_access" => $project_access,
        "wp_user" => $wp_user_id,
        "internal_notes" => null,
        'plain_pwd' => $data['user_pass'],
      )    );

    self::set_used_registration_code($post_id, $data['code']);
    self::set_random_review_permission_secret($post_id);

    if (\is_string($manager_email)) {
      //TODO: send mail to $manager_email!
      $to = $manager_email;
      $subject = \__('Please review this sign up:').
        " {$data['first_name']} {$data['last_name']}";
      $accept_link = self::get_guest_review_link(
        $post_id,
        GuestReviewDecisionValues::Accept      );
      $decline_link = self::get_guest_review_link(
        $post_id,
        GuestReviewDecisionValues::Decline      );
      // $body =
      //   "Hi,\nshould {$data['first_name']} {$data['last_name']} get access to your photos?\n 
      //   Accpet: {$accept_link}\n
      //   Decline: {$decline_link}\n
      //   ";
      $body = \__(
        "Hi,\nshould %s get access to your photos?\n
        Accpet: %s\n
        Decline: %s\n
        "      );

      $body = (string)/*UNSAFE_EXPR*/\sprintf(
        $body,
        "{$data['first_name']} {$data['last_name']}",
        $accept_link,
        $decline_link      );


      // $headers = ['From: "Denis Photo" <denis@golovin.de>'];
      // $headers = array('Content-Type: text/html; charset=UTF-8'); 

      $success = Email::send(
        array(
          "to" => [$to],
          "subject" => $subject,
          "message" => $body,
          "headers" => "",
          "attachments" => [],
        )      );

    }

    return array(
      'success' => true,
      'error_messages' => array(),
      'client_id' => $post_id,
    );

  }

  public static function set_used_registration_code(
$guest_id,
$code  ){
    \update_post_meta($guest_id, 'used_registration_code', $code);
  }

  public static function set_random_review_permission_secret(
$guest_id  ){
    $secret = \wp_generate_password(10, false);
    \update_post_meta($guest_id, 'random_review_permission_secret', $secret);
  }


  public static function get_random_review_permission_secret(
$guest_id  ){
    $secret = Utils::get_post_meta_ONLY_USE_IN_HELPER_FUNCTIONS(
      $guest_id,
      "random_review_permission_secret"    );
    if (\is_string($secret)) return $secret;
    return null;
  }

  public static function get_guest_review_link(
$client_id,
$decision  ){
    $secret = self::get_random_review_permission_secret($client_id);
    $permalink = \get_permalink($client_id);
    if (\is_string($secret) && \is_string($permalink)) {
      $dk = (string)GuestReviewURLParams::DecisionKey;
      $sk = (string)GuestReviewURLParams::SecretKey;
      $link = \add_query_arg(
        array(
          $dk => (string)$decision,
          $sk => (string)$secret,
        ),
        $permalink      );
      return $link;
    }
    return null;
  }

  public static function accept_guest_registration($client_id){

    if (self::is_pending_review($client_id) && self::is_guest($client_id)) {
      $project_access = self::get_meta_project_access($client_id);
      foreach ($project_access as $key => $a) {
        $project_access[$key]['active'] = true; 
      }
      self::typesafe_project_access_save($client_id, $project_access); 
      \wp_update_post(array(
        'ID' => $client_id,
        'post_status' => 'publish',
      ));
    }
  }

  public static function is_guest($id){
    $wp_user_id = self::get_client_wp_user_id($id);
    if (\is_null($wp_user_id)) return false;
    $user_meta = \get_userdata($wp_user_id);
    if ($user_meta instanceof \WP_User) {
      $user_roles = $user_meta->roles;
      return \in_array(Configuration::get()['guest_user_role'], $user_roles);

    }
    return false;
  }

  public static function is_pending_review($id){
    $status = \get_post_status($id);
    return $status === 'pending';
  }


  public static function has_client_wp_user($clientID){
    $id = self::get_meta_wp_user($clientID);

    return !\is_null($id);
  }
  public static function get_client_wp_user_id($clientID){
    return self::get_meta_wp_user($clientID);

  }

  public static function get_client_id_from_wp_user_id($userID){
    $clients = self::get_all_clients();
    $clientID = null;
    foreach ($clients as $client) {
      $uid = self::get_client_wp_user_id($client->ID);

      if ($uid === $userID)
        $clientID = $client->ID;
    }

    return $clientID;
  }
  public static function get_wp_user_from_client_id($clientID){
    $id = self::get_client_wp_user_id($clientID);

    if (!is_int($id))
      return null;

    $user = \get_user_by('ID', $id);

    if ($user instanceof \WP_User)
      return $user;

    return null;

  }

  public static function get_project_ids($clientID){
    if ($clientID === 0) {
      $project_ids_with_guest_access = [];
      $project_ids = Project::get_all_ids();
      foreach ($project_ids as $id) {
        $protec = Project::get_protection($id);
        if (!\is_null($protec['password']) || !$protec['private']) {
          $project_ids_with_guest_access[] = $id;
        }
      }
      return $project_ids_with_guest_access;
    }
    $projects = self::get_meta_project_access($clientID);

    if (is_array($projects)) {
      $map = function($project) {
\HH\invariant(          \array_key_exists('id', $project),
          '%s',
          new Error("Project access shape different than expected"));

        return $project['id'];
      };

      return \array_values(\array_map($map, $projects));
    }
    return array();
  }

  /*
   * Gets all projects assigned to a client.
   */
  public static function get_project_wp_posts(
$clientID,
$filterActive = false  ){
    $projects = self::get_meta_project_access($clientID);
\HH\invariant(      is_array($projects),
      '%s',
      new Error('get project_access meta expected to be array'));

    if ($filterActive) {
      $projects = \array_values(
        \array_filter(
          $projects,
          function($project) {
            return $project['active'] === true;
          }        )      );
      // TODO: also filter projects which only have wp status === published

    }

    $map = function($project) {
      $post = \get_post($project['id']);
\HH\invariant(        $post instanceof \WP_Post,
        '%s',
        new Error("Could not get project post by id"));

      return $post;

    };

    $posts = \array_map($map, $projects);

    return /*HH_IGNORE_ERROR[4110]*/$posts;
  }

  public static function get_project_configuration(
$clientID,
$projectID  ){
    $accesses = self::get_meta_project_access($clientID);

    foreach ($accesses as $i => $a) {
      if ($a['id'] === $projectID) {
        if (!\is_null($a['configuration']))
          return $a['configuration'];
        else
          return Project::get_configuration($projectID);
      }
    }
    return null;
  }

  public static function get_all_ids(){
    $clientIDs = \get_posts(
      array(
        'post_type' => Configuration::get()['client_post_type'],
        'post_status' => 'any',
        'numberposts' => -1, // 'order'    => 'ASC'
        'fields' => 'ids',
      )    );
\HH\invariant(      is_array($clientIDs),
      '%s',
      new Error('Expected array getting client IDs'));

    return /*HH_IGNORE_ERROR[4110]*/$clientIDs;
  }
  public static function get_all_clients(){
    $clients = \get_posts(
      array(
        'post_type' => Configuration::get()['client_post_type'],
        'post_status' => 'any',
        'numberposts' => -1, // 'order'    => 'ASC'
        'post_parent' => null,
      )    );
\HH\invariant(      is_array($clients),
      '%s',
      new Error('Expected array getting clients'));

    return /*HH_IGNORE_ERROR[4110]*/$clients;
  }

  public static function get_all_labels_from_client(
$clientID  ){
    $projectIDs = Client::get_project_ids($clientID);
    $labels = [];
    foreach ($projectIDs as $projectID) {

      $labels[] = array(
        'client_id' => $clientID,
        'project_id' => $projectID,
        'label_id' => (string)InternalLabelID::Favorites,
        'labels' => Labels::get_set(
          $clientID,
          $projectID,
          (string)InternalLabelID::Favorites        ),
      );
    }

    return $labels;
  }

  /**
   * Checks if given client has access to given project
   */
  public static function has_access_to_project(
$clientID,
$projectID  ){

    $protection = Project::get_protection($projectID);
    if ($protection['private'] === false)
      return true;

    $project_access = self::get_meta_project_access($clientID);
    // $projects = self::get_project_ids($clientID);

    // $has_access = false;

    // foreach ($projects as $project) {

    //   if ($project === $projectID)
    //     $has_access = true;
    // }

    foreach ($project_access as $access) {

      if ($access['id'] === $projectID) {
        return $access['active'];
      }

    }
    return false;
  }

  /**
   * Takes a project ID, fetches all available clients by ID; iterate over all IDs; gets the meta data project_access and filters out every occurence of $projectID
   * @param $projectID - The ID of the project that should be dereferenced in all clients
   */
  public static function dereference_project(
$projectID,
$clientIDs  ){
    // we do this so we do not fetch all client ids again, if the caller of this function already has the list
    if ($clientIDs === null)
      $clientIDs = self::get_all_ids();
    foreach ($clientIDs as $clientID) {
      $access = self::get_meta_project_access($clientID);
\HH\invariant(        is_array($access),
        '%s',
        new Error('project_access shape not as expected'));

      $filter = function($acc) use ($projectID) {
\HH\invariant(          is_array($acc),
          '%s',
          new Error('project_access shape not as expected'));

        if ($acc['id'] === $projectID) {
          return false;
        }

        return true;
      };

      $cleanAccess = \array_filter($access, $filter);

      \update_post_meta($clientID, 'project_access', $cleanAccess);

    }
  }

  public static function get_name($clientID){
    return $clientID !== 0 ? \get_the_title($clientID) : 'Guest';
  }


  public static function delete_client_by_wp_user_id($userID){

    $postID = self::get_client_id_from_wp_user_id($userID);
    if (\is_null($postID))
      return;
    \wp_delete_post($postID, true);
  }

  public static function trash_client(
$client_id,
$force = false  ){
    \wp_trash_post($client_id);
  }

  public static function ACTION_status_transition(
$new_status,
$old_status,
$post  ){

    if ($post->post_type === Configuration::get()['client_post_type']) {
      if ($old_status === 'pending' && $new_status === 'publish') {
        // A function to perform actions any time any post changes status.
      }
    }

  }
}
