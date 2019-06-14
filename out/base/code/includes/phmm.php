<?php //strict
namespace codeneric\phmm\base\includes;
use \codeneric\phmm\base\includes\Permission;
use \codeneric\phmm\base\includes\Error;
use \codeneric\phmm\base\includes\ErrorSeverity;

use \codeneric\phmm\Logger as Logger;
require_once plugin_dir_path(dirname(__FILE__)).'includes/requires.php';

/**
 * The class responsible for orchestrating the actions and filters of the
 * core plugin.
 */
;

class Phmm {

  protected $loader;

  protected $handler;

  public function __construct() {

    $this->load_dependencies();
    $this->set_locale();
    $this->define_admin_hooks();
    $this->define_public_hooks();

    $this->set_error_handler();
  }

  private function set_error_handler(){
    $callback = Error::class.'::handle_error_case';

    \HH\invariant_callback_register($callback);
  }

  private function load_dependencies(){
    $this->loader = new Loader();
    $this->loader->add_action(
      'codeneric/phmm/check_user_permission',
      Permission::class,
      'current_user_can_access_client'    );

  }

  private function set_locale(){
    $plugin_i18n = new i18n();

    $this->loader
      ->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

  }

  private function define_admin_hooks(){

    $plugin_admin = \codeneric\phmm\base\admin\Main::class;

    $ajax = \codeneric\phmm\base\admin\ajax\Endpoints::class;
    $frontendHandler = \codeneric\phmm\base\admin\FrontendHandler::class;
    $settings = \codeneric\phmm\base\admin\Settings::class;

    $config = \codeneric\phmm\Configuration::get();

    \codeneric\phmm\base\admin\Main::add_custom_image_sizes();

    $this->loader
      ->add_action('wp_ajax_phmm_star_photo', $ajax, 'label_images');
    $this->loader->add_action(
      'wp_ajax_phmm_dismiss_notice',
      $ajax,
      'dismiss_admin_notice'    );

    $this->loader
      ->add_action('wp_ajax_phmm_send_feedback', $ajax, 'send_feedback');
    $this->loader
      ->add_action('wp_ajax_nopriv_phmm_star_photo', $ajax, 'label_images');

    $this->loader->add_action(
      'wp_ajax_phmm_number_of_zip_parts',
      $ajax,
      'get_download_zip_parts'    );
    $this->loader->add_action(
      'wp_ajax_nopriv_phmm_number_of_zip_parts',
      $ajax,
      'get_download_zip_parts'    );
    $this->loader->add_action(
      'wp_ajax_phmm_fetch_images',
      $ajax,
      'fetch_gallery_images'    );
    $this->loader->add_action(
      'wp_ajax_nopriv_phmm_fetch_images',
      $ajax,
      'fetch_gallery_images'    );
    $this->loader
      ->add_action('wp_ajax_phmm_label_image', $ajax, 'label_images');

    $this->loader
      ->add_action('wp_ajax_phmm_check_username', $ajax, 'check_username');

    $this->loader->add_action(
      'wp_ajax_analytics_opt_in_allow',
      $ajax,
      'analytics_opt_in_allow'    );

    $this->loader
      ->add_action('wp_ajax_phmm_check_email', $ajax, 'check_email');
    $this->loader->add_action(
      'wp_ajax_phmm_get_interactions',
      $ajax,
      'get_interactions'    );
    $this->loader->add_action(
      'wp_ajax_phmm_get_original_image_url',
      $ajax,
      'get_original_image_url'    );
    $this->loader->add_action(
      'wp_ajax_nopriv_phmm_get_original_image_url',
      $ajax,
      'get_original_image_url'    );

    $this->loader->add_action(
      'wp_ajax_codeneric_phmm_update_premium',
      $ajax,
      'update_premium'    );

    $this->loader->add_action(
      'wp_ajax_codeneric_phmm_set_product_demo_finished',
      $ajax,
      'set_product_demo_finished'    );

    $this->loader
      ->add_action('init', $plugin_admin, 'register_client_post_type');

    // $this->loader->add_action(
    //   'after_setup_theme',
    //   $plugin_admin,
    //   'add_custom_image_sizes',
    // );

    $this->loader->add_action(
      'admin_head',
      $frontendHandler,
      'handle_analytics_enqueue'    );

    $this->loader->add_action(
      'admin_init',
      $frontendHandler,
      'handle_placeholder_image_upload'    );
    $this->loader
      ->add_action('init', $plugin_admin, 'register_project_post_type');
    $this->loader->add_action(
      'admin_enqueue_scripts',
      $frontendHandler,
      'enqueue_styles'    );
    $this->loader->add_action(
      'admin_enqueue_scripts',
      $frontendHandler,
      'enqueue_scripts'    );

    $this->loader->add_action(
      'admin_menu',
      \codeneric\phmm\base\admin\InteractionsPage::class,
      'add_page'    );
    $this->loader->add_action('admin_menu', $settings, 'add_settings_page');
    $this->loader->add_action(
      'admin_menu',
      \codeneric\phmm\base\admin\SupportPage::class,
      'add_page'    );
    $this->loader->add_action(
      'admin_menu',
      \codeneric\phmm\base\admin\PremiumPage::class,
      'add_page'    );

    $this->loader->add_action(
      'admin_init',
      \codeneric\phmm\base\admin\InteractionsPage::class,
      'init'    );
    $this->loader->add_action('admin_init', $settings, 'init');
    $this->loader->add_action(
      'admin_init',
      \codeneric\phmm\base\admin\PremiumPage::class,
      'init'    );
    $this->loader->add_action('admin_init', $plugin_admin, 'update_database');
    $this->loader->add_action(
      'admin_init',
      $plugin_admin,
      'add_admin_notice_fast_images_available_for_free'    );
    $this->loader->add_action(
      'admin_init',
      $plugin_admin,
      'add_admin_notice_rate_the_plugin'    );
    $this->loader->add_action(
      'admin_init',
      $plugin_admin,
      'add_admin_notice_analytics_opt_in'    );

    $this->loader->add_action(
      'admin_init',
      $plugin_admin,
      'add_admin_notice_update_phmm_fast_images'    );

    $this->loader->add_action(
      'add_meta_boxes_'.$config['client_post_type'],
      $plugin_admin,
      'add_client_meta_box'    );
    $this->loader->add_action(
      'add_meta_boxes_'.$config['project_post_type'],
      $plugin_admin,
      'add_project_meta_box'    );

    $this->loader
      ->add_action('save_post', $plugin_admin, 'save_meta_box_data', 10, 3);

    $this->loader
      ->add_action('after_setup_theme', $frontendHandler, 'hide_admin_bar');
    $this->loader->add_action(
      'before_delete_post',
      $plugin_admin,
      'cleanup_before_deletion'    );

    $this->loader->add_action(
      'admin_notices',
      $frontendHandler,
      'warn_if_page_template_not_exists'    );
    // $this->loader->add_action(
    //   'admin_notices',
    //   $frontendHandler,
    //   'notice_if_page_template_never_set',
    // );
    $this->loader->add_filter(
      'enter_title_here',
      $frontendHandler,
      'change_title_placeholder'    );
    $this->loader
      ->add_filter('upload_dir', $plugin_admin, 'resume_photo_upload_dir');

    $this->loader->add_filter(
      'manage_'.$config['client_post_type'].'_posts_columns',
      $frontendHandler,
      'define_client_table_columns'    );
    $this->loader->add_action(
      'manage_'.$config['client_post_type'].'_posts_custom_column',
      $frontendHandler,
      'fill_client_columns',
      10,
      2    );
    $this->loader->add_filter(
      'manage_'.$config['project_post_type'].'_posts_columns',
      $frontendHandler,
      'define_project_table_columns'    );
    $this->loader->add_action(
      'manage_'.$config['project_post_type'].'_posts_custom_column',
      $frontendHandler,
      'fill_project_columns',
      10,
      2    );

    $this->loader->add_action(
      'init',
      \codeneric\phmm\base\includes\Labels::class,
      'initLabelStore',
      10    );

    $this->loader
      ->add_action('template_redirect', $plugin_admin, 'provide_csv');

    $this->loader->add_action(
      'codeneric/phmm/watermark',
      \codeneric\phmm\base\Watermarker::class,
      'watermark_image',
      10,
      1    );
    $this->loader->add_action('deleted_user', Client::class, 'delete_client_by_wp_user_id');

    // $this->loader->add_action(
    //   'wp_after_admin_bar_render',
    //   \codeneric\phmm\base\admin\FrontendHandler::class,
    //   'cleanup_state',
    //   10,
    // );

    // register_shutdown_function(array(\codeneric\phmm\base\admin\FrontendHandler::class, 'cleanup_state'));

    // $frontendHandler::handle_error("any");
    // Force 1 column layout in edit view
    // $this->loader->add_filter(
    //   'get_user_option_screen_layout_'.$config['project_post_type'],
    //   $plugin_admin,
    //   'make_post_edit_one_column',
    // );

    // $this->loader->add_filter(
    //   'screen_layout_columns',
    //   $plugin_admin,
    //   'screen_layout_columns',
    // );

    // $this->loader->add_action(
    //   'ajax_query_attachments_args',
    //   $plugin_admin,
    //   'mutate_media_modal_query',
    // );
    $this->loader->add_action(
      'ajax_query_attachments_args',
      $plugin_admin,
      'mutate_media_library_query'    );

    $this->loader->add_filter(
      'wp_privacy_personal_data_exporters',
      Privacy::class,
      'register_data_exporter'    );

    //   add_action('admin_init', 'my_example_plugin_add_privacy_policy_content');

    $this->loader
      ->add_action('admin_init', Privacy::class, 'get_privacy_content');

    $this->loader->add_action(
      'update_option_siteurl',
      $plugin_admin,
      'update_htaccess',
      10,
      2    );

    $this->loader->add_filter(
      'next_post_link',
      $frontendHandler,
      'remove_post_links_for_custom_post_types'    );
    $this->loader->add_filter(
      'previous_post_link',
      $frontendHandler,
      'remove_post_links_for_custom_post_types'    );
  }

  private function define_public_hooks(){

    $plugin_public = \codeneric\phmm\base\frontend\Main::class;
    $this->loader->add_filter('init', $plugin_public, 'posts_logout');

    $this->loader
      ->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
    $this->loader
      ->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

    $this->loader
      ->add_filter('the_content', $plugin_public, 'the_content_hook');
    $this->loader
      ->add_action('template_include', $plugin_public, 'apply_template');

    if (\codeneric\phmm\Utils::wp_version_is_at_least("4.7.0")) {
      $this->loader->add_action(
        'post_password_required',
        $plugin_public,
        'filter_post_password_required',
        20,
        2      );
    } else
      $this->loader->add_filter(
        'the_password_form',
        $plugin_public,
        'the_password_form_hook'      );

    $this->loader->add_filter(
      'protected_title_format',
      $plugin_public,
      'remove_protected_string'    );

    $this->loader->add_filter(
      'jetpack_photon_skip_image',
      $plugin_public,
      'photon_exceptions',
      10,
      3    );

    $this->loader->add_filter(
      'jetpack_photon_override_image_downsize',
      $plugin_public,
      'photon_exceptions_2',
      10,
      2    );

    $this->loader->add_action(
      'template_redirect',
      $plugin_public,
      'provide_secured_image'    );
    $this->loader->add_action(
      'template_redirect',
      $plugin_public,
      'redirect_from_portal_page'    );
    // $this->loader->add_action(
    //   'template_redirect',
    //   $plugin_public,
    //   'redirect_from_client_page',
    // );
    $this->loader->add_action(
      'wp_login_failed',
      $plugin_public,
      'login_failed'    );
    $this->loader->add_action(
      'pre_get_posts',
      $plugin_public,
      'allow_pending_guest_view'    );

    // $this->loader->add_filter(
    //   'login_redirect',
    //   $plugin_public,
    //   'login_redirect',
    //   10,
    //   3,
    // );
    \add_shortcode(
      \codeneric\phmm\base\frontend\Shortcodes::GALLERY,
      array($plugin_public, 'gallery_shortcode')    );
    \add_shortcode(
      \codeneric\phmm\base\frontend\Shortcodes::CLIENT,
      array($plugin_public, 'client_shortcode')    );
    \add_shortcode(
      \codeneric\phmm\base\frontend\Shortcodes::PORTAL,
      array($plugin_public, 'portal_shortcode')    );
  }

  public function run(){
    $this->loader->run();
  }

  /**
   * The reference to the class that orchestrates the hooks with the plugin.
   *
   * @since     1.0.0
   * @return    Phmm_Loader    Orchestrates the hooks of the plugin.
   */
  public function get_loader(){
    return $this->loader;
  }

}
