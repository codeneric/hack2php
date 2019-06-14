<?php //strict
namespace codeneric\phmm\legacy;
use \codeneric\phmm\legacy\type\client_data_representation_3_6_5;
use \codeneric\phmm\legacy\type\project_data_representation_3_6_5;
use \codeneric\phmm\legacy\type\comment_data_representation_3_6_5;
use \codeneric\phmm\legacy\type\plugin_settings_data_representation_3_6_5;
use \codeneric\phmm\legacy\type\plugin_settings_representation_4_0_0;
use \codeneric\phmm\base\includes\Error;

function map_client_from_3_6_5(
$client_data_3_6_5,
$project_ids  // ): type\client_data_representation_4_0_0 {
){
  $client_data_4_0_0 = array();
  $client_data_4_0_0['project_access'] = [];
  foreach ($project_ids as $pid) {
    $client_data_4_0_0['project_access'][] = array(
      'id' => $pid,
      'active' => true,
      'configuration' => null,
    );
  }

  $login_name = $client_data_3_6_5['login_name'];
  $client_data_4_0_0['post_title'] = $client_data_3_6_5['full_name'];
\HH\invariant(!\is_null($login_name), '%s', new Error('cannot map this client!'));
  $client_data_4_0_0['user_login'] = $login_name;
  $client_data_4_0_0['email'] = $client_data_3_6_5['email'];
  $client_data_4_0_0['plain_pwd'] = $client_data_3_6_5['pwd'];
  $client_data_4_0_0['internal_notes'] = "Phone: ".
    $client_data_3_6_5['phone'].
    "\n".
    "Address: ".
    $client_data_3_6_5['address'];
  // $client_data_4_0_0['email'] = '';

  return $client_data_4_0_0;
}

function map_project_from_3_6_5(
$data_3_6_5,
$pwd,
$watermark){

  $data_4_0_0 = array();
  // $data_4_0_0['version'] = '4.0.0';
  $data_4_0_0['gallery'] = $data_3_6_5['gallery'];
  $data_4_0_0['protection'] = array(
    'private' => !\is_null($pwd),
    'password_protection' => !\is_null($pwd),
    'password' => $pwd,
    'registration' => null
  ); 

  $data_4_0_0['pwd'] = $pwd;
  $data_4_0_0['thumbnail'] =
    $data_3_6_5['thumbnail'] == 0 ? null : $data_3_6_5['thumbnail'];

  $data_4_0_0['configuration'] = array(
    'commentable' => $data_3_6_5['commentable'] === 'true',
    'disableRightClick' => $data_3_6_5['disableRightClick'] === 'true',
    'downloadable' => $data_3_6_5['downloadable'] === 'true',
    'downloadable_favs' => $data_3_6_5['downloadable_favs'] === 'true',
    'downloadable_single' => false,
    'favoritable' => $data_3_6_5['favoritable'] === 'true',
    'showCaptions' => $data_3_6_5['showCaptions'] === 'true',
    'showFilenames' => $data_3_6_5['showFilenames'] === 'true',
    'watermark' => $watermark,
  );

  return $data_4_0_0;
}

function map_comment_from_3_6_5(
$data_3_6_5,
$project_id,
$wp_user_id_of_client){

  $data_4_0_0 = array();

  $data_4_0_0['attachment_id'] = $data_3_6_5['attach_id'];
  $data_4_0_0['wp_user_id'] = $wp_user_id_of_client;

  $data_4_0_0['project_id'] = $project_id;
  $data_4_0_0['content'] = $data_3_6_5['content'];
  $data_4_0_0['client_id'] = $data_3_6_5['client_id'];
  $data_4_0_0['time'] = \date("Y-m-d H:i:s", $data_3_6_5['date']); 
  $data_4_0_0['wp_author_id'] = $data_3_6_5['user_id'];

  return $data_4_0_0;
}

function map_plugin_settings_from_3_6_5(
$data_3_6_5){

  $data_4_0_0 = array();

  $data_4_0_0['enable_slider'] = $data_3_6_5['cc_photo_image_box'] === 1;
  $data_4_0_0['slider_theme'] =
    \in_array($data_3_6_5['cc_photo_lightbox_theme'], ['light', 'dark'])
      ? $data_3_6_5['cc_photo_lightbox_theme']
      : 'dark';
  $data_4_0_0['page_template'] = $data_3_6_5['page_template'];
  $data_4_0_0['accent_color'] = "#0085ba";
  $data_4_0_0['hide_admin_bar'] = $data_3_6_5['hide_admin_bar'] === 1;
  $data_4_0_0['portal_page_id'] = $data_3_6_5['cc_photo_portal_page'];

  $er = \explode(",", $data_3_6_5['cc_email_recipient']);
  $er = \array_filter(
    $er,
    function($e) {
      return $e !== "";
    }  );
  $data_4_0_0['email_recipients'] = $er;
  $data_4_0_0['custom_css'] = $data_3_6_5['custom_css'];
  $data_4_0_0['max_zip_part_size'] = $data_3_6_5['max_zip_part_size'];
  $data_4_0_0['watermark'] = array(
    'image_id' => $data_3_6_5['watermark_image_id'],
    'scale' => $data_3_6_5['watermark_scale'],
    'position' => $data_3_6_5['watermark_position'],
  );

  $data_4_0_0['remove_images_on_project_deletion'] =
    $data_3_6_5['remove_images_on_project_deletion'] === 1;

  $data_4_0_0['canned_emails'] = [];
  $es = $data_3_6_5['canned_email_subject'];
  $ec = $data_3_6_5['canned_email'];
  if (!\is_null($es) && !\is_null($ec)) {
    $data_4_0_0['canned_emails'][] = array(
      'id' => "generated_".\time(),
      "display_name" => "Migrated template",
      "subject" => $es,
      "content" => $ec,
    );
  }

  return /* HH_IGNORE_ERROR[4110] */ $data_4_0_0; 
}
