<?hh //strict

namespace codeneric\phmm\base\includes;

use codeneric\phmm\Configuration;
use codeneric\phmm\Utils;

use codeneric\phmm\Logger as Logger;
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
  const phmm_user_role = "phmm_client";

  /**
   * Get client by id
   * @param $id - The client id
   */
  public static function get(
    int $clientID,
  ): ?\codeneric\phmm\type\client\Populated {

    if (get_post_status($clientID) === false)
      return null;

    $projectAccess = self::get_meta_project_access($clientID);

    $internalNotes = self::get_meta_internal_notes($clientID);

    $pwd = self::get_meta_plain_pwd($clientID);

    $user = null;
    if (self::has_client_wp_user($clientID))
      $user = self::get_wp_user_from_client_id($clientID);

    $client = shape(
      'ID' => $clientID,
      'wp_user' => $user,
      'project_access' => $projectAccess,
      'internal_notes' => $internalNotes,
      'canned_email_history' => Utils::apply_filter_or(
        "codeneric/phmm/get_canned_email_history",
        $clientID,
        [],
      ),
      'plain_pwd' => $pwd,
    );

    //Logger::debug("Getting client", $client);

    return $client;
  }

  public static function get_current(
  ): ?\codeneric\phmm\type\client\Populated {
    $current_user = wp_get_current_user();
    if ($current_user === false)
      return null; //simple...user is not logged in -> dismiss
    $c = self::get_client_id_from_wp_user_id($current_user->ID);
    if (is_null($c))
      return null;
    return self::get($c);
  }

  private static function update_wp_user(
    int $wpUserID,
    \codeneric\phmm\type\client_from_client $data,
  ): void {
    $userdata = array(
      'display_name' => $data['post_title'],
      'user_email' => $data['email'],
      'user_login' => $data['user_login'],
      'ID' => $wpUserID,
      'user_pass' =>
        is_null($data['plain_pwd'])
          ? null
          : wp_hash_password($data['plain_pwd']), // need to hash in on updates
    );

    wp_insert_user($userdata);
  }
  private static function create_and_get_wp_user(
    int $post_id,
    \codeneric\phmm\type\client_from_client $data,
  ): int {
    //  var_dump($data['plain_pwd']);
    $userdata = array(
      'user_login' => $data['user_login'],
      'user_email' => $data['email'],
      'display_name' => $data['post_title'],
      'role' => Configuration::get()['client_user_role'],
      'show_admin_bar_front' => false,
      'user_pass' => $data['plain_pwd'],
    );
    $userID = wp_insert_user($userdata);
    invariant(
      is_int($userID),
      '%s',
      new Error(
        "Failed to create a user.",
        [tuple('data', json_encode($data))],
      ),
    );

    // $updated = update_post_meta($post_id, 'wp_user', $userID);

    // invariant(
    //   is_int($updated),
    //   '%s',
    //   new Error("Failed to save wp_user meta to client post"),
    // );
    return $userID;
  }

  public static function typesafe_save(
    int $ID,
    \codeneric\phmm\type\client_to_db $data,
  ): void {

    $data = \codeneric\phmm\validate\client_to_db($data);

    update_post_meta($ID, 'project_access', $data['project_access']);
    update_post_meta($ID, 'internal_notes', $data['internal_notes']);
    update_post_meta($ID, 'plain_pwd', $data['plain_pwd']);
    update_post_meta($ID, 'wp_user', $data['wp_user']);
  }

  public static function get_meta_plain_pwd(int $post_id): ?string {
    $mix = Utils::get_post_meta_ONLY_USE_IN_HELPER_FUNCTIONS(
      $post_id,
      "plain_pwd",
    );
    if (is_string($mix))
      return $mix; else
      return null;
  }
  public static function get_meta_wp_user(int $post_id): ?int {
    $mix =
      Utils::get_post_meta_ONLY_USE_IN_HELPER_FUNCTIONS($post_id, "wp_user");

    if (!is_null($mix))
      return (int) $mix; else
      return null;
  }
  public static function get_meta_project_access(
    int $post_id,
  ): array<\codeneric\phmm\type\client_project_access> {
    $mix = Utils::get_post_meta_ONLY_USE_IN_HELPER_FUNCTIONS(
      $post_id,
      "project_access",
    );
    if (is_array($mix)) {
      return array_map(
        function($e) {
          return \codeneric\phmm\validate\client_project_access($e);
        },
        $mix,
      );
    } else
      return [];
  }
  public static function get_meta_internal_notes(int $post_id): ?string {
    $mix = Utils::get_post_meta_ONLY_USE_IN_HELPER_FUNCTIONS(
      $post_id,
      "internal_notes",
    );
    if (is_string($mix))
      return $mix; else
      return null;
  }

  public static function save(
    int $post_id,
    \codeneric\phmm\type\client_from_client $data,
  ): void {

    $pwd = null;

    $plain_pwd = $data['plain_pwd'];

    $oldPwd = self::get_meta_plain_pwd($post_id);
    if (is_null($plain_pwd)) {
      if (is_null($oldPwd)) {
        $pwd = wp_generate_password(10);
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
      shape(
        "project_access" => $data['project_access'],
        "wp_user" => $wp_user_id,
        "internal_notes" => $data['internal_notes'],
        'plain_pwd' => $data['plain_pwd'],
      ),
    );

  }

  public static function has_client_wp_user(int $clientID): bool {
    $id = self::get_meta_wp_user($clientID);

    return !is_null($id);
  }
  public static function get_client_wp_user_id(int $clientID): ?int {
    return self::get_meta_wp_user($clientID);

  }

  public static function get_client_id_from_wp_user_id(int $userID): ?int {
    $clients = self::get_all_clients();
    $clientID = null;
    foreach ($clients as $client) {
      $uid = self::get_client_wp_user_id($client->ID);

      if ($uid === $userID)
        $clientID = $client->ID;
    }

    return $clientID;
  }
  public static function get_wp_user_from_client_id(int $clientID): ?\WP_User {
    $id = self::get_client_wp_user_id($clientID);

    if (!is_int($id))
      return null;

    $user = get_user_by('ID', $id);

    if ($user instanceof \WP_User)
      return $user;

    return null;

  }

  public static function get_project_ids(int $clientID): array<int> {
    if ($clientID === 0) {
      $project_ids_with_guest_access = [];
      $project_ids = Project::get_all_ids();
      foreach ($project_ids as $id) {
        $protec = Project::get_protection($id);
        if (!is_null($protec['password']) || !$protec['private']) {
          $project_ids_with_guest_access[] = $id;
        }
      }
      return $project_ids_with_guest_access;
    }
    $projects = self::get_meta_project_access($clientID);

    if (is_array($projects)) {
      $map = function($project) {
        invariant(
          array_key_exists('id', $project),
          '%s',
          new Error("Project access shape different than expected"),
        );

        return $project['id'];
      };

      return array_values(array_map($map, $projects));
    }
    return array();
  }

  /*
   * Gets all projects assigned to a client.
   */
  public static function get_project_wp_posts(
    int $clientID,
    bool $filterActive = false,
  ): array<\WP_Post> {
    $projects = self::get_meta_project_access($clientID);

    invariant(
      is_array($projects),
      '%s',
      new Error('get project_access meta expected to be array'),
    );

    if ($filterActive) {
      $projects = array_values(
        array_filter(
          $projects,
          function($project) {
            return $project['active'] === true;
          },
        ),
      );
      // TODO: also filter projects which only have wp status === published

    }

    $map = function($project) {
      $post = get_post($project['id']);
      invariant(
        $post instanceof \WP_Post,
        '%s',
        new Error("Could not get project post by id"),
      );

      return $post;

    };

    $posts = array_map($map, $projects);

    return $posts;
  }

  public static function get_project_configuration(
    int $clientID,
    int $projectID,
  ): ?\codeneric\phmm\type\configuration {
    $accesses = self::get_meta_project_access($clientID);

    foreach ($accesses as $i => $a) {
      if ($a['id'] === $projectID) {
        if (!is_null($a['configuration']))
          return $a['configuration']; else
          return Project::get_configuration($projectID);
      }
    }
    return null;
  }

  public static function get_all_ids(): array<int> {
    $clientIDs = get_posts(
      array(
        'post_type' => Configuration::get()['client_post_type'],
        'post_status' => 'any',
        'numberposts' => -1, // 'order'    => 'ASC'
        'fields' => 'ids',
      ),
    );

    invariant(
      is_array($clientIDs),
      '%s',
      new Error('Expected array getting client IDs'),
    );

    return $clientIDs;
  }
  public static function get_all_clients(): array<\WP_Post> {
    $clients = get_posts(
      array(
        'post_type' => Configuration::get()['client_post_type'],
        'post_status' => 'any',
        'numberposts' => -1, // 'order'    => 'ASC'
        'post_parent' => null,
      ),
    );

    invariant(
      is_array($clients),
      '%s',
      new Error('Expected array getting clients'),
    );

    return $clients;
  }

  public static function get_all_labels_from_client(
    int $clientID,
  ): array<shape(
    'client_id' => int,
    'project_id' => int,
    'label_id' => string,
    'labels' => array<int>,
  )> {
    $projectIDs = Client::get_project_ids($clientID);
    $labels = [];
    foreach ($projectIDs as $projectID) {

      $labels[] = shape(
        'client_id' => $clientID,
        'project_id' => $projectID,
        'label_id' => (string) InternalLabelID::Favorites,
        'labels' => Labels::get_set(
          $clientID,
          $projectID,
          (string) InternalLabelID::Favorites,
        ),
      );
    }

    return $labels;
  }

  /**
   * Checks if given client has access to given project
   */
  public static function has_access_to_project(
    int $clientID,
    int $projectID,
  ): bool {

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
    int $projectID,
    ?array<int> $clientIDs,
  ): void {
    // we do this so we do not fetch all client ids again, if the caller of this function already has the list
    if ($clientIDs == null)
      $clientIDs = self::get_all_ids();
    foreach ($clientIDs as $clientID) {
      $access = self::get_meta_project_access($clientID);
      invariant(
        is_array($access),
        '%s',
        new Error('project_access shape not as expected'),
      );

      $filter = function($acc) use ($projectID) {
        invariant(
          is_array($acc),
          '%s',
          new Error('project_access shape not as expected'),
        );

        if ($acc['id'] === $projectID) {
          return false;
        }

        return true;
      };

      $cleanAccess = array_filter($access, $filter);

      update_post_meta($clientID, 'project_access', $cleanAccess);

    }
  }

  public static function get_name(int $clientID): string {
    return $clientID !== 0 ? get_the_title($clientID) : 'Guest';
  }

  // public static function get_canned_email_history
  // public function save(
  //   int $post_id,
  //   array<string, string> $post,
  //   bool $is_update,
  // ): void {
  //   invariant(is_array($post), 'Expected $post of type Array');
  //   invariant(is_bool($is_update), "Expected is_update to be bool");

  //   $x = $this->get(1);

  //   $CLIENT = $post['client'];
  //   $CLIENT = $post;

  //   if ($is_update) {
  //   } else {
  //     // new post

  //   }

  //   $current_client =
  //     Photography_Management_Base_Client::get_client($post_id);

  //   if ($CLIENT['pwd'] !== "") {
  //     if (array_key_exists('login_name', $CLIENT)) {
  //       $login_name = sanitize_user($CLIENT['login_name']);

  //     } else {
  //       $login_name = sanitize_text_field($CLIENT['full_name']);
  //     }
  //     $email = sanitize_email($CLIENT['email']);
  //     // $email = isset($CLIENT['email']) ? $CLIENT['email'] : '';
  //     // if (!empty($email)) {
  //     //   $email = sanitize_email($email);
  //     // }

  //     $userdata = array(
  //       'user_login' => $login_name,
  //       //'user_pass'   =>  $CLIENT['pwd'],
  //       'role' => self::phmm_user_role,
  //       'user_email' => $email,
  //       'display_name' => $login_name,
  //       'nickname' => $login_name,
  //       'user_nicename' => $login_name,
  //       'show_admin_bar_front' => false,
  //     );

  //     if (!isset($current_client['pwd']) ||
  //         ($CLIENT['pwd'] !== $current_client['pwd'])) //only update pass if needed
  //       $userdata['user_pass'] = $CLIENT['pwd'];

  //     if (isset($current_client['wp_user_id'])) {

  //       $userdata['ID'] = $current_client['wp_user_id'];
  //       $user_id = wp_update_user($userdata);

  //     } else {

  //       $user_id = wp_insert_user($userdata);
  //       update_user_meta($user_id, 'is_phmm_client', true);
  //       update_user_meta($user_id, 'phmm_post_id', $post_id);
  //     }

  //     if (is_numeric($user_id)) {
  //       $CLIENT['wp_user_id'] = $user_id;
  //     }

  //     //TODO: cant this be done on wp_delete_post hook? So WP user is deleted when the post is deleted
  //   } else if (isset($current_client['wp_user_id'])) {
  //     wp_delete_user($current_client['wp_user_id']);
  //   }

  //   //        update_post_meta($post_id, 'client', $CLIENT);
  //   Photography_Management_Base_Client::update_client($post_id, $CLIENT);

  // }

  public static function delete_client(int $userID): void {

    $postID = self::get_client_id_from_wp_user_id($userID);
    if (is_null($postID))
      return;
    wp_delete_post($postID, true);
  }
}
