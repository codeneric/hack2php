<?hh //strict

namespace codeneric\phmm\base\admin;
use codeneric\phmm\base\includes\Client;
use codeneric\phmm\base\includes\Project;
use codeneric\phmm\base\includes\Image;
use codeneric\phmm\enums\CannedEmailPlaceholders;
use codeneric\phmm\Configuration;
use codeneric\phmm\base\includes\Error;
use codeneric\phmm\Utils;
use codeneric\phmm\type;
use codeneric\phmm\base\includes\ErrorSeverity;
use codeneric\phmm\base\globals\Superglobals as Superglobals;

class FrontendHandler {
  const POST_EMPTY_TITLE_FILL = "No title";
  const ERROR_SCRIPT_HANDLE = "codeneric-phmm-err";

  public static function enqueue_styles(): void {
    $config = Configuration::get();
    wp_enqueue_style(
      "codeneric-phmm-admin-css-fixes",
      $config['assets']['css']['admin']['fixes'],
      [],
      null,
      'all',
    );

    if (!self::is_our_business())
      return;

    if (!self::is_custom_post_edit_or_new())
      return;

    $handle = "codeneric-phmm-admin-commons-css";
    wp_enqueue_style(
      $handle,
      $config['assets']['css']['admin']['post'],
      [],
      null,
      'all',
    );

  }

  // public static function make_post_edit_one_column(mixed $columns): int {

  //   return 1;
  // }

  private static function is_custom_post_edit_or_new(): bool {

    $pagename = Superglobals::Globals('pagenow');
    return $pagename === 'post-new.php' || $pagename === 'post.php';
  }

  private static function safely_get_current_screen(): ?\WP_Screen {
    if (function_exists('get_current_screen'))
      return get_current_screen();
    return null;
  }
  private static function get_current_post_type(): ?string {

    $current_screen = self::safely_get_current_screen();
    if (!is_null($current_screen))
      return $current_screen->post_type;
    return null;
  }

  private static function is_client_single_page(): bool {
    return
      self::is_custom_post_edit_or_new() &&
      self::get_current_post_type() === Configuration::get()['client_post_type'];
  }
  public static function is_project_single_page(): bool {
    return
      self::is_custom_post_edit_or_new() &&
      self::get_current_post_type() === Configuration::get()['project_post_type'];
  }
  private static function is_settings_page(): bool {
    $current_screen = self::safely_get_current_screen();
    if (is_null($current_screen))
      return false;
    return
      $current_screen->base ===
      Configuration::get()['project_post_type'].'_page_'.Settings::page_name;
  }
  private static function is_premium_page(): bool {
    $current_screen = self::safely_get_current_screen();
    if (is_null($current_screen))
      return false;
    return
      $current_screen->base ===
      Configuration::get()['project_post_type'].'_page_'.PremiumPage::page_name;
  }
  private static function is_support_page(): bool {
    $current_screen = self::safely_get_current_screen();
    if (is_null($current_screen))
      return false;
    return
      $current_screen->base ===
      Configuration::get()['project_post_type'].'_page_'.SupportPage::page_name;
  }
  private static function is_interaction_page(): bool {
    $current_screen = self::safely_get_current_screen();
    if (is_null($current_screen))
      return false;
    return
      $current_screen->base ===
      Configuration::get()['project_post_type'].'_page_'.InteractionsPage::page_name;
  }

  private static function is_our_business(): bool {
    $current_post_type = self::get_current_post_type();
    $config = Configuration::get();
    return
      $current_post_type === $config['client_post_type'] ||
      $current_post_type === $config['project_post_type'];

  }

  private static function enqueue_admin_commons(string $handle): void {
    wp_enqueue_script('dashicons');
    $configuration = Configuration::get();
    wp_register_script(
      $handle,
      $configuration['assets']['js']['admin']['common'],
      [],
      $configuration['version'],
      true,
    );

    $url = plugins_url("/", $configuration['manifest_path']);

    wp_localize_script($handle, 'codeneric_phmm_plugins_dir', $url);
    wp_enqueue_script($handle);
  }

  public static function get_the_id(): ?int {
    $id = get_the_ID();
    if (!is_int($id))
      return null;
    return $id;
  }

  public static function enqueue_scripts(string $hook): void {

    if (!self::is_our_business())
      return;

    // commons needed for all admin side scripts

    $configuration = Configuration::get();

    $commons_script_handle = "codeneric-phmm-admin-commons";
    $scripthandle = '';

    self::enqueue_admin_commons($commons_script_handle);

    if (self::is_client_single_page()) {
      $scriptsrc = $configuration['assets']['js']['admin']['client'];
      $scripthandle = $configuration['plugin_name']."-admin-client";

      wp_register_script(
        $scripthandle,
        $scriptsrc,
        array($commons_script_handle),
        $configuration['version'],
        true,
      );

      wp_localize_script(
        $scripthandle,
        'codeneric_phmm_admin_client_globals',
        json_encode(self::get_admin_client_frontend_globals()),
      );

      wp_localize_script(
        $scripthandle,
        'codeneric_phmm_admin_client_project_access_globals',
        json_encode(
          self::get_admin_client_project_access_frontend_globals(),
        ),
      );

    }

    if (self::is_project_single_page()) {
      $scriptsrc = $configuration['assets']['js']['admin']['project'];
      $scripthandle = $configuration['plugin_name']."-admin-project";

      /* UNSAFE_EXPR */
      wp_enqueue_media();

      wp_register_script(
        $scripthandle,
        $scriptsrc,
        array('media-upload', $commons_script_handle),
        $configuration['version'],
        true,
      );

      $admin_project_frontend_globals =
        self::get_admin_project_frontend_globals();
      if (is_null($admin_project_frontend_globals))
        $admin_project_frontend_globals = [];

      wp_localize_script(
        $scripthandle,
        'CODENERIC_PHMM_ADMIN_PROJECT_GLOBALS',
        json_encode($admin_project_frontend_globals),
      );

      self::enqueue_addons_admin([$commons_script_handle]);

    }

    if (self::is_settings_page()) {
      /* UNSAFE_EXPR */
      wp_enqueue_media();

      $scripthandle = $configuration['plugin_name'].'-settings-page';

      wp_register_script(
        $scripthandle,
        $configuration['assets']['js']['admin']['settings'],
        array('media-upload', $commons_script_handle),
        $configuration['version'],
        true,
      );

      wp_localize_script(
        $scripthandle,
        'codeneric_phmm_settings_globals',
        json_encode(self::get_settings_frontend_globals()),
      );

      $settings = Settings::getCurrentSettings();

      wp_localize_script(
        $scripthandle,
        'codeneric_phmm_current_settings',
        json_encode($settings),
      );

      $watermarkImageID = $settings['watermark']['image_id'];
      if (is_int($watermarkImageID)) {
        $image = Image::get_image($watermarkImageID, false);
        // wp_die(var_dump($watermarkImageID));

        if (!is_null($image))
          wp_localize_script(
            $scripthandle,
            'codeneric_phmm_settings_preloaded_watermark_image',
            json_encode($image),
          );

      }

    }
    if (self::is_interaction_page()) {
      $scripthandle = $configuration['plugin_name'].'-interactions-page';

      wp_register_script(
        $scripthandle,
        $configuration['assets']['js']['admin']['interactions_page'],
        array($commons_script_handle),
        $configuration['version'],
        true,
      );

      wp_localize_script(
        $scripthandle,
        'codeneric_phmm_interactions_globals',
        json_encode(self::get_interactions_frontend_globals()),
      );

    }
    if (self::is_premium_page()) {
      $scripthandle = $configuration['plugin_name'].'-premium-page';

      wp_register_script(
        $scripthandle,
        $configuration['assets']['js']['admin']['premium_page'],
        array('media-upload', $commons_script_handle),
        $configuration['version'],
        true,
      );

      wp_localize_script(
        $scripthandle,
        'codeneric_phmm_premiumpage_globals',
        json_encode(self::get_premium_page_globals()),
      );

    }
    if (self::is_support_page()) {
      $scripthandle = $configuration['plugin_name'].'-support-page';

      wp_register_script(
        $scripthandle,
        $configuration['assets']['js']['admin']['support_page'],
        array($commons_script_handle),
        $configuration['version'],
        true,
      );

      wp_localize_script(
        $scripthandle,
        'codeneric_phmm_supportpage_globals',
        json_encode(self::get_support_page_globals()),
      );

    }

    // General admin globals
    wp_localize_script(
      $scripthandle,
      'codeneric_phmm_admin_globals',
      json_encode(self::get_general_admin_frontend_globals()),
    );

    wp_enqueue_script($scripthandle);

  }

  public static function enqueue_addons_admin(
    array<string> $commonScripts,
  ): void {
    $config = Configuration::get();

    $processAddon =
      function(
        string $scripthandle,
        string $pathFilter,
        string $globalsVarName,
        string $globalsFilter,
        array<string> $dependencies,
        string $version,
      ): void {
        $path = Utils::apply_filter_or($pathFilter, null, null);
        if (!is_string($path))
          return;

        $globals = Utils::apply_filter_or($globalsFilter, null, null);

        wp_register_script(
          $scripthandle,
          $path,
          $dependencies,
          $version,
          true,
        );

        if (!is_null($globals))
          wp_localize_script(
            $scripthandle,
            $globalsFilter,
            json_encode($globals),
          );
        wp_enqueue_script($scripthandle);
      };

    // captions add on
    $processAddon(
      $config['plugin_name'].'-addon-captions',
      $config['add_ons']['captions']['get_admin_js_path_filter'],
      'codeneric_img_addon_captions',
      $config['add_ons']['captions']['get_admin_frontend_globals'],
      $commonScripts,
      $config['version'],
    );
    // like add on
    $processAddon(
      $config['plugin_name'].'-addon-like',
      $config['add_ons']['like']['get_admin_js_path_filter'],
      'codeneric_img_addon_like',
      $config['add_ons']['like']['get_admin_frontend_globals'],
      $commonScripts,
      $config['version'],
    );
    // like add on
    $processAddon(
      $config['plugin_name'].'-addon-filename',
      $config['add_ons']['filename']['get_admin_js_path_filter'],
      'codeneric_img_addon_filename',
      $config['add_ons']['filename']['get_admin_frontend_globals'],
      $commonScripts,
      $config['version'],
    );
    // like add on
    $processAddon(
      $config['plugin_name'].'-addon-comments',
      $config['add_ons']['comments']['get_admin_js_path_filter'],
      'codeneric_img_addon_comments',
      $config['add_ons']['comments']['get_admin_frontend_globals'],
      $commonScripts,
      $config['version'],
    );

  }

  public static function handle_placeholder_image_upload(): void {

    $optionName = Configuration::get()['default_thumbnail_id_option_key'];

    $id = get_option($optionName, null);

    if (!is_null($id)) {

      $exists = wp_attachment_is_image((int) $id);
      // wp_die(var_dump($id));
      if ($exists)
        return;
    }

    // upload an attachment

    $imageUrl = plugin_dir_path(__FILE__).'/../assets/img/placeholder.png';
    $upload = wp_upload_bits(
      "phmm_project_placeholder.png",
      null,
      file_get_contents($imageUrl),
    );

    $wp_filetype = wp_check_filetype(basename($upload['file']), null);
    $wp_upload_dir = wp_upload_dir();

    $attachment = array(
      'guid' =>
        $wp_upload_dir['baseurl']._wp_relative_upload_path($upload['file']),
      'post_mime_type' => $wp_filetype['type'],
      'post_title' => preg_replace(
        '/\.[^.]+$/',
        '',
        basename($upload['file']),
      ),
      'post_content' => '',
      'post_status' => 'inherit',
    );
    $attach_id = wp_insert_attachment($attachment, $upload['file']);
    $attach_data =
      wp_generate_attachment_metadata($attach_id, $upload['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);

    // done jezuz

    if (is_int($attach_id)) {
      update_option($optionName, $attach_id);
    }

  }

  public static function get_general_admin_frontend_globals(
  ): \codeneric\phmm\type\to_js\admin_common {
    $config = Configuration::get();
    $res = shape(
      "ajax_url" => admin_url('admin-ajax.php'),
      "author_id" => Utils::get_current_user_id(),
      "locale" => (string) get_locale(),
      "wpps_url" => $config['wpps_url'],
      "canned_emails_supported_placeholders" => array_values(
        CannedEmailPlaceholders::getValues(),
      ),
      "canned_emails" => Settings::getCurrentSettings()['canned_emails'],
      "links" => shape(
        'new_project' => add_query_arg(
          array('post_type' => $config['project_post_type']),
          admin_url('post-new.php'),
        ),
        'new_client' => add_query_arg(
          array('post_type' => $config['client_post_type']),
          admin_url('post-new.php'),
        ),
        'settings' => add_query_arg(
          array(
            'post_type' => $config['client_post_type'],
            'page' => 'options',
          ),
          admin_url('edit.php'),
        ),
        'support' => add_query_arg(
          array(
            'post_type' => $config['client_post_type'],
            'page' => 'support',
          ),
          admin_url('edit.php'),
        ),
      ),
    );
    return $res;
  }

  public static function strip_wp_posts(
    array<\WP_Post> $posts,
  ): array<shape(
    "id" => int,
    "title" => string,
  )> {
    $whitelist = ['id', 'post_title'];

    return array_map(
      function($post) {
        return shape("id" => $post->ID, "title" => $post->post_title);
      },
      $posts,
    );

  }

  public static function get_settings_frontend_globals(
  ): \codeneric\phmm\type\to_js\admin_settings {
    $options = Settings::getCurrentSettings();

    $theme = get_template_directory();

    $template_file = $theme.'/'.$options['page_template'];
    $current_template_exists = file_exists($template_file);

    $pages = get_page_templates();

    $templates = array();
    foreach ($pages as $key => $value) {
      $arr = array("templatename" => $key, "filename" => $value);
      $templates[] = $arr;
    }

    $current_theme = wp_get_theme();

    $current_theme_name = $current_theme->offsetGet("Name");

    return shape(
      "templates" => $templates,
      "current_template_exists" => $current_template_exists,
      "current_theme_name" => $current_theme_name,
      "pages" => self::strip_wp_posts(get_pages()),
    );

  }
  public static function get_interactions_frontend_globals(
  ): \codeneric\phmm\type\to_js\admin_interactions {
    $options = Settings::getCurrentSettings();

    $theme = get_template_directory();

    $template_file = $theme.'/'.$options['page_template'];
    $current_template_exists = file_exists($template_file);

    $pages = get_page_templates();

    $templates = array();
    foreach ($pages as $key => $value) {
      $arr = array("templatename" => $key, "filename" => $value);
      $templates[] = $arr;
    }

    $current_theme = wp_get_theme();

    $current_theme_name = $current_theme->offsetGet("Name");

    $clientIDs = Client::get_all_ids();

    $clients = array_map(
      function($ID) {
        $client = Client::get_wp_user_from_client_id($ID);

        return shape(
          "id" => $ID,
          "name" => get_the_title($ID),
          "wp_user_name" =>
            is_null($client) ? null : $client->get("user_login"),
          "project_access" => Client::get_project_ids($ID),
        );

      },
      $clientIDs,
    );
    $project_ids = Project::get_all_ids();

    $projects = array_map(
      function($ID) {
        $post = get_post($ID);
        $title = "";
        if (!is_null($post))
          $title = $post->post_title;
        return shape(
          "id" => $ID,
          "name" =>
            $title === "" ? (self::POST_EMPTY_TITLE_FILL.' #'.$ID) : $title,
        );
      },
      $project_ids,
    );

    $project_ids_with_guest_access = [];
    foreach ($project_ids as $id) {
      $protec = Project::get_protection($id);
      if (!is_null($protec['password']) || !$protec['private']) {
        $project_ids_with_guest_access[] = $id;
      }
    }

    $clients[] = shape(
      'id' => 0,
      'name' => 'Guest',
      'project_access' => $project_ids_with_guest_access,
      'wp_user_name' => null,
    );
    return shape("clients" => $clients, "projects" => $projects);

  }

  public static function get_premium_page_globals(
  ): \codeneric\phmm\type\to_js\admin_premium_page {

    return shape(
      "id" => Utils::get_plugin_id(),
      "has_premium_extension" => Configuration::get()['has_premium_ext'],
    );
  }
  public static function get_support_page_globals(
  ): \codeneric\phmm\type\to_js\admin_support {

    $email = get_option('admin_email');
    if (!is_string($email))
      $email = "";

    $current_user = wp_get_current_user();
    $name = $current_user->user_firstname.' '.$current_user->user_lastname;

    return shape("admin_email" => $email, "admin_name" => $name);
  }

  public static function get_admin_client_frontend_globals(
  ): \codeneric\phmm\type\client\Populated {

    $id = get_the_ID();
    invariant(
      is_int($id),
      '%s',
      new Error('Post is not set', [tuple('id', $id)]),
    );
    $client = Client::get($id);

    invariant(
      !is_null($client),
      '%s',
      new Error('No client found with given id'),
    );

    return $client;

  }

  public static function get_addon_paths(
  ): shape(
    "captions" => ?string,
    "comments" => ?string,
    "filename" => ?string,
    "like" => ?string,
  ) {

    $captions = Utils::apply_filter_or(
      'codeneric/img/addon/captions/get_admin_js_path',
      [],
      null,
    );
    $comments = Utils::apply_filter_or(
      'codeneric/img/addon/comments/get_admin_js_path',
      null,
      null,
    );
    $filename = Utils::apply_filter_or(
      'codeneric/img/addon/filename/get_admin_js_path',
      null,
      null,
    );
    $like = Utils::apply_filter_or(
      'codeneric/img/addon/like/get_admin_js_path',
      null,
      null,
    );

    return shape(
      "captions" => $captions,
      "comments" => $comments,
      "filename" => $filename,
      "like" => $like,
    );
  }
  public static function get_admin_project_frontend_globals(
  ): ?\codeneric\phmm\type\project_to_admin {
    $id = self::get_the_id();

    if (is_null($id))
      return null;

    return Project::get_project_for_admin($id);
  }
  public static function get_admin_client_project_access_frontend_globals(
  ): \codeneric\phmm\type\to_js\admin_client_project_acccess {
    $id = get_the_ID();
    $posts = get_posts(
      array(
        'post_type' => Configuration::get()['project_post_type'],
        'post_status' => 'any',
        'numberposts' => -1,
      ),
    );

    $configurations = array();

    foreach ($posts as $key => $project) {
      $configurations[$project->ID] =
        Project::get_configuration($project->ID);
    }

    if (count($configurations) === 0)
      $configurations = null;

    return shape(
      'projects' => self::populateMissingPostTitle($posts),
      'configurations' => $configurations,
    );
  }

  private static function populateMissingPostTitle(
    array<\WP_Post> $posts,
  ): array<\WP_Post> {
    return array_map(
      function($post) {
        $post->post_title =
          $post->post_title === ""
            ? __(self::POST_EMPTY_TITLE_FILL).' #'.$post->ID
            : $post->post_title;

        return $post;
      },
      $posts,
    );
  }

  public static function hide_admin_bar(): void {

    $settings = Settings::getCurrentSettings();

    if ($settings['hide_admin_bar'] === true &&
        !current_user_can('edit_posts'))
      add_filter('show_admin_bar', '__return_false');
  }

  public static function change_title_placeholder(string $title): string {
    if (self::is_client_single_page())
      return __("Enter client name here", "phmm");

    if (self::is_project_single_page())
      return __("Enter project name here", "phmm");

    return $title;
  }
  private static function string_or_else(
    string $str,
    string $alternative,
  ): string {
    if (!is_string($str) || strlen($str) === 0)
      return $alternative;
    return $str;
  }

  public static function define_client_table_columns(
    string $column_name,
  ): array<string, string> {

    $cols = array(
      'cb' => '<input type="checkbox" />',
      'title' => __('Name'),
      'projects' => __('Projects', Configuration::get()['plugin_name']),
      'email' => __('Email'),
      'shortcode' => __('Shortcode'),
      'date' => __('Date'),
    );

    return $cols;
  }
  public static function define_project_table_columns(
  ): array<string, string> {

    $cols = array(
      'cb' => '<input type="checkbox" />',
      'title' => __('Gallery Name'),
      'categories' => __('Categories'),
      'shortcode' => __('Shortcode'),
      'date' => __('Date'),
    );

    return $cols;
  }

  public static function fill_project_columns(
    string $column,
    int $post_id,
  ): void {
    if ($column === "shortcode")
      echo
        "<code>[".
        \codeneric\phmm\base\frontend\Shortcodes::GALLERY.
        " id=\"".
        $post_id.
        "\"]</code>"
      ;
  }

  public static function fill_client_columns(
    string $column,
    int $post_id,
  ): void {
    $post = get_post($post_id);

    $emptyDash = "—";

    $editLink = get_edit_post_link($post_id);
    invariant(
      $post instanceof \WP_Post,
      '%s',
      new Error("Could not get post from post_id"),
    );
    invariant(
      is_string($editLink),
      '%s',
      new Error("Could not get post edit link from post_id"),
    );

    switch ($column) {
      case "shortcode":
        echo
          "<code>[".
          \codeneric\phmm\base\frontend\Shortcodes::CLIENT.
          " id=\"".
          $post_id.
          "\"]</code>"
        ;
        break;

      case "email":
        $user = Client::get_wp_user_from_client_id($post_id);

        $email = $user?->get('user_email');

        if (is_null($email) || $email === "")
          echo $emptyDash; else
          echo $email;

        break;
      case "projects":
        {
          $projects = Client::get_project_wp_posts($post_id);

          if (count($projects) === 0)
            echo $emptyDash; else
            foreach ($projects as $i => $project) {
              $editLink = get_edit_post_link($project->ID);
              invariant(
                is_string($editLink),
                '%s',
                new Error(
                  "Could not get project post edit link from post_id",
                ),
              );

              echo
                '<strong><a class="row-title post-edit-link" href="'.
                $editLink.
                '">'.
                self::string_or_else(
                  $project->post_title,
                  __('Unnamed project', 'phmm'),
                ).
                '</a></strong>'
              ;

              if ($i + 1 < count($projects))
                echo ", ";

            }

          break;
        }
    }
    return;
  }

  public static function warn_if_page_template_not_exists(): void {

    $settings = Settings::getCurrentSettings();

    $chosenTemplate = false;

    $chosenTemplate = $settings['page_template'];

    $theme = get_template_directory();
    $template_file = $theme.'/'.$chosenTemplate;

    if ($chosenTemplate !== 'phmm-legacy' &&
        $chosenTemplate !== 'phmm-theme-default' &&
        !file_exists($template_file)) {
      $editurl = admin_url("edit.php");
      $link = add_query_arg(
        array('post_type' => 'client', 'page' => 'options'),
        $editurl,
      );
      $class = 'notice notice-error';
      $message =
        __(
          'The chosen page template in Photography Management -> Settings does not exist. Have you changed your theme recently? <br />Please <a href="'.
          $link.
          '">update</a> the option.',
        );
      $submessage = __('');

      /* UNSAFE_EXPR */
      printf(
        '<div class="'.
        $class.
        '">
        <h3>Photography Management</h3>
        <p><strong>'.
        $message.
        '</strong></p><p>'.
        $submessage.
        '</p></div>',
      );
      /*'none' is default so no worries */
    }
  }

  public static function get_loading_spinner_html(): string {
    return
      "<div style=\"background:url('images/spinner.gif') no-repeat;background-size: 20px 20px;vertical-align: middle;margin: 0 auto;height: 20px;width: 20px;display:block;\"></div>";
  }
  public static function render_client_information_meta_box(): void {

    wp_nonce_field('my_meta_box_nonce', 'meta_box_nonce');
    echo
      '<div id="cc_phmm_client_information">'.
      self::get_loading_spinner_html().
      '</div>'
    ;
  }
  public static function render_client_project_access_meta_box(): void {
    wp_nonce_field('my_meta_box_nonce', 'meta_box_nonce');
    echo
      '<div id="cc_phmm_client_project_access">'.
      self::get_loading_spinner_html().
      '</div>'
    ;
  }

  public static function render_project_gallery_meta_box(): void {
    wp_nonce_field('my_meta_box_nonce', 'meta_box_nonce');
    echo
      '<div id="cc_phmm_project_gallery">'.
      self::get_loading_spinner_html().
      '</div>'
    ;
  }
  public static function render_project_configuration_meta_box(): void {
    wp_nonce_field('my_meta_box_nonce', 'meta_box_nonce');
    echo
      '<div id="cc_phmm_project_configuration">'.
      self::get_loading_spinner_html().
      '</div>'
    ;
  }
  public static function render_project_thumbnail_meta_box(): void {
    wp_nonce_field('my_meta_box_nonce', 'meta_box_nonce');
    echo
      '<div id="cc_phmm_project_thumbnail">'.
      self::get_loading_spinner_html().
      '</div>'
    ;
  }

  /* This file is buggy. The formatter of Hack always removes the last } which messes up the class definition  */
}
