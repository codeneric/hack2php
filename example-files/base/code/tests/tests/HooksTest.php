<?hh
/* HH_IGNORE_ERROR[3015] The member is always defined with setUp() */
final class HooksTest extends WP_UnitTestCase {

  public function setUp(): void {
    parent::setUp();

  }

  private function expectHookToBeRegistered(string $tag, array $target) {

    // check for int, since return is only bool when no action attachment found
    $class = $target[0];
    $class = is_string($class) ? $class : get_class($class);
    $this->assertTrue(
      is_int(has_action($tag, $target)),
      $class.'::'.$target[1]." should be registered to ".$tag,
    );
  }

  // public function testConditionalHookRegister() {
  //   $GLOBALS['wp_version'] = "1.0.0";
  //   $public = $this->phmm->getPublicClass();

  //    $this->expectHookToBeRegistered(
  //     "the_password_form",
  //     array($public, 'the_password_form_hook'),
  //   );
  // }

  public function testAdminHooksRegisteredAsExpected() {
    $admin = \codeneric\phmm\base\admin\Main::class;
    $frontEndHandler = \codeneric\phmm\base\admin\FrontendHandler::class;
    $settings = \codeneric\phmm\base\admin\Settings::class;
    $ajax = \codeneric\phmm\base\admin\ajax\Endpoints::class;
    $config = \codeneric\phmm\Configuration::get();

    /* Ajax stuff */
    $this->expectHookToBeRegistered(
      "wp_ajax_phmm_star_photo",
      array($ajax, 'label_images'),
    );
    $this->expectHookToBeRegistered(
      "wp_ajax_phmm_label_image",
      array($ajax, 'label_images'),
    );
    $this->expectHookToBeRegistered(
      "wp_ajax_nopriv_phmm_star_photo",
      array($ajax, 'label_images'),
    );
    $this->expectHookToBeRegistered(
      "wp_ajax_download_csv",
      array($ajax, 'download_csv'),
    );
    $this->expectHookToBeRegistered(
      "wp_ajax_nopriv_download_csv",
      array($ajax, 'download_csv'),
    );
    $this->expectHookToBeRegistered(
      "wp_ajax_phmm_fetch_images",
      array($ajax, 'fetch_gallery_images'),
    );
    $this->expectHookToBeRegistered(
      "wp_ajax_nopriv_phmm_fetch_images",
      array($ajax, 'fetch_gallery_images'),
    );
    $this->expectHookToBeRegistered(
      "wp_ajax_phmm_check_username",
      array($ajax, 'check_username'),
    );

    /* Other stuff */

    $this->expectHookToBeRegistered(
      "admin_init",
      array($admin, 'update_database'),
    );
    $this->expectHookToBeRegistered(
      "init",
      array($admin, 'register_client_post_type'),
    );
    $this->expectHookToBeRegistered(
      "init",
      array($admin, 'register_project_post_type'),
    );
    $this->expectHookToBeRegistered(
      "admin_enqueue_scripts",
      array($frontEndHandler, 'enqueue_styles'),
    );
    $this->expectHookToBeRegistered(
      "admin_enqueue_scripts",
      array($frontEndHandler, 'enqueue_scripts'),
    );
    $this->expectHookToBeRegistered(
      "admin_menu",
      array($settings, 'add_settings_page'),
    );
    $this->expectHookToBeRegistered("admin_init", array($settings, 'init'));
    $this->expectHookToBeRegistered(
      'add_meta_boxes_'.$config['client_post_type'],
      array($admin, 'add_client_meta_box'),
    );
    $this->expectHookToBeRegistered(
      'add_meta_boxes_'.$config['project_post_type'],
      array($admin, 'add_project_meta_box'),
    );
    $this->expectHookToBeRegistered(
      "save_post",
      array($admin, 'save_meta_box_data'),
    );
    $this->expectHookToBeRegistered(
      "after_setup_theme",
      array($frontEndHandler, 'hide_admin_bar'),
    );
    $this->expectHookToBeRegistered(
      "before_delete_post",
      array($admin, 'cleanup_before_deletion'),
    );
    $this->expectHookToBeRegistered(
      "upload_dir",
      array($admin, 'resume_photo_upload_dir'),
    );

    $this->expectHookToBeRegistered(
      "init",
      array(\codeneric\phmm\base\includes\Labels::class, 'initLabelStore'),
    );
    $this->expectHookToBeRegistered(
      'manage_'.$config['project_post_type'].'_posts_custom_column',
      array($frontEndHandler, 'fill_project_columns'),
    );
    $this->expectHookToBeRegistered(
      "admin_notices",
      array($frontEndHandler, 'warn_if_page_template_not_exists'),
    );
    $this->expectHookToBeRegistered(
      "admin_notices",
      array($frontEndHandler, 'notice_if_page_template_never_set'),
    );
    $this->expectHookToBeRegistered(
      "enter_title_here",
      array($frontEndHandler, 'change_title_placeholder'),
    );
    $this->expectHookToBeRegistered(
      'manage_'.$config['project_post_type'].'_posts_columns',
      array($frontEndHandler, 'define_project_table_columns'),
    );
    $this->expectHookToBeRegistered(
      'manage_'.$config['client_post_type'].'_posts_custom_column',
      array($frontEndHandler, 'fill_client_columns'),
    );
    $this->expectHookToBeRegistered(
      'manage_'.$config['client_post_type'].'_posts_columns',
      array($frontEndHandler, 'define_client_table_columns'),
    );

  }

  public function testPublicHooksRegisteredAsExpected() {
    $public = \codeneric\phmm\base\frontend\Main::class;
    $this->expectHookToBeRegistered(
      "wp_enqueue_scripts",
      array($public, 'enqueue_styles'),
    );
    $this->expectHookToBeRegistered(
      "wp_enqueue_scripts",
      array($public, 'enqueue_scripts'),
    );
    $this->expectHookToBeRegistered(
      "the_content",
      array($public, 'the_content_hook'),
    );
    $this->expectHookToBeRegistered(
      "template_include",
      array($public, 'apply_template'),
    );
    $this->expectHookToBeRegistered(
      "jetpack_photon_skip_image",
      array($public, 'photon_exceptions'),
    );
    $this->expectHookToBeRegistered(
      "jetpack_photon_override_image_downsize",
      array($public, 'photon_exceptions_2'),
    );
    $this->expectHookToBeRegistered(
      "post_password_required",
      array($public, 'filter_post_password_required'),
    );
    $this->expectHookToBeRegistered(
      "protected_title_format",
      array($public, 'remove_protected_string'),
    );
  }

  public function testShortcodeWasRegistered() {
    $this->assertTrue(
      shortcode_exists(\codeneric\phmm\base\frontend\Shortcodes::GALLERY),
    );
  }
}
