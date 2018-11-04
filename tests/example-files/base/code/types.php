<?hh //strict

// namespace codeneric\phmm\type\to_js {

// }

namespace codeneric\phmm\type\privacy {
  type item = shape(
    'group_id' => string,
    'group_label' => string,
    'item_id' => string,
    'data' => array<
      shape(
        'name' => string,
        'value' => string,
      )
    >,
  );

  type exporter = shape(
    'exporter_friendly_name' => string,
    'callback' => array<string>,
  );
  type eraser = shape(
    'eraser_friendly_name' => string,
    'callback' => array<string>,
  );

}

namespace codeneric\phmm\type\configuration {

  type JSConfigurationI = shape(
    'admin' => shape(
      'common' => string,
      'client' => string,
      'project' => string,
      'premium_page' => string,
      'support_page' => string,
      'interactions_page' => string,
      'settings' => string,
      'migration' => string,
    ),
    'public' => shape(
      'common' => string,
      'client' => string,
      'project' => string,
    ),
  );
  type CssConfigurationI = shape(
    'admin' => shape(
      'post' => string,
      'fixes' => string,
      'custom' => string,
    ),
    'public' => shape('projects' => string),
  );

  type I = shape(
    'manifest_path' => string,
    'target' => string,
    'support_email' => string,
    'revision' => string,
    'env' => string,
    'wpps_url' => string,
    'landing_url' => string,
    'client_post_type' => string,
    'project_post_type' => string,
    'plugin_slug_abbr' => string,
    'premium_plugin_key' => string,
    'plugin_name' => string,
    'premium_plugin_name' => string,
    'has_premium_ext' => bool,
    'premium_ext_active' => bool,
    'version' => string,
    'assets' => shape(
      'js' => JSConfigurationI,
      'css' => CssConfigurationI,
      'crypto' => shape('pub_key' => string),
    ),
    'max_zip_part_size' => int,
    'update_check_cool_down' => int,
    'plugin_base_url' => string,
    'image_size_fullscreen' => string,
    'phmm_posts_logout' => string,
    'cookie_wp_postpass' => string,
    'client_user_role' => string,
    'default_thumbnail_id_option_key' => string,
    'ping_service_url' => string,
    'notification_cool_down' => int,
  );
  type ManifestI = shape(
    'admin.client.js' => string,
    'admin.migration.js' => string,
    'admin.commons.js' => string,
    'admin.premiumpage.js' => string,
    'admin.supportpage.js' => string,
    'admin.interactionspage.js' => string,
    'admin.premium.js' => string,
    'admin.project.js' => string,
    'admin.settings.js' => string,
    'public.client.js' => string,
    'public.commons.js' => string,
    'public.project.js' => string,
  );

}

namespace codeneric\phmm\type\Interactions {
  type Comment = shape(
    "project_id" => int,
    "image_id" => int,
  );

  type Label = shape(
    "project_id" => int,
    "label_id" => int,
    "label_name" => string,
    "set" => array<int>,
  );

  type t = shape(
    "comments" => array<Comment>,
    "labels" => array<Label>,
  );

}
namespace codeneric\phmm\type\client {

  type Stored = shape(
    'ID' => int,
    'wp_user' => ?int,
    'address' => ?string,
    'phone' => ?string,
    'project_access' => \codeneric\phmm\type\client_project_access,
    'internal_notes' => ?string,
    'canned_email_history' => array<\codeneric\phmm\type\canned_email_history>,
    'plain_pwd' => string,
  );

  type Populated = shape(
    'ID' => int,
    'wp_user' => ?\WP_User,
    // 'address' => ?string,
    // 'phone' => ?string,
    'project_access' => array<\codeneric\phmm\type\client_project_access>,
    'internal_notes' => ?string,
    'canned_email_history' => array<\codeneric\phmm\type\canned_email_history>,
    'plain_pwd' => ?string,
  );

}

namespace codeneric\phmm\type\to_js {
  type public_client = shape(
    'projects' => array<
      shape(
        'id' => int,
        'permalink' => string,
        'thumbnail' => ?\codeneric\phmm\type\image,
        'title' => string,
      )
    >,
  );

  type admin_settings = shape(
    'current_theme_name' => string,
    'current_template_exists' => bool,
    'templates' => array<array<string, string>>,
    'pages' => array<
      shape(
        "id" => int,
        "title" => string,
      )
    >,
  );

  type admin_interactions = shape(
    'clients' => array<
      shape(
        "id" => int,
        "name" => string,
        "wp_user_name" => ?string,
        "project_access" => array<int>,
      )
    >,
    'projects' => array<
      shape(
        'id' => int,
        'name' => string,
      )
    >,
  );

  type admin_premium_page = shape(
    'id' => string,
    'has_premium_extension' => bool,
  );
  type admin_support = shape(
    'admin_email' => string,
    'admin_name' => string,
  );

  type admin_common = shape(
    'ajax_url' => string,
    'locale' => string,
    'author_id' => int,
    // 'customer_id' => string,
    'wpps_url' => string,
    'canned_emails' => array<\codeneric\phmm\type\canned_email>,
    'canned_emails_supported_placeholders' =>
      array<\codeneric\phmm\enums\CannedEmailPlaceholders>,
    'links' => shape(
      'new_project' => string,
      'new_client' => string,
      'settings' => string,
      'support' => string,
    ),
  );

  type admin_client_project_acccess = shape(
    'projects' => array<\WP_Post>,
    'configurations' => ?array<int, \codeneric\phmm\type\configuration>,
  );

  type project_gallery = shape(
    'order' => array<int>,
    'preloaded' => array<\codeneric\phmm\type\image>,
  );

  type public_settings = shape(
    'accent_color' => string,
    'enable_slider' => bool,
    'slider_theme' => string,
  );
  type public_common = shape(
    'author_id' => int,
    'ajax_url' => string,
    'locale' => string,
    'logout_url' => ?string,
    'back_url' => ?string,
    'options' => public_settings, // "theme" => ?mixed,
  );

}

namespace codeneric\phmm\type {
  type canned_email_history = shape(
    'id' => string,
    'timestamp' => int,
  );
  type check_email = shape(
    'client_id' => int,
    'email' => string,
  );
  type check_username = shape('username' => string);
  type client_from_client = shape(
    'project_access' => array<client_project_access>,
    'post_title' => string,
    'user_login' => string,
    'email' => string,
    'internal_notes' => ?string,
    'plain_pwd' => ?string,
  );
  type client_project_access = shape(
    'id' => int,
    'active' => bool,
    'configuration' => ?configuration,
  );

  type client_to_db = shape(
    'project_access' => array<client_project_access>,
    'wp_user' => int,
    'internal_notes' => ?string,
    'plain_pwd' => ?string,
  );

  type configuration = shape(
    'commentable' => bool,
    'disableRightClick' => bool,
    'downloadable' => bool,
    'downloadable_favs' => bool,
    'downloadable_single' => bool,
    'favoritable' => bool,
    'showCaptions' => bool,
    'showFilenames' => bool,
    'watermark' => bool,
  );

  type fetch_images = shape(
    'IDs' => array<int>,
    'project_id' => ?int,
  );
  type get_canned_email_preview = shape(
    'client_id' => int,
    'email_id' => string,
  );
  type get_comments_count = shape(
    'project_id' => int,
    'client_id' => int,
  );
  type get_comments = shape(
    'project_id' => int,
    'client_id' => ?int,
    'attachment_id' => int,
  );
  type image = shape(
    'error' => ?bool,
    'filename' => string,
    'id' => int,
    'meta' => shape('caption' => ?string),
    'mini_thumb' => string,
    'sizes' => array<
      shape(
        'height' => int,
        'name' => string,
        'url' => string,
        'width' => int,
      )
    >,
  );
  type label_photo = shape(
    'label_id' => string,
    'photo_ids' => array<int>,
    'project_id' => int,
  );
  type post_comment = shape(
    'project_id' => int,
    'client_id' => ?int,
    'attachment_id' => int,
    'content' => string,
  );
  type project_from_admin = shape(
    'gallery' => array<int>,
    'protection' => project_protection,
    'pwd' => ?string,
    'thumbnail' => ?int,
    'configuration' => configuration,
  );
  type project_from_client = shape(
    'gallery' => string,
    'is_private' => bool,
    'pwd' => ?string,
    'thumbnail' => ?int,
    'configuration' => configuration,
  );
  type project_protection = shape(
    'private' => bool,
    'password_protection' => bool,
    'password' => ?string,
  );
  type project_to_admin = shape(
    'gallery' => array<int>,
    'protection' => project_protection,
    'pwd' => ?string,
    'id' => ?int,
    'thumbnail' => ?image,
    'configuration' => configuration,
  );
  type project_to_client = shape(
    'gallery' => shape(
      'order' => array<int>,
      'preloaded' => array<image>,
    ),
    'labels' => array<
      shape(
        'id' => string,
        'images' => array<int>,
      )
    >,
    'comment_counts' => array<
      shape(
        'image_id' => int,
        'count' => int,
      )
    >,
    'id' => int,
    'thumbnail' => ?image,
    'configuration' => configuration,
    'download_base_url' => string,
  );

  type project = shape(
    'gallery' => array<int>,
    'is_private' => bool,
    'pwd' => ?string,
    'thumbnail' => ?int,
    'configuration' => configuration,
  );
  type protected_file_request = shape(
    'f' => string,
    'project_id' => ?int,
    'attach_id' => ?int,
    'part' => int,
  );
  type send_canned_email = shape(
    'template_id' => string,
    'client_id' => int,
    'to' => string,
    'subject' => string,
    'message' => string,
  );
  type send_feedback = shape(
    'subject' => string,
    'email' => string,
    'content' => string,
    'topic' => string,
    'name' => string,
  );
  type update_premium = shape('bool' => bool);

  type watermark = shape(
    'image_id' => ?int,
    'scale' => ?int,
    'position' => ?string,
  );

  type get_interactions = shape("client_id" => int);

  type comment_count = shape(
    "image_id" => int,
    "count" => int,
  );

  type comment = shape(
    'id' => int,
    'attachment_id' => int,
    'wp_user_id' => int,
    'project_id' => int,
    'content' => string,
    'client_id' => int,
    'time' => string,
    'wp_author_id' => int,
  );

  type save_comment = shape(
    'attachment_id' => int,
    'wp_user_id' => int,
    'project_id' => int,
    'content' => string,
    'client_id' => int,
    'time' => ?string,
    'wp_author_id' => int,
  );
  //  type Project = shape(
  //   'gallery' => ProjectGallery,
  //   'labels' => array<label>,
  //   'comment_counts' => ?array<int, int>,
  //   'id' => int,
  //   'thumbnail' => ?\codeneric\phmm\type\project\Image,
  //   'configuration' => \codeneric\phmm\type\project\Configuration,
  // );

  // type ProjectGallery = shape(
  //     'order' => array<int>,
  //     'preloaded' => ?\codeneric\phmm\type\project\ProjectGalleryI,
  //   );
  // type ProjectGalleryI = array<int, Image>;

  // type label = shape(
  //     "id" => string,
  //     "images" => array<int>,
  //   );

  // type plugin_settings = shape(
  //   'cc_photo_image_box' => bool,
  //   'cc_photo_enable_styling' => bool,
  //   'cc_photo_lightbox_theme' => string,
  //   'page_template' => string,
  //   'accent_color' => string,
  //   'hide_admin_bar' => bool,
  //   'cc_photo_portal_page' => ?int,
  //   'email_recipients' => array<string>,
  //   'custom_css' => string,
  //   'max_zip_part_size' => int,
  //   'watermark' => shape(
  //     'image_id' => ?int,
  //     'scale' => ?int,
  //     'position' => ?string,
  //   ),
  //   'remove_images_on_project_deletion' => bool,
  //   'canned_emails' => array<canned_email>,
  // );

  type plugin_settings = shape(
    'enable_slider' => bool,
    'slider_theme' => string,
    'page_template' => string,
    'accent_color' => string,
    'hide_admin_bar' => bool,
    'portal_page_id' => ?int,
    'email_recipients' => array<string>,
    'custom_css' => string,
    'max_zip_part_size' => int,
    'watermark' => watermark,
    'remove_images_on_project_deletion' => bool,
    'canned_emails' => array<canned_email>,
  );

  type canned_email = shape(
    'id' => string,
    'display_name' => string,
    'subject' => string,
    'content' => string,
  );

  type get_download_zip_parts = shape(
    'project_id' => int,
    'client_id' => ?int,
    'mode' => string,
  );

  type get_proofing_csv = shape(
    'project_id' => int,
    'client_id' => int,
  );

  type event = shape(
    'type' => string,
    'project_id' => int,
    'client_id' => int,
  );

  type get_original_image_url_request =
    shape('project_id' => int, 'image_id' => int);

}
