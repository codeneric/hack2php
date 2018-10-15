<?php //strict
namespace codeneric\phmm\base\admin\ajax;

use \codeneric\phmm\base\includes\Labels;
use \codeneric\phmm\base\includes\Client;
use \codeneric\phmm\base\includes\Email;
use \codeneric\phmm\base\includes\Image;
use \codeneric\phmm\base\includes\Project;
use \codeneric\phmm\base\globals\Superglobals;
use \codeneric\phmm\base\includes\Error;
use \codeneric\phmm\Utils;
use \codeneric\phmm\Configuration;
use \codeneric\phmm\base\admin\CannedEmail\Handler as CannedEmail;
use \codeneric\phmm\type as Type;
use \codeneric\phmm\base\includes\Permission;

class Endpoints extends Request {

  /**
   * Method for labeling images. Validates the request. Checks if the client has access to given project. If so, saves the images set, otherwise error.
   */
  public static function label_images(){

    $request = self::getPayload();
    $request = \codeneric\phmm\validate\label_photo($request);

    $permitted_to_access_project =
      Permission::current_user_can_access_project($request['project_id']);

    if (!$permitted_to_access_project) {
      self::rejectInvalidRequest('Current user cannot access this project!');
      return null;
    }

    $clientID = Permission::get_client_id_wrt_project($request['project_id']);
\HH\invariant(      !is_null($clientID),
      '%s',
      new Error(
        'Something went wrong, your are permitted to access the project, but do not have a client_id!'      ));

    // if ($userID === 0) {
    //   self::rejectInvalidRequest('Only logged in user can label images');
    //   return null;
    // }

    // $clientID = Client::get_client_id_from_wp_user_id($userID);
    // if (is_null($clientID)) {
    //   self::rejectInvalidRequest(
    //     'Logged in user is not attached to any client',
    //   );
    //   return null;
    // }

    // $legitimate_request =
    //   Client::has_access_to_project($clientID, $request['project_id']);
    // if (!$legitimate_request) {
    //   self::rejectInvalidRequest(
    //     'Given client does not have access to this project',
    //   );
    //   return null;
    // }

    if (!\codeneric\phmm\base\includes\Labels::label_exists(
          $request['label_id']        )) {
      self::rejectInvalidRequest('Given label does not exist');
      return null;
    }

    $successful = Labels::save_set(
      $clientID,
      $request['project_id'],
      $request['label_id'],
      $request['photo_ids']    );

    if ($successful) {
      $make_event = function($event) {
        return $event;
      };
      $event = $make_event(
        array(
          'type' => 'updated_labels',
          'client_id' => $clientID,
          'project_id' => $request['project_id'],
        )      );
      do_action("codeneric/phmm/label_images_notification", $event);
      return self::resolveValidRequest(true);
      // return self::resolveValidRequest($request['photo_ids']);
    }

    self::rejectInvalidRequest("Failed to save favorites", 500);

    return null;
  }

  public static function check_username(
$t  ){

    $request = self::getPayload();
    $request = \codeneric\phmm\validate\check_username($request);

    $username = sanitize_user($request['username']);
    $valid = strlen($username) > 0;
    if ($valid) {
      $valid = validate_username($username);
      if (!$valid)
        $valid = Helper::validate_username_fallback($username); //better check one more time
      // username_exists only returns bool on failure
      $valid = $valid && is_bool(username_exists($username));
    }

    self::resolveValidRequest($valid);
    return null;

    // if ($valid)
    //   return self::resolveValidRequest($valid); else {
    //   self::rejectInvalidRequest("Username invalid or taken.");
    //   return null;
    // }
  }

  public static function update_premium($t){
    $request = self::getPayload();
    $request = \codeneric\phmm\validate\update_premium($request);

    update_option('cc_prem', $request['bool']);
    delete_option('__temp_site_transiant_54484886');
    self::resolveValidRequest(true);

  }

  public static function check_email(){

    $request = self::getPayload();
    $request = \codeneric\phmm\validate\check_email($request);

    $email = sanitize_email($request['email']);

    if (!is_email($email)) {
      self::rejectInvalidRequest("Invalid email", 200);
      return false;
    }

    $user = get_user_by('email', $email);

    // get_user_by returned false, therefore the email is free

    if (is_bool($user)) {
      return self::resolveValidRequest(true);
    } else {
\HH\invariant(        $user instanceof \WP_User,
        '%s',
        new Error("user should exist in this scope"));

      $id = Client::get_client_id_from_wp_user_id($user->ID);

      if (is_null($id))
        return self::resolveValidRequest(false);

      if ($id === $request['client_id'])
        return self::resolveValidRequest(true);
    }

    return self::resolveValidRequest(false);
  }

  public static function fetch_gallery_images(){
    $request = self::getPayload();
    $request = \codeneric\phmm\validate\fetch_images($request);

    $map = function($ID) use ($request) {
      $pid = $request['project_id'];
      $query_args = [];
      if (!is_null($pid))
        $query_args = ['project_id' => "$pid"];
      $image = \codeneric\phmm\base\includes\Image::get_image(
        $ID,
        true,
        $query_args      );
      if (is_array($image))
        return $image;
      // TODO: make this error handling better
      return array('id' => $ID, 'error' => true);
    };

    $result = array_map($map, $request['IDs']);

    return self::resolveValidRequest($result);
  }

  public static function send_feedback(){

    $request = self::getPayload();
    $request = \codeneric\phmm\validate\send_feedback($request);

    $config = Configuration::get();

    $to = $config['support_email'];

    $subject = sanitize_text_field($request['subject']);

    $headers = [
      'From: "'.$request['name'].'" <'.sanitize_email($request['email']).'>',
    ];

    $message = sanitize_text_field($request['content']);

    // $meta = "---------- START META DATA ----------".PHP_EOL;
    $meta_payload = [];
    $meta_payload['product'] = $config["plugin_name"];
    $meta_payload['product_version'] = $config["version"];
    $meta_payload['plugin_id'] = Utils::get_plugin_id();
    $meta_payload['topic'] = $request['topic'];
    $crypted = '';

    if (function_exists('openssl_public_encrypt')) {
      $pub_key = file_get_contents($config["assets"]["crypto"]["pub_key"]);
      openssl_public_encrypt(json_encode($meta_payload), $crypted, $pub_key);
      $s = Utils::get_temp_file('support_medatada_');
      $resource = $s['resource'];
      $name = $s['name'];
      fwrite($resource, $crypted);
      $mail_attachments = [$name];

      $success =
        wp_mail($to, $subject, $message, $headers, $mail_attachments);

      Utils::close_and_delete_file($resource, $name);
    } else {
      $success = wp_mail($to, $subject, $message, $headers);
    }

    // wp_die(var_dump($success));
    self::resolveValidRequest($success);

  }

  public static function get_interactions($t){

    if (!Utils::is_current_user_admin())
      self::rejectInvalidRequest("This is an admin-only endpoint");

    // $request = self::getRequest($t, Schemas::getInteractions);
    $request = self::getPayload();
    $request = \codeneric\phmm\validate\get_interactions($request);

    $projects = Client::get_project_ids($request['client_id']);

    $populated =
      array_map(
        function($projectID) use ($request) {

          $labels = Labels::get_all_labels();

          $interactionLabels = [];

          foreach ($labels as $label) {

            $set = Labels::get_set(
              $request['client_id'],
              $projectID,
              $label['id']            );

            $interactionLabels[] = array(
              "project_id" => $projectID,
              "label_id" => $label['id'],
              "label_name" => $label['name'],
              "set" => $set,
            );

          }
          $comments = Utils::apply_filter_or(
            "codeneric/phmm/get_comment_counts",
            array(
              "client_id" => $request['client_id'],
              "project_id" => $projectID,
            ),
            []          );
          $comments = array_map(
            function($comment) use ($projectID, $request) {
              return array_merge(
                $comment,
                [
                  "project_id" => $projectID,
                  "client_id" => $request['client_id'],
                  "image" => Image::get_image($comment['image_id'], true),
                ]              );

            },
            $comments          );
          return array(
            "labels" => $interactionLabels,
            "comments" => $comments,
          );

        },
        $projects      );

    // die(var_dump($populated));
    $abc = array_reduce(
      $populated,
      function($carry, $item) {

        $carry['comments'] =
          array_merge($carry['comments'], $item['comments']);
        $carry['labels'] = array_merge($carry['labels'], $item['labels']);
        return $carry;
      },
      array("comments" => [], "labels" => [])    );

    self::resolveValidRequest($abc);

    // $s = shape(
    //   "comments" => [shape(
    //     "project_id" => 42,
    //     "project_id" => 42,
    //   )]
    // );

  }

  public static function get_download_zip_parts(){
    $r = self::getPayload();
    $r = \codeneric\phmm\validate\get_download_zip_parts($r);

    self::resolveValidRequest(
      Project::get_number_of_zip_parts(
        $r['project_id'],
        $r['mode'],
        $r['client_id']      )    );
  }

  public static function get_original_image_url(){
    //UNSAFE
    $r = self::getPayload();
    $r = \codeneric\phmm\validate\get_original_image_url_request($r);
    $id = $r['image_id'];
    $query_args = ['project_id' => $r['project_id'] ];
    // $request = \codeneric\phmm\validate\update_premium($request);
    $img_url = Image::get_original_image_url($id, $query_args); 
    
    self::resolveValidRequest( $img_url); 

  }

}
