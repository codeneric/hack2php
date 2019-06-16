<?php //strict
namespace codeneric\phmm\base\admin;
use \codeneric\phmm\Configuration;
use \codeneric\phmm\base\frontend\Shortcodes;

use \codeneric\phmm\base\includes\Error;

class Settings {

  const option_group = "codeneric_phmm";
  const option_name = "codeneric_phmm_plugin_settings";
  const option_section = "cc_photo_settings_section";
  const page_name = 'options';

  // private string $plugin_name;
  // private string $version;
  // private string $slug;

  // private string $option_group;
  // private string $option_name;
  // private string $option_section;

  // public string $page_name = 'options';
  // public function __construct(
  //   string $plugin_name,
  //   string $version,
  //   string $slug,
  // ) {
  //   self::plugin_name = $plugin_name;
  //   $this->version = $version;
  //   $this->slug = $slug;
  //   $this->option_group = "codeneric_phmm";
  //   $this->option_name = "cc_photo_settings";
  //   $this->option_section = "cc_photo_settings_section";

  // }

  // public static function remove_portal_from_page(int $pageID): void {
  //   $post = get_post($pageID);
  //   invariant(!is_null($post), "%s", new Error("Given post does not exist"));

  //   wp_update_post(
  //     [
  //       "ID" => $post->ID,
  //       "post_content" => str_replace(
  //         "[".Shortcodes::PORTAL."]",
  //         '',
  //         $post->post_content,
  //       ),
  //     ],
  //   );
  // }
  // public static function add_portal_to_page(int $pageID): void {

  //   $post = get_post($pageID);
  //   invariant(!is_null($post), "%s", new Error("Given post does not exist"));

  //   if (has_shortcode($post->post_content, Shortcodes::PORTAL))
  //     return; // dont do anything

  //   wp_update_post(
  //     [
  //       "ID" => $post->ID,
  //       "post_content" => $post->post_content."[".Shortcodes::PORTAL."]",
  //     ],
  //   );

  // }
  public static function init(){
    \register_setting(
      self::option_group,
      self::option_name,
      array(self::class, 'sanitize_option')    );

    \add_settings_section(
      self::option_section,
      '',
      array(self::class, 'settings_section_callback'),
      self::option_group    );

    // invariant(false, '%s', new Error('Getting options; expected array'));

    /* The rest is handled by JS */

  }
  public static function sanitize_option(
$options  ){

    // $options = null;
    if (\is_null($options))
      return [];

    /*
     * Checkbox inputs does not get included in POST when unchecked
     * Therefore we need to set the value for each boolean option to NULL when not existing
     * Otherwise the off state of a toggle will not be saved
     */

    $booleanOptions = [
      "hide_admin_bar",
      "enable_slider",
      "remove_images_on_project_deletion",
      "analytics_opt_in",
    ];
    foreach ($booleanOptions as $key) {
      if (!\array_key_exists($key, $options))
        $options[$key] = null;
    }
    // var_dump($options);
    // exit();
    $return = array();
    foreach ($options as $key => $value) {

      $state = null;
      switch ($key) {
        case 'enable_slider':
        case 'remove_images_on_project_deletion':
        case 'analytics_opt_in':
        case 'hide_admin_bar':
          $state = is_bool($value) ? $value : !\is_null($value);
          break;

        case 'canned_emails':
          if (is_array($value))
            $state = \array_values($value); else
            $state = $value;

          break;
        case 'max_zip_part_size':
          $state = (int) $value;
          break;

        case 'portal_page_id':
          // clean up
          if ($value === '')
            $newPage = null; else
            $newPage = (int) $value;

          $state = $newPage;
          break;
        case 'watermark':
          $sanitized = \codeneric\phmm\validate\watermark($value);
          $state = $sanitized;

          break;
        default:

          $state = $value;
          break;

      }
      $return[$key] = $state;
    }
    return $return;

  }
  public static function settings_section_callback(){}

  public static function add_settings_page(){

    \add_submenu_page(
      'edit.php?post_type='.Configuration::get()['client_post_type'],
      'PHMM '.\__('Settings', 'photography-management'),
      \__('Settings', 'photography-management'),
      'manage_options',
      self::page_name,
      array(self::class, 'render_add_submenu_page')    );

  }

  public static function render_add_submenu_page(){

    $title = "<h2>".\__('Settings', 'photography-management')."</h2>";

    // $settings = self::getCurrentSettings();

    // $json = json_encode($settings);

    $fbJoin =
      "<strong>".
      \__(
        'Join our <a style=\'color: coral\' target=\'_blank\' href=\'https:\/\/www.facebook.com/groups/1529247670736165/\'>facebook group</a> to get immediate help or get in contact with other photographers using WordPress!',
        Configuration::get()['plugin_name']      ).
      "</strong>";

    echo "<form action='options.php' method='post'>
            $title
      <div class='postbox'>
                <div class='inside'>";
    \wp_nonce_field();
    \settings_fields(self::option_group);
    \do_settings_sections(self::option_group);

    echo
      "<div id='cc_phmm_settings'  >
       <div style=\"background:url('images/spinner.gif') no-repeat;background-size: 20px 20px;vertical-align: middle;margin: 0 auto;height: 20px;width: 20px;display:block;\"></div>
    </div>"
    ;
    /* UNSAFE_EXPR */
    submit_button();

    echo "<hr />".$fbJoin."</div></div></form>";

  }

  public static function getCurrentSettings(
  ){
    $settings = \get_option(self::option_name, array());
\HH\invariant(      is_array($settings),
      '%s',
      new Error('Getting options; expected array'));

    // update_option(self::option_name, array());
    $defaultSettings = self::getDefaultSettings();

    $merged = \array_merge($defaultSettings, $settings);
    // wp_die(var_dump($merged));

    return $merged;
  }

  public static function updateSettings(
$settings  ){
    $defaultSettings = self::getDefaultSettings();

    $merged = \array_merge($defaultSettings, $settings);

    \update_option(self::option_name, $merged);

  }

  public static function getDefaultSettings(
  ){
    // return shape(
    //   'hide_admin_bar' => false,
    //   'accent_color' => "#0085ba",
    //   'cc_photo_image_box' => false,
    //   'cc_photo_enable_styling' => true,
    //   'cc_photo_lightbox_theme' =>
    //     (string)\codeneric\phmm\type\settings\LighboxThemeEnum::DARK,
    //   'page_template' => '',
    //   'custom_css' => '',
    //   'remove_images_on_project_deletion' => false,
    //   'cc_email_recipient' => '',
    //   'canned_emails' => [],
    //   'max_zip_part_size' => Configuration::get()['max_zip_part_size'],
    //   'watermark' => shape(
    //     "image_id" => null,
    //     "scale" => null,
    //     "position" => null,
    //   ),
    // );
    $s = \codeneric\phmm\validate\plugin_settings(
      [
        'max_zip_part_size' => Configuration::get()['max_zip_part_size'],
        'watermark' => [],
      ]    );
    return $s;
  }
}
