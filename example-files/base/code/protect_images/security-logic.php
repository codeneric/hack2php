<?hh //strict
/**
 * Created by PhpStorm.
 * User: denis_000
 * Date: 25.04.2016
 * Time: 19:59
 */

namespace codeneric\phmm\base\protect_images;
use codeneric\phmm\base\globals\Superglobals;
use codeneric\phmm\base\includes\Project;
use codeneric\phmm\base\includes\Client;
use codeneric\phmm\Utils;
use codeneric\phmm\base\includes\Permission;
use codeneric\phmm\base\includes\Error;
use codeneric\phmm\enums\UserState;
use codeneric\phmm\enums\ProjectState;

require_once dirname(__FILE__).'/ZipStream/ZipStream.plain.php';

class Main {

  public static function provide_file(
    string $f,
    string $filename,
    ?int $project_id,
    int $part = 0,
  ): void {

    while (ob_get_level() > 0) {
      ob_end_clean();
    }

    if ($f === 'zip-all' || $f === 'zip-favs') {

      //ini_set('memory_limit', '3M');
      // $part = $this->part;

      $current_user = wp_get_current_user();
      $user_id = $current_user !== false ? $current_user->ID : 0;

      // $hook_args = array(
      //   'client' => $this->client_id,
      //   'project' => $this->project_id,
      //   'user' => $user_id,
      //   'type' => 'zip-download',
      // );

      // do_action('codeneric/phmm/statistics/event', $hook_args);

      $dir = dirname(__FILE__); //.../protect-images

      invariant(
        is_int($project_id),
        '%s',
        new Error('Cannot download zip without a project_id!'),
      );

      $total_parts = 0;

      $files = array();
      if ($f === 'zip-favs') {
        $client = Client::get_current();
        $client_id = !is_null($client) ? $client['ID'] : 0;
        $files = Project::get_zip_batch($project_id, $f, $part, $client_id);

        $total_parts =
          Project::get_number_of_zip_parts($project_id, $f, $client_id);
      } else if ($f === 'zip-all') {
        $total_parts =
          Project::get_number_of_zip_parts($project_id, $f, null);
        $files = Project::get_zip_batch($project_id, $f, $part, null);
      }
      //get only requested part

      # create a new zipstream object
      $display_part = $part + 1;
      $title = get_the_title($project_id);
      $zip = new \Photography_Management_Base_ZipStream(
        "$title ($display_part of $total_parts)".'.zip',
        array('large_file_size' => 1),
      );
      http_response_code(200);
      foreach ($files as $file) {
        $zip->addFileFromPath(basename($file), $file);
      }
      $zip->finish();

      die();
    }

    //        $options = get_option('cc_photo_settings', array());
    $apply_watermark = false;
    $access_config = null;
    $client = Client::get_current();
    if (!is_null($client) && is_int($project_id)) {
      $client_id = $client['ID'];
      $access_config =
        Client::get_project_configuration($client_id, $project_id);

    }

    if (is_null($access_config) && is_int($project_id)) {
      $access_config = Project::get_configuration($project_id);
    }
    $apply_watermark =
      !is_null($access_config) ? $access_config['watermark'] : false;

    if ($apply_watermark &&
        getimagesize($filename) !== false &&
        has_action('codeneric/phmm/watermark')) { //image requested

      $settings = \codeneric\phmm\base\admin\Settings::getCurrentSettings();
      $wms = $settings['watermark'];
      if (!is_null($wms['image_id']) &&
          !is_null($wms['position']) &&
          !is_null($wms['scale'])) {
        $args = array('file' => $filename, 'wms' => $wms);
        do_action('codeneric/phmm/watermark', $args);
        die();
      }

    }

    $file = @fopen($filename, 'rb');
    $buffer = 1024 * 8;
    $mime = 'image/xyz';
    if (function_exists('mime_content_type')) {
      $mime = mime_content_type($filename);
    }
    header('Content-Description: File Transfer');
    //        header('Content-Type: application/octet-stream');
    //        header('Content-Type: image/xyz');
    header("Content-Type: $mime");
    //        header('Content-Disposition: attachment; filename="'.basename($filename).'"');
    header('Content-Disposition: inline; filename="'.basename($filename).'"');
    http_response_code(200);
    while (!feof($file)) {
      echo fread($file, $buffer);
      flush();
    }
    fclose($file);
    die();
  }

  public static function file_belongs_to_attachment(
    string $url,
    int $attach_id,
  ): bool {
    $attach_url = wp_get_attachment_url($attach_id);
    if ($attach_url === $url)
      return true;
    $sizes = Utils::get_intermediate_image_sizes();
    foreach ($sizes as $size) {
      $data = wp_get_attachment_image_src($attach_id, $size);
      //            if($data === false)return false;
      if (is_array($data) &&
          Utils::get_protocol_relative_url($data[0]) === Utils::get_protocol_relative_url(
            $url,
          ))
        return true;
    }
    return false;

  }

  private static function user_can_access_project(int $project_id): bool {
    $project_state = Permission::get_project_state($project_id);
    $client_state =
      Permission::get_client_state_wrt_project($project_state, $project_id);
    if ($project_state === ProjectState::Public_) {
      return true;
    }
    return in_array($client_state, [UserState::Client, UserState::Guest]);
  }

  public static function user_can_access_file(
    string $f,
    ?int $attach_id,
    ?int $project_id,
  ): bool {
    //at this point, the user is permitted to access the specified client and project!
    if (Utils::is_current_user_admin())
      return true;
    invariant(
      is_int($attach_id),
      '%s',
      new Error(
        'Current user is not an admin and the attach_id is not defnied, but only admins can load files without setting the project parameter.',
      ),
    );
    invariant(
      is_int($project_id),
      '%s',
      new Error(
        'Current user is not an admin and the project_id is not defnied, but only admins can load files without setting the project parameter.',
      ),
    );

    $can_access = self::user_can_access_project($project_id);

    if (!$can_access)
      return false;

    $config = null;
    $current_client = Client::get_current();
    if (!is_null($current_client)) {
      $config =
        Client::get_project_configuration($current_client['ID'], $project_id);
    }

    if (is_null($config)) {
      $config = Project::get_configuration($project_id);
    }

    if ($f === 'zip-all' || $f === 'zip-favs') {

      $can_download_all = !($f === 'zip-all') || $config['downloadable'];
      $can_download_favs =
        !($f === 'zip-favs') || $config['downloadable_favs'];
      return $can_download_all && $can_download_favs;
    } else {
      $upload_url = wp_upload_dir();
      $upload_url = $upload_url['baseurl'];
      $attach_url = "$upload_url/photography_management/".$f;

      $fbta = self::file_belongs_to_attachment($attach_url, $attach_id);
      if (!$fbta)
        return false;
      $g = Project::get_gallery_image_ids($project_id);
      foreach ($g as $img) {
        if ($img === $attach_id) {
          return true;
        }
      }
      return false;
    }

  }

  private function is_exposed_cover_image(): bool {
    // $options = get_option('cc_photo_settings', array());

    // if(!empty($options['expose_cover_images'])){ // we have to check if the requested file is a cover image
    //     $project = $this->project;
    //     if(!isset($project))return false; //project does not exist, bail out
    //     if(isset($project['thumbnail']) && $project['thumbnail']['id'] === $this->attach_id){
    //         return true; // the requested file is the cover image
    //     }elseif(!isset($project['thumbnail']) && isset($project['gallery']) && isset($project['gallery'][0]) && $project['gallery'][0] === $this->attach_id){
    //         return true; //the requested file is the default cover image
    //     }
    //     return false; //non of the 2 whitelisted situation is applicable
    // }else{
    //     return false; //user does not want to expose cover images
    // }
    return true;
  }

  public static function user_is_permitted(
    string $f,
    ?int $attach_id,
    ?int $project_id,
  ): bool {
    $user_can_access_file =
      self::user_can_access_file($f, $attach_id, $project_id);
    // $is_exposed_cover_image = $this->is_exposed_cover_image();
    $is_exposed_cover_image = false;
    return ($user_can_access_file) || $is_exposed_cover_image;
  }

}
