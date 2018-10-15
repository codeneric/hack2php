<?php //strict
namespace codeneric\phmm\base\admin;
use \codeneric\phmm\base\includes\Labels;
use \codeneric\phmm\Configuration;
use \codeneric\phmm\base\includes\Client;
use \codeneric\phmm\base\includes\Image;
use \codeneric\phmm\base\includes\Project;
use \codeneric\phmm\base\globals\Superglobals;
use \codeneric\phmm\base\includes\CommentService;
use \codeneric\phmm\base\includes\Error;
use \codeneric\phmm\base\includes\BackgroundProcess;
use \codeneric\phmm\base\includes\ErrorSeverity;
use \codeneric\phmm\Utils;
use \codeneric\phmm\Logger;

class Main {

  private static     $CACHE_resume_photo_upload_dir = null;
  public static function update_database(){
    $config = Configuration::get();
    \codeneric\phmm\DBUpdater::update($config);
  }
  public static function register_client_post_type(){
    $config = Configuration::get();
    $slug = $config['client_post_type'];
    $pluginName = $config['plugin_name'];

    // invariant(false, "%s", new Error("test"));

    register_post_type(
      $slug,
      array(
        'labels' => array(
          'name' => __('Clients', $pluginName),
          'singular_name' => __('Client', $pluginName),
          'add_new' => __('New client', $pluginName),
          'add_new_item' => __('Add new client', $pluginName),
          'edit_item' => __('Edit client', $pluginName),
          // 'new_item' => __('q', $pluginName),
          'view_item' => __('View client', $pluginName),
          // 'view_items' => __('e', $pluginName),
          // 'search_items' => __('r', $pluginName),
          // 'not_found' => __('t', $pluginName),
          // 'not_found_in_trash' => __('z', $pluginName),
          // 'parent_item_colon' => __('u', $pluginName),
          'all_items' => __('Clients', $pluginName),
          // 'archives' => __('o', $pluginName),
          // 'attributes' => __('p', $pluginName),
          // 'insert_into_item' => __('a', $pluginName),
          // 'uploaded_to_this_item' => __('s', $pluginName),
          // 'featured_image' => __('d', $pluginName),
          // 'set_featured_image' => __('f', $pluginName),
          // 'remove_featured_image' => __('g', $pluginName),
          // 'use_featured_image' => __('h', $pluginName),
          'menu_name' => __('Photography Management', $pluginName),
        // 'filter_items_list' => __('k', $pluginName),
        // 'items_list_navigation' => __('l', $pluginName),
        // 'items_list' => __('y', $pluginName),
        // 'name_admin_bar' => __('x', $pluginName),
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'can_export' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-camera',
        'supports' => array('title', 'editor'),
        'rewrite' => array('slug' => $slug, 'with_front' => false),
        'taxonomies' => array(),
      )    );

  }

  public static function register_project_post_type(){
    $config = Configuration::get();
    $slug = $config['project_post_type'];
    $pluginName = $config['plugin_name'];
    register_post_type(
      $slug,
      array(
        'labels' => array(
          'name' => __('Projects', $pluginName),
          'singular_name' => __('Project', $pluginName),
          'add_new' => __('New project', $pluginName),
          'add_new_item' => __('Add new project', $pluginName),
          'edit_item' => __('Edit project', $pluginName),
          // 'new_item' => __('q', $pluginName),
          'view_item' => __('View project', $pluginName),
          // 'view_items' => __('e', $pluginName),
          // 'search_items' => __('r', $pluginName),
          // 'not_found' => __('t', $pluginName),
          // 'not_found_in_trash' => __('z', $pluginName),
          // 'parent_item_colon' => __('u', $pluginName),
          'all_items' => __('Projects', $pluginName),
          // 'archives' => __('o', $pluginName),
          // 'attributes' => __('p', $pluginName),
          // 'insert_into_item' => __('a', $pluginName),
          // 'uploaded_to_this_item' => __('s', $pluginName),
          // 'featured_image' => __('d', $pluginName),
          // 'set_featured_image' => __('f', $pluginName),
          // 'remove_featured_image' => __('g', $pluginName),
          // 'use_featured_image' => __('h', $pluginName),
          'menu_name' => __('Photography Management', $pluginName),
        // 'filter_items_list' => __('k', $pluginName),
        // 'items_list_navigation' => __('l', $pluginName),
        // 'items_list' => __('y', $pluginName),
        // 'name_admin_bar' => __('x', $pluginName),
        ),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'can_export' => true,
        'show_in_admin_bar' => true,
        'show_in_menu' => 'edit.php?post_type='.$config['client_post_type'],
        'has_archive' => false,
        'hierarchical' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array(
          'slug' => $config['project_post_type'],
          'with_front' => false,
        ),
        'taxonomies' => array('category'),
      )    );

  }

  public static function add_client_meta_box(){
    $config = Configuration::get();
    $pluginName = $config['plugin_name'];
    add_meta_box(
      $pluginName.'-client-information',
      __('Client Information', $pluginName),
      array(FrontendHandler::class, 'render_client_information_meta_box'),
      $config['client_post_type'],
      'normal',
      'high'    );
    add_meta_box(
      $pluginName.'-client-project-access',
      __('Project access', $pluginName),
      array(FrontendHandler::class, 'render_client_project_access_meta_box'),
      $config['client_post_type'],
      'normal',
      'high'    );
  }

  public static function add_project_meta_box(){
    $config = Configuration::get();
    remove_meta_box('postimagediv', $config['project_post_type'], 'side');
    $pluginName = $config['plugin_name'];
    add_meta_box(
      $pluginName.'-project-thumbnail',
      __('Cover Image', $pluginName),
      array(FrontendHandler::class, 'render_project_thumbnail_meta_box'),
      $config['project_post_type'],
      'normal',
      'high'    );

    add_meta_box(
      $pluginName.'-project-configuration',
      __('Configuration', $pluginName),
      array(FrontendHandler::class, 'render_project_configuration_meta_box'),
      $config['project_post_type'],
      'normal',
      'high'    );
    add_meta_box(
      $pluginName.'-project-gallery',
      __('Gallery', $pluginName),
      array(FrontendHandler::class, 'render_project_gallery_meta_box'),
      $config['project_post_type'],
      'normal',
      'high'    );
  }

  public static function save_meta_box_data(
$post_id,
$post,
$is_update  ){

    if (!$is_update)
      return false; // dont act when "Add new" is just clicked
    if (defined('DOING_AUTOSAVE') && /* UNSAFE_EXPR */ DOING_AUTOSAVE)
      return false;

    if ('trash' === get_post_status($post_id)) {
      return false;
    }

    $post_type = get_post_type($post_id);
\HH\invariant(      is_string($post_type),
      '%s',
      new Error('Post type not string', [array('post_type', $post_type)]));

    $config = Configuration::get();

    if ($post_type !== $config['client_post_type'] &&
        $post_type !== $config['project_post_type'])
      return false; /* not our business */

    $P = Superglobals::Post();

    if ($post_type === $config['client_post_type']) {
      $data = \codeneric\phmm\validate\client_from_client($P);
      Client::save($post_id, $data);
      return true;
    }

    if ($post_type === $config['project_post_type']) {
      if (!is_null($P['gallery']) && is_string($P['gallery'])) {
        if ($P['gallery'] === '')
          $P['gallery'] = []; else {
          $P['gallery'] = explode(',', (string) $P['gallery']);
        }

      }
      $data = \codeneric\phmm\validate\project_from_admin($P);
      Project::save_project($post_id, $data);
      return true;
    }

    return false;

  }

  // remove project reference in client->project_access when project is permanentely deleted
  public static function cleanup_before_deletion($projectID){
    $projectID = (int) $projectID;
    $post_type = get_post_type($projectID);
    $config = Configuration::get();

    if ($post_type === $config['client_post_type']) {
      $clientID = $projectID;
      $client = Client::get($clientID);
      if (!is_null($client)) {
        $wp_user = $client['wp_user'];
        if (!is_null($wp_user)) {
          \wp_delete_user($wp_user->ID);
        }
      }

      //TODO: rm labels
      //TODO: rm comments
      return;
    }

    if ($post_type !== $config['project_post_type'])
      return;

    $clientIDs = Client::get_all_ids();
    Client::dereference_project($projectID, $clientIDs);

    foreach ($clientIDs as $clientID) {
      //TODO: this might get slow when there are A LOT of clients or the server is the first turing machine from 1936
      Labels::delete_set(
        $clientID,
        $projectID,
        (string) \codeneric\phmm\base\includes\InternalLabelID::Favorites      );
    }

    $settings = Settings::getCurrentSettings();
    if ($settings['remove_images_on_project_deletion']) {

      do_action('codeneric/phmm/delete_images_unique_to_project', $projectID);
    }
  }

  public static function add_custom_image_sizes(){
    // $doit = self::request_comes_from_project_edit_page();
    $doit = true;
    if ($doit) {
      add_image_size(
        Configuration::get()['image_size_fullscreen'],
        2000,
        2000      );
    }

  }

  private static function parse_query(){
    $query_params = array();
    $_server = Superglobals::Server();
    $_get = Superglobals::Get();
    if (array_key_exists('HTTP_REFERER', $_server)) {
      $comps = parse_url($_server['HTTP_REFERER']);
      if (array_key_exists('query', $comps)) {
        parse_str($comps['query'], $query_params);
        return $query_params;
      }
    }
    return [];
  }

  private static function request_comes_from_project_edit_page(){
    $query_params = self::parse_query();

    if (array_key_exists('page', $query_params) &&
        $query_params['page'] === 'options') {
      //media lib...
    }

    $config = Configuration::get();

    $new_post =
      (array_key_exists('post', $query_params) &&
       get_post_type($query_params['post']) === $config['project_post_type']);

    $edit_old_post =
      (array_key_exists('post_type', $query_params) &&
       $query_params['post_type'] === $config['project_post_type']);

    if ($new_post || $edit_old_post) {
      return true;
    }

    return false;
  }

  public static function resume_photo_upload_dir(
$param  ){
    if (!is_null(self::$CACHE_resume_photo_upload_dir))
      return self::$CACHE_resume_photo_upload_dir;

    $config = Configuration::get();
    $query_params = array();
    $_server = Superglobals::Server();
    $_get = Superglobals::Get();
    if (array_key_exists('HTTP_REFERER', $_server)) {
      $comps = parse_url($_server['HTTP_REFERER']);
      if (array_key_exists('query', $comps)) {
        parse_str($comps['query'], $query_params);
        $new_post =
          (array_key_exists('post', $query_params) &&
           get_post_type($query_params['post']) === $config['project_post_type']);
        if (array_key_exists('page', $query_params) &&
            $query_params['page'] === 'options') {
          self::$CACHE_resume_photo_upload_dir = $param;
          return $param;
        }
        $edit_old_post =
          (array_key_exists('post_type', $query_params) &&
           $query_params['post_type'] === $config['project_post_type']);
        if ($new_post || $edit_old_post) {
          if (function_exists('add_image_size')) {
            // add_image_size(
            //   $config['image_size_fullscreen'],
            //   2000,
            //   2000,
            // );
          }
          $mydir = $config['plugin_base_url'].$param['subdir'];
          $param['path'] = $param['basedir'].$mydir;
          $param['url'] = $param['baseurl'].$mydir;
        }
      }
    }
    self::$CACHE_resume_photo_upload_dir = $param;
    return $param;
  }

  private static function download_proofing_csv(
$request  ){
    $clientID = $request['client_id'];
    $projectID = $request['project_id'];

    $proofs = Labels::get_set(
      $clientID,
      $projectID,
      (string) \codeneric\phmm\base\includes\InternalLabelID::Favorites    );
    $data =
      array_map(
        function($imageID) use ($clientID) {

          $image = Image::get_image($imageID);
\HH\invariant(            !is_null($image),
            "%s",
            new Error("Unexpected Image get failure"));
          return array(
            'label_name' => "Proofs",
            'label_id' =>
              (string) \codeneric\phmm\base\includes\InternalLabelID::Favorites,
            'original_filename' => $image['filename'],
            'wordpress_file_id' => $imageID,
            'client_name' => Client::get_name($clientID),
            'client_id' => $clientID,
          );
        },
        $proofs      );

    \codeneric\phmm\base\includes\FileStream::export_label_csv($data);
  }

  public static function provide_csv(){

    $get = Superglobals::Get();

    $codeneric_load_csv =
      array_key_exists('codeneric_load_csv', $get)
        ? $get['codeneric_load_csv']
        : 0;
    if (intval($codeneric_load_csv) == 1) {
      if (!Utils::is_current_user_admin())
        wp_die(
          "Access not permitted",
          "Access not permitted",
          ["response" => 401]        );

      $request = \codeneric\phmm\validate\get_proofing_csv($get);

      self::download_proofing_csv($request);
    }

  }

  public static function mutate_media_modal_query(
$sql  ){
    $x = "hello";
  }
  public static function mutate_media_library_query(
$query  ){
    $_request = Superglobals::Request();
    $projects = Project::get_all_ids();
    $clients = Client::get_all_ids(); //legacy
    $ids = array_merge($projects, $clients);

    // $main = $query->is_main_query();
    $project_edit = self::request_comes_from_project_edit_page();
    if ($project_edit) {
      // $query_params = self::parse_query();
      // if (array_key_exists('post', $query_params)) {
      //   $id = (int) $query_params['post'];
      //   $query["post_parent__in"] = [$id];
      // } else {
      //   $query["post_parent__in"] = [];
      // }
      $query["post_parent__in"] = $ids;

    } else {

      $query["post_parent__not_in"] = $ids;
    }

    return $query;

  }

  public static function update_htaccess(
$old_siteurl,
$new_siteurl  ){
    if (!is_plugin_active('phmm-fast-images/phmm_fast_images.php')) {
      $upload_dir = wp_upload_dir();
      $upload_dir = $upload_dir['basedir'].'/photography_management';
      Photography_Management_Base_Generate_Htaccess(
        "$upload_dir/.htaccess",
        $new_siteurl      );
    }

  }

  public static function add_admin_notice_fast_images_available_for_free(
  ){
    if (!\is_plugin_active('phmm-fast-images/phmm_fast_images.php')) {
      Utils::add_admin_notice(
        '<p><strong>Photography Management</strong>: use the coupon <i>freespeed</i> and get the <strong><a href="https://codeneric.com/shop/phmm-fast-images/">PHMM Fast Images</a></strong> extension for free!</p>',
        'info',
        'fast_images_available_for_free',
        0,
        true      );
    }

  }

  // Force 1 column layout in edit post
  // public static function make_post_edit_one_column(): int {
  //   return 1;
  //   // FrontendHandler::make_post_edit_one_column($x);
  // }
  // public static function screen_layout_columns($columns): mixed {
  //   $columns[Configuration::get()['project_post_type']] = 1;
  //   return $columns;
  // }

}
