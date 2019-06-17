<?php //strict
namespace codeneric\phmm\base\frontend;

use \codeneric\phmm\base\includes\Project;
use \codeneric\phmm\base\includes\Client;
use \codeneric\phmm\base\includes\Image;
use \codeneric\phmm\base\includes\Error;
use \codeneric\phmm\base\includes\Permission;
use \codeneric\phmm\base\admin\Settings;
use \codeneric\phmm\base\globals\Superglobals;
use \codeneric\phmm\base\admin\FrontendHandler;
use \codeneric\phmm\Utils;
use \codeneric\phmm\Configuration;
use \codeneric\phmm\enums\ProjectDisplay;
use \codeneric\phmm\enums\PortalDisplay;
use \codeneric\phmm\enums\ClientDisplay;
use \codeneric\phmm\enums\AdvancedBoolSettings;
final class Shortcodes { private function __construct() {} 
private static $hacklib_values = array(
"PORTAL" => "cc_phmm_portal" ,
"GALLERY" => 'phmm-project' ,
"CLIENT" => 'phmm-client' 
);
use \HH\HACKLIB_ENUM_LIKE;
const PORTAL = "cc_phmm_portal";
const GALLERY = 'phmm-project';
const CLIENT = 'phmm-client';
 }

// require_once plugin_dir_path(__FILE__).'../admin/settings.php';
class Main {

  public static function enqueue_styles(){
    \do_action("codeneric/phmm/custom_css");

    if (self::is_project_page()) {
      \wp_enqueue_style(
        "codeneric-phmm-css-public",
        Configuration::get()['assets']['css']['public']['projects'],
        [],
        null,
        'all'      );

    }

  }

  public static function enqueue_scripts(){

    if (self::is_portal_page()) {
      $configuration = Configuration::get();
      $portal_registration =
        $configuration['assets']['js']['public']['portal_registration'];
      $portal_registration_script_handle = 'codeneric_phmm_portal_registration';
      $ajaxurl = \admin_url('admin-ajax.php');
      \wp_register_script(
        $portal_registration_script_handle,
        $portal_registration,
        array(),
        $configuration['version'],
        true      );
      \wp_localize_script(
        $portal_registration_script_handle,
        'codeneric_phmm_ajaxurl',
        $ajaxurl      );
      \wp_enqueue_script($portal_registration_script_handle);
      return;
    }

    if (!self::is_our_business())
      return;

    $configuration = Configuration::get();
    /* early abort if the post of given ID is not existing
     relevant when shortcode is used and admin fails to enter valid ID
     */

    if (self::is_project_page()) {
      $id = self::get_relevant_id(Shortcodes::GALLERY);

      if (\get_post_type($id) !== $configuration['project_post_type'])
        return;
    }
    if (self::is_client_page()) {
      $id = self::get_relevant_id(Shortcodes::CLIENT);

      if (\get_post_type($id) !== $configuration['client_post_type'])
        return;
    }

    /* here it is our business and the relevant id is valid */

    $scripthandle = '';

    // Nothing should be enqueued
    if (self::is_project_page()) {
      $id = self::get_relevant_id(Shortcodes::GALLERY);

      $display = Permission::display_project($id);
      if (
        $display !== ProjectDisplay::ProjectWithClientConfig &&
        $display !== ProjectDisplay::ProjectWithProjectConfig &&
        $display !== ProjectDisplay::AdminNotice
      )
        return;
    }

    $commons_script_handle = "codeneric-phmm-public-commons";

    \wp_register_script(
      $commons_script_handle,
      $configuration['assets']['js']['public']['common'],
      array(),
      $configuration['version'],
      true    );

    $url = \plugins_url('/', $configuration['manifest_path']);

    \wp_localize_script(
      $commons_script_handle,
      'codeneric_phmm_plugins_dir',
      $url    );
    \wp_enqueue_script($commons_script_handle);

    if (self::is_project_page()) {
      $id = self::get_relevant_id(Shortcodes::GALLERY);

      $display = Permission::display_project($id);
      $scriptsrc = $configuration['assets']['js']['public']['project'];
      $scripthandle = $configuration['plugin_name']."-public-project";

      $projectGlobals = null;
      if ($display === ProjectDisplay::ProjectWithClientConfig) {
        $client = Client::get_current();
        $clientID = null;
        if ($client)
          $clientID = $client['ID'];

        $projectGlobals = Project::get_project_for_frontend($id, $clientID);
      }

      if ($display === ProjectDisplay::ProjectWithProjectConfig) {
        $projectGlobals = Project::get_project_for_frontend($id, null);
      }

      if ($display === ProjectDisplay::AdminNotice) {
        $projectGlobals = Project::get_project_for_frontend($id, null);
      }

      if (!\is_null($projectGlobals)) {

        \wp_register_script(
          $scripthandle,
          $scriptsrc,
          array($commons_script_handle),
          $configuration['version'],
          true        );
        \wp_localize_script(
          $scripthandle,
          'codeneric_phmm_public_project_globals',
          \json_encode($projectGlobals)        );
      }
    }
    if (self::is_client_page()) {

      $scriptsrc = $configuration['assets']['js']['public']['client'];
      $scripthandle = $configuration['plugin_name']."-public-client";

      \wp_register_script(
        $scripthandle,
        $scriptsrc,
        array($commons_script_handle),
        $configuration['version'],
        true      );

      \wp_localize_script(
        $scripthandle,
        'codeneric_phmm_public_client_globals',
        \json_encode(self::get_client_public_frontend_globals())      );
    }
    \wp_localize_script(
      $scripthandle,
      'codeneric_phmm_public_general_globals',
      \json_encode(self::get_general_public_frontend_globals())    );
    \wp_enqueue_script($scripthandle);

  }

  public static function get_client_public_frontend_globals(
  ){
    $clientID = self::get_relevant_id(Shortcodes::CLIENT);

    $projects = Client::get_project_wp_posts($clientID, true);

    $transformed = \array_map(
      function($project) {
        $permalink = \get_permalink($project->ID);
\HH\invariant(          is_string($permalink),
          '%s',
          new Error("Failed to get permalink from existing project post"));

        return array(
          'id' => $project->ID,
          'permalink' => $permalink,
          'title' => Project::get_title_with_id_default($project->ID),
          'thumbnail' => Project::get_thumbnail($project->ID),
        );
      },
      $projects    );

    return array('projects' => $transformed);

  }
  private static function get_public_global_options(
  ){
    $settings = Settings::getCurrentSettings();

    return array(
      'accent_color' => $settings['accent_color'],
      'enable_slider' => $settings['enable_slider'],
      'slider_theme' => $settings['slider_theme'],
    );
  }
  public static function get_general_public_frontend_globals(
  ){

    $backUrl = null;
    $logoutUrl = null;
    /* if we are on a project page and the viewer is a assigned client change the back url  */
    if (
      self::is_project_page() &&
      Permission::display_project(
        self::get_relevant_id(Shortcodes::GALLERY)      ) ===
        ProjectDisplay::ProjectWithClientConfig
    ) {

      $clientID =
        Client::get_client_id_from_wp_user_id(Utils::get_current_user_id());
      if (is_int($clientID)) {
        $clientUrl = \get_permalink($clientID);
        if (is_string($clientUrl))
          $backUrl = $clientUrl;

        $logoutUrl = self::posts_logout_url();
      }
    }

    return array(
      "author_id" => Utils::get_current_user_id(),
      "ajax_url" => \admin_url('admin-ajax.php'),
      "base_url" => \get_site_url(),
      "locale" => \get_locale(),
      "logout_url" => $logoutUrl,
      "back_url" => $backUrl,
      "options" => self::get_public_global_options(),
      // "theme" => get_theme_mods(),
      // "links" =>
      //   shape(
      //     // 'new_project' => add_query_arg(
      //     //   array('post_type' => Configuration::get()['project_post_type']),
      //     //   admin_url('post-new.php'),
      //     // ),
      //     // 'new_client' => add_query_arg(
      //     //   array('post_type' => Configuration::get()['client_post_type']),
      //     //   admin_url('post-new.php'),
      //     // ),
      //   ),
    );
  }
  private static function get_current_post_type(){
    $post = /* UNSAFE_EXPR */ $GLOBALS['post'];

    $type = \get_post_type($post);

    if (is_bool($type))
      return null;
    else
      return (string)$type;

  }

  public static function is_project_page(){
    $post = \get_post();

    if (!\is_null($post)) {
      if (\has_shortcode($post->post_content, Shortcodes::GALLERY))
        return true;
    }

    return \is_single() &&
      self::get_current_post_type() ===
        Configuration::get()['project_post_type'];
  }
  public static function is_client_page(){

    $post = \get_post();

    if (!\is_null($post)) {
      if (\has_shortcode($post->post_content, Shortcodes::CLIENT))
        return true;
    }
    return \is_single() &&
      self::get_current_post_type() ===
        Configuration::get()['client_post_type'];
  }
  public static function is_portal_page(){

    $post = \get_post();

    if (\is_null($post))
      return false;

    if (\has_shortcode($post->post_content, Shortcodes::PORTAL))
      return true;

    $currentPortalPage = Settings::getCurrentSettings()['portal_page_id'];

    if (\is_null($currentPortalPage))
      return false;

    return $currentPortalPage === $post->ID;
  }

  private static function is_our_business(){
    return self::is_client_page() || self::is_project_page();
  }

  private static function has_current_post_shortcode(
$shortcode  ){
    /* If the user has explicitly inserted the shortcode, he wants it to be in a specific place. */

    $post = \get_post();

    if (\is_null($post))
      return false;

    return \has_shortcode($post->post_content, $shortcode);

  }
  private static function attach_shortcode(
$content,
$shortcode  ){

    /* If the user has explicitly inserted the shortcode, he wants it to be in a specific place. */
    if (\has_shortcode($content, $shortcode))
      return $content;

    // otherwise append at the end
    $content .= "[".$shortcode."]";
    return $content;
  }

  private static function replace_shortcode_or_append(
$content,
$replacement,
$shortcode  ){
    /* If the user has explicitly inserted the shortcode, he wants it to be in a specific place. */
    if (\has_shortcode($content, $shortcode)) {
      $d = \preg_replace(
        "/\[\s*".$shortcode."\s*(?:id=[\"\'](.*)[\"\'])?\s*\]/",
        $replacement,
        $content      );

      return $d;
      // return str_replace("[".$shortcode."]", $replacement, $content);
    }
    return $content.$replacement;

  }

  private static function get_relevant_id($shortcode){
    $post = \get_post();
\HH\invariant(!\is_null($post), '%s', new Error("Post is not set"));

    if (!\has_shortcode($post->post_content, $shortcode))
      return $post->ID;

    $content = $post->post_content;

    $matches = [];
    \preg_match("/\[".$shortcode."\s*id=\"(.*)\"\s*\]/", $content, $matches);

    /* for now handle only single match */
    if (\count($matches) === 2) {
      return (int)$matches[1];
    }
    return -1;

  }

  private static function get_the_id(){
    $id = \get_the_ID();
\HH\invariant(is_int($id), '%s', new Error("Post is not set"));
    return $id;
  }

  public static function the_content_hook($content){
    if (!is_int(\get_the_ID())) {
      return $content; //Yoast SEO fucks us up!
    }
    // if (post_password_required())
    //   return $content;

    $noAccessHTML = "<h1>".\__("No Access")."<h1>";
    $pending_guest_review_HTML =
      "<h3 style=\"background-color: rgba(253, 187, 0, 0.3);\">".
      \__(
        "Your account is under review. You will get access to the photos as soon as your account was approved. Please come back later!"      ).
      "</h3>";
    $accpeted_guest_request_HTML =
      "<h3 style=\"background-color: rgba(0, 250, 0, 0.3);\">".
      \__(
        "This account has been approved! The user can access the galleries now!"      ).
      "</h3>";
    $declined_guest_request_HTML =
      "<h3 style=\"background-color: rgba(0, 0, 250, 0.3);\">".
      \__(
        "This account has been declined! The user cannot access the galleries!"      ).
      "</h3>";
    $not_permitted_guest_request_HTML =
      "<h3 style=\"background-color: rgba(253, 0, 0, 0.3);\">".
      \__("You are not allowed to review this guest.").
      "</h3>";

    $adminNoticeHTML = "<style>@keyframes slideInFromBottom {
  0% {
    transform: translateY(100%);
  }
  100% {
    transform: translateX(0);
  }
      }</style>".
      "<div id='cc-phmm-pfe-admin-notice' style=' transform: translateY(100%); animation:slideInFromBottom 1s ease-in-out forwards;  animation-delay: 2s; background:#ecf0f1; padding: 10px 0; text-align:center;border:1px solid rgba(0,0,0,0.1); left:0; position:fixed; bottom:0; width:100%; z-index:100000;'><h3 style='margin:0;margin-bottom:10px;'>".
      "<strong>PHMM: </strong>".
      \__(
        "You view the project as an admin. To verify the project password protection, open this page in an incognito window."      ).
      "</h3><span style='background:#7f8c8d; margin: 5px; padding:5px; cursor:pointer; color: #ecf0f1;' id='cc-phmm-pfe-admin-notice-close'>Ok, close</span></div>
      <script>
        document.getElementById('cc-phmm-pfe-admin-notice-close').onclick = function() {
          document.getElementById('cc-phmm-pfe-admin-notice').remove();
        }
      </script>";

    // $postID = self::get_the_id();
    $loginForm = \wp_login_form(["echo" => false]);
    $pwdForm = \get_the_password_form();
    if (self::is_client_page()) {
      $postID = self::get_relevant_id(Shortcodes::CLIENT);

      $cv = Permission::display_client($postID);
      switch ($cv) {
        case ClientDisplay::ClientView:
          return self::attach_shortcode($content, Shortcodes::CLIENT);
          break;
        case ClientDisplay::GuestPendingReview:
          return self::replace_shortcode_or_append(
            $content,
            $pending_guest_review_HTML,
            Shortcodes::CLIENT          );
          break;
        case ClientDisplay::GuestReviewAccepted:
          Client::accept_guest_registration($postID);
          return self::replace_shortcode_or_append(
            $content,
            $accpeted_guest_request_HTML,
            Shortcodes::CLIENT          );
          break;
        case ClientDisplay::GuestReviewDeclined:
          Client::trash_client($postID);
          return self::replace_shortcode_or_append(
            $content,
            $declined_guest_request_HTML,
            Shortcodes::CLIENT          );
          break;
        case ClientDisplay::ReviewGuestRequestNotPermitted:
          return self::replace_shortcode_or_append(
            $content,
            $not_permitted_guest_request_HTML,
            Shortcodes::CLIENT          );
          break;
        case ClientDisplay::NoAccess:
          return self::replace_shortcode_or_append(
            $content,
            $noAccessHTML,
            Shortcodes::CLIENT          );
          break;
        case ClientDisplay::AdminNoticeWithClientView:
          return self::attach_shortcode($content, Shortcodes::CLIENT);
          break;
        case ClientDisplay::LoginForm:
          return self::replace_shortcode_or_append(
            $content,
            $loginForm,
            Shortcodes::CLIENT          );

          break;
      }
    }

    if (self::is_project_page()) {
      $postID = self::get_relevant_id(Shortcodes::GALLERY);
      switch (Permission::display_project($postID)) {
        case ProjectDisplay::LoginForm:

          return self::replace_shortcode_or_append(
            $content,
            $loginForm,
            Shortcodes::GALLERY          );
        case ProjectDisplay::NoAccess:
          return self::replace_shortcode_or_append(
            $content,
            $noAccessHTML,
            Shortcodes::GALLERY          );
        case ProjectDisplay::PasswordInput:

          return self::replace_shortcode_or_append(
            $content,
            $pwdForm,
            Shortcodes::GALLERY          );
        case ProjectDisplay::SplitLoginView:
          $wrapStyle =
            "border:1px solid rgba(0,0,0,0.15); padding: 1em;margin: 0.5em;";
          $html = "<div style='$wrapStyle'>".
            "<h3>".
            \__("Guest login").
            "</h3>".
            $pwdForm.
            "</div>".
            "<div style='$wrapStyle'>".
            "<h3>".
            \__("Client login").
            "</h3>".
            $loginForm.
            "</div>";
          return self::replace_shortcode_or_append(
            $content,
            $html,
            Shortcodes::GALLERY          );
        /* case ProjectDisplay::AdminNotice:
         return self::replace_shortcode_or_append(
         $content,
         $adminNoticeHTML,
         Shortcodes::GALLERY,
         ); */
        case ProjectDisplay::AdminNotice:
          /*  $content = self::replace_shortcode_or_append(
           $content,
           $adminNoticeHTML."[".Shortcodes::GALLERY."]",
           Shortcodes::GALLERY,
           ); */
          return self::attach_shortcode(
            $adminNoticeHTML.$content,
            Shortcodes::GALLERY          );

        case ProjectDisplay::ProjectWithClientConfig:
        case ProjectDisplay::ProjectWithProjectConfig:
          return self::attach_shortcode($content, Shortcodes::GALLERY);

      }

      // return
      //   Permission::current_user_can_access_project($postID)
      //     ? self::attach_shortcode($content, Shortcodes::GALLERY)
      //     : $content;
    }

    if (self::is_portal_page()) {
      switch (Permission::display_portal()) {
        case PortalDisplay::LoginForm:
          return self::attach_shortcode($content, Shortcodes::PORTAL);
        case PortalDisplay::AdminNotice:
          $replacement = "<h1>".
            \__(
              "You are logged in as admin. Logout to see the client login form and login as client to see the redirection"            ).
            "</h1>";
          return self::replace_shortcode_or_append(
            $content,
            $replacement,
            Shortcodes::PORTAL          );

        case PortalDisplay::Redirect:
          return
            $content; // do nothing because this should never apply since the redirect is already kicking in

      }
    }

    return $content;

  }
  private static function _redirect_client_to_single_project_if_necessary(
$client_id  ){
    if (
      Utils::get_advanced_bool_setting(
        AdvancedBoolSettings::PHMM_REDIRECT_CLIENT_TO_SINGLE_PROJECT      )
    ) {
      $project_ids = Client::get_project_ids($client_id);

      if (\count($project_ids) === 1) {
        $content = (string)\get_post_field('post_content', $client_id);
        if (\strlen($content) === 0) {
          $link = \get_permalink($project_ids[0]);
          if (!is_string($link))
            return;

          \wp_redirect($link);
          exit();
        }
      }
    }
  }
  /*
   * When visiting the portal page as logged in user, redirect to own client page
   */
  public static function redirect_from_portal_page(){
    if (!self::is_portal_page())
      return;

    $client_id = Client::get_current_id();

    if (\is_null($client_id))
      return;

    self::_redirect_client_to_single_project_if_necessary($client_id);

    $link = \get_permalink($client_id);

    if (!is_string($link))
      return;

    \wp_redirect($link);
    exit();

  }
  /*
   * When visiting the client page as logged in client and only one project, redirect to this project
   */
  public static function redirect_from_client_page(){
    if (!self::is_client_page())
      return;

    $client_id = Client::get_current_id();

    if (\is_null($client_id))
      return;

    self::_redirect_client_to_single_project_if_necessary($client_id);
  }

  public static function remove_protected_string($default){
    if (self::is_client_page() || self::is_project_page())
      return \__('%s');

    return $default;
  }

  public static function apply_template($default){

    $should_apply_template = self::is_project_page();
    if (
      Utils::get_advanced_bool_setting(
        AdvancedBoolSettings::PHMM_APPLY_TEMPLATE_TO_CLIENT_PAGE      )
    ) {
      $should_apply_template =
        self::is_client_page() || self::is_project_page();
    } else {
      $should_apply_template = self::is_project_page();
    }

    if ($should_apply_template) {

      $settings = Settings::getCurrentSettings();

      $template = $settings['page_template']; // guaranteed to be defined

      // 'phmm-theme-default' === default
      if ($template == 'phmm-theme-default' || $template === "") {
        return $default;
      }

      if ($template == 'phmm-legacy') {

        $legacy = \dirname(__FILE__).'/single-client.plain.php';
        if (\file_exists($legacy))
          return $legacy;
        else
          return $default;
      }

      $theme = \get_template_directory();
      $template_file = $theme.'/'.$template;

      if (\file_exists($template_file)) {
        return $template_file;
      }
    }

    // if all fails
    return $default;
  }

  public static function client_shortcode(){
    $scripthandle = Configuration::get()['plugin_name']."-public-client";
    // if (wp_script_is($scripthandle)) {
    //   wp_localize_script(
    //     $scripthandle,
    //     'CODENERIC_TEST_HADOUKEN',
    //     json_encode(Project::get_project_for_frontend(7)),
    //   );

    // }

    return '<div id="cc_phmm_public_client" style="position:relative" ></div>';
  }
  public static function gallery_shortcode($args){

    $scripthandle = Configuration::get()['plugin_name']."-public-project";
    // if (wp_script_is($scripthandle)) {
    //   wp_localize_script(
    //     $scripthandle,
    //     'CODENERIC_TEST_HADOUKEN',
    //     json_encode(Project::get_project_for_frontend(7)),
    //   );

    // }

    return
      '<div  id="cc_phmm_public_project" style="position:relative" ></div>';
  }
  public static function portal_shortcode(){
    $loginForm = \wp_login_form(["echo" => false]);
    $wrapStyle =
      "border:1px solid rgba(0,0,0,0.15); padding: 1em;margin: 0.5em;";

    // use this instead   $configuration['assets']['js']['public']['portal_registration']
    // $TODO = \file_get_contents(__DIR__.'/../assets/js/registration.js');
    // $js = "<script>$TODO</script>";
    $x = '<span style="color: red;"> *</span>';

    $fields = array(
      array(
        'label' => \__("First Name"),
        'id' => 'codeneric_phmm_registration_first_name',
        'name' => 'first_name',
        'type' => 'text',
      ),
      array(
        'label' => \__("Last Name"),
        'id' => 'codeneric_phmm_registration_last_name',
        'name' => 'last_name',
        'type' => 'text',
      ),
      array(
        'label' => \__("Username"),
        'id' => 'codeneric_phmm_registration_user_login',
        'name' => 'user_login',
        'type' => 'text',
      ),
      array(
        'label' => \__("Password"),
        'id' => 'codeneric_phmm_registration_user_pass',
        'name' => 'user_pass',
        'type' => 'password',
      ),
      array(
        'label' => \__("Email"),
        'id' => 'codeneric_phmm_registration_user_email',
        'name' => 'user_email',
        'type' => 'text',
      ),
    );

    $fields_div = "";
    foreach ($fields as $f) {
      $fields_div .= "<p><label for=\"{$f['id']}\">{$f['label']}$x</label>".
        "<input type=\"{$f['type']}\" id=\"{$f['id']}\" name=\"{$f['name']}\" required></p>";
    }

    $html = "<div style='$wrapStyle'>".
      "<h3>".
      \__("Client login").
      "</h3>".
      $loginForm.
      "</div>".
      "<div style='$wrapStyle'>".
      '<form name="codeneric_registration_form" id="codeneric_registration_form" action="/" method="post">'.
      "<h3>".
      \__("Registration").
      "</h3>".
      '<p><label for="codeneric_phmm_registration_code">'.
      \__("Registration Code").
      $x.
      '</label> 
      <input type="text" name="code" id="codeneric_phmm_registration_code" required></p>'.

      '<p> 
      <button type="button" id="codeneric_phmm_registration_code_check" />'.
      \__("Check").
      '</button></p>'.


      '<div id="codeneric_phmm_registration_client_form" style="display:none;">'.

      '<p>'.
      \__(
        'Awesome! After your registration you will have access to these projects:'      ).
      '<span id="codeneric_phmm_registration_for_projects" ></span></p>'.
      $fields_div.
      '<p> 
      <input type="submit" id="codeneric_phmm_registration_submit" value="'.
      \__("Sign up").
      '"></p>'.
      '</from>'.
      "</div>".

      "</div>";

    return $html;
  }

  public static function posts_logout_url(){
    return \wp_nonce_url(
      \add_query_arg(
        array('action' => 'codeneric_phmm_posts_logout'),
        \site_url('wp-login.php', 'login')      ),
      'codeneric_phmm_posts_logout'    );
  }

  public static function posts_logout(){

    $request = \codeneric\phmm\base\globals\Superglobals::Request();
\HH\invariant(is_array($request), '%s', new Error('_request is not an array.'));
    // invariant(
    //   is_string($request['action']),
    //   '%s',
    //   new Error('_REQUEST[action] not a string.'),
    // );

    if (
      \array_key_exists('action', $request) &&
      $request['action'] == Configuration::get()['phmm_posts_logout']
    ) {
      // check_admin_referer(Configuration::get()['phmm_posts_logout']);

      $cookiehash = /*UNSAFE_EXPR*/ COOKIEHASH;
      $cookiepath = /*UNSAFE_EXPR*/ COOKIEPATH;

      // invariant(
      //   is_string($cookiehash),
      //   '%s',
      //   new Error('Cookiehash is not a string!'),
      // );
      // invariant(
      //   is_string($cookiepath),
      //   '%s',
      //   new Error('Cookiepath is not a string!'),
      // );

      \setcookie(
        Configuration::get()['cookie_wp_postpass'].$cookiehash,
        ' ',
        \time() - 31536000,
        $cookiepath      );
      \wp_logout(); // destroy user session
      \wp_redirect(\wp_get_referer());
      die();
    }
  }

  /**
   * Hide the post password form for logged in clients that have access to the project
   * This is the cleaner hook introduced in WP 4.7.0
   */
  public static function filter_post_password_required(
$actual,
$post  ){
    if (!self::is_project_page())
      return $actual;

    return false;
  }

  /**
   * Hide the post password form for logged in clients that have access to the project
   * This is the legacy handling for WP smaller than 4.7.0
   */
  public static function the_password_form_hook($output){
    $postID = self::get_the_id();

    if (self::is_project_page()) {
      return '';
    }

    return $output;
  }

  /**
   * TODO ^ ersetzen von mixed variablen und return type mixed!
   */
  public static function photon_exceptions(
$val,
$src,
$tag  ){
    if (
      \strpos($src, 'uploads/photography_management') !== false
    ) { //pipe through protect.php
      return true;
    }
    return $val;
  }

  /**
   * TODO ^ ersetzen von mixed variablen und return type mixed!
   */
  public static function photon_exceptions_2($skip, $b){
    // invariant(
    //   is_array($b),
    //   '%s',
    //   new Error('Photon exception 2 parameter is not an array'),
    // );
    // if (array_key_exists('attachmend_id', $b)) {
    //   $fullsize_path = get_attached_file($b['attachment_id']);
    //   return
    //     strpos($fullsize_path, 'photography_management') !== false;

    // }
    // return $skip;
    //UNSAFE
    if (isset($b) && isset($b['attachment_id'])) {
      $fullsize_path = get_attached_file($b['attachment_id']);
      return strpos($fullsize_path, 'photography_management') !== false;

    }
    return $skip;
  }

  public static function provide_secured_image($args){
    $get = Superglobals::Get();

    $codeneric_load_image = \array_key_exists('codeneric_load_image', $get)
      ? $get['codeneric_load_image']
      : 0;
    if (\intval($codeneric_load_image) == 1) {

      \codeneric_send_image_if_allowed();
    }

  }

  public static function login_failed(){
    $referrer = \wp_get_referer();

    if (
      $referrer &&
      !\strstr($referrer, 'wp-login') &&
      !\strstr($referrer, 'wp-admin')
    ) {
      \wp_redirect($referrer);
      exit();
    }
  }

  public static function allow_pending_guest_view($qry){

    if (
      !\is_admin() &&
      $qry->get('post_type') === Configuration::get()['client_post_type']
    ) {

      $qry->set('post_status', array('publish', 'pending'));

    }
    // if (!is_admin() && current_user_can('edit_posts')) {
    // }
  }

}
