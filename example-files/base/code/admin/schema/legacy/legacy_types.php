<?hh //strict

namespace codeneric\phmm\legacy\type;

// 4.0.0

type project_data_representation_4_0_0 = shape(
  'version' => ?string,
  'gallery' => array<int>,
  'protection' => shape(
    'private' => bool,
    'password_protection' => bool,
    'password' => ?string,
  ),
  'post_password' => ?string,
  'thumbnail' => ?int,
  'configuration' => shape(
    'commentable' => bool,
    'disableRightClick' => bool,
    'download_favs' => ?string,
    'downloadable' => bool,
    'downloadable_favs' => bool,
    'downloadable_single' => bool,
    'favoritable' => bool,
    'showCaptions' => bool,
    'showFilenames' => bool,
    'watermark' => bool,
  ),
);

type client_data_representation_4_0_0 = shape(
  'project_access' => array<shape(
    'id' => int,
    'active' => bool,
    'configuration' => ?shape(
      'commentable' => bool,
      'disableRightClick' => bool,
      'download_favs' => ?string,
      'downloadable' => bool,
      'downloadable_favs' => bool,
      'downloadable_single' => bool,
      'favoritable' => bool,
      'showCaptions' => bool,
      'showFilenames' => bool,
      'watermark' => bool,
    ),
  )>,
  'post_title' => string,
  'user_login' => string,
  'email' => string,
  'internal_notes' => ?string,
  'plain_pwd' => ?string,
);

type plugin_settings_data_representation_4_0_0 = shape(
  'cc_photo_image_box' => bool,
  'cc_photo_enable_styling' => bool,
  'cc_photo_lightbox_theme' => string,
  'page_template' => string,
  'accent_color' => string,
  'hide_admin_bar' => bool,
  'cc_photo_portal_page' => ?int,
  'email_recipients' => array<string>,
  'custom_css' => string,
  'max_zip_part_size' => int,
  'watermark' => shape(
    'image_id' => ?int,
    'scale' => ?int,
    'position' => ?string,
  ),
  'remove_images_on_project_deletion' => bool,
  'canned_emails' => array<shape(
    'id' => string,
    'display_name' => string,
    'subject' => string,
    'content' => string,
  )>,
);

// 3.6.5

type client_data_representation_3_6_5 = shape(
  'full_name' => string,
  'email' => string,
  'wp_user_id' => ?int,
  'show_on_page' => ?string,
  'login_name' => ?string,
  'pwd' => ?string,
  'additionalInfo' => ?string,
  'phone' => string,
  'address' => string,
);

type project_data_representation_3_6_5 = shape(
  'title' => string,
  'description' => string,
  'gallery' => array<int>,
  'starred' => array<int>,
  'favoritable' => string,
  'downloadable' => string,
  'disableRightClick' => string,
  'showCaptions' => string,
  'showFilenames' => string,
  'statusText' => string,
  'status' => string,
  'id' => ?string,
  'commentable' => string,
  'downloadable_favs' => string,
  'thumbnail' => int,
  'download_favs' => ?string,
  'url' => ?string,
);

type comment_data_representation_3_6_5 = shape(
  'client_id' => int,
  'author' => string,
  'author_email' => string,
  'content' => string,
  'user_id' => int,
  'author_IP' => string,
  'agent' => string,
  'date' => int,
  'approved' => int,
  'attach_id' => int,
);

type plugin_settings_data_representation_3_6_5 = shape(
  'cc_photo_image_box' => int,
  'cc_photo_lightbox_theme' => string,
  'page_template' => string,
  'watermark' => string,
  'watermark_image_id' => ?int,
  'watermark_position' => ?string,
  'watermark_scale' => ?int,
  'hide_admin_bar' => int,
  'canned_email_subject' => ?string,
  'canned_email' => ?string,
  'cc_photo_portal_page' => ?int,
  'cc_email_recipient' => string,
  'custom_css' => string,
  'max_zip_part_size' => int,
  'remove_images_on_project_deletion' => int,
);

// array (
//   'cc_photo_image_box' => '1',
//   'cc_photo_lightbox_theme' => 'dark',
//   'page_template' => 'phmm-theme-default',
//   'watermark' => 'on',
//   'watermark_image_id' => '4',
//   'watermark_position' => 'center',
//   'watermark_scale' => '10',
//   'hide_admin_bar' => '1',
//   'canned_email_subject' => 'subject',
//   'canned_email' => 'Dear [client-name],  [username], [password]',
//   'cc_photo_portal_page' => '2',
//   'cc_photo_portal_page_old' => '',
//   'cc_email_recipient' => 'test@test.com',
//   'custom_css' => 'some fancy css',
//   'max_zip_part_size' => '4000',
//   'remove_images_on_project_deletion' => '1',
// )
