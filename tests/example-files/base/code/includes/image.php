<?hh //strict

namespace codeneric\phmm\base\includes;
use codeneric\phmm\Utils;
use codeneric\phmm\Logger;
class Image {

  public static function get_image(
    int $id,
    bool $use_minithumb = false,
    array<string, string> $query_args = array(),
  ): ?\codeneric\phmm\type\image {

    // $t0 = Utils::time();

    if (get_post_type($id) !== 'attachment') {
      return null;
    }

    $query_args['attach_id'] = (string) $id;

    $imagedata = wp_get_attachment_metadata($id);

    if (!is_array($imagedata))
      return null;

    $image = get_post($id);

    $meta = shape("caption" => is_null($image) ? null : $image->post_excerpt);

    //   array_key_exists('imagedata', $imagedata)
    //     ? $imagedata['imagedata']
    //     : array();

    // $image = get_post($id);
    // if (!is_null($image)) {
    //   $meta['caption'] = $image->post_excerpt;
    // }
    $uncropped_sizes = Utils::get_uncropped_image_sizes();
    $uncropped_sizes_names = array_keys($uncropped_sizes);
    $sizes =
      array_key_exists('sizes', $imagedata) ? $imagedata['sizes'] : array();

    // $sizes = is_array($sizes) ? $sizes : array();

    $mapped_sizes = array();
    $available_uncropped_sizes =
      array_intersect($uncropped_sizes_names, array_keys($sizes));
    $no_uncropped_sizes_availalbe = count($available_uncropped_sizes) === 0;

    // $t1 = Utils::time();

    foreach ($sizes as $size_name => $size) {
      $s = wp_get_attachment_image_src($id, $size_name);

      if (is_array($s) &&
          ($no_uncropped_sizes_availalbe ||
           in_array($size_name, $uncropped_sizes_names))) {
        $url = (string) add_query_arg($query_args, $s[0]);
        $url = Utils::get_protocol_relative_url($url);
        $mapped_sizes[] = shape(
          'url' => $url,
          'width' => (int) $size['width'], //$s holds the wrong dimensions
          'height' => (int) $size['height'],
          'name' => (string) $size_name,
        );
      }
    }
    $t2 = Utils::time();

    // $t2 = Utils::time();

    $filename = basename(get_attached_file($id, true));

    $mini_thumb_b64 = $use_minithumb ? self::get_minithumb($id) : null;

    $image = shape(
      'sizes' => $mapped_sizes,
      'filename' => $filename,
      'id' => (int) $id,
      'meta' => $meta,
      'mini_thumb' => (string) $mini_thumb_b64,
      'error' => false,
    );
    // $t3 = Utils::time();

    // Logger::debug("total time: ".($t3 - $t0));
    // Logger::debug("foreach time: ".($t2 - $t1));
    // Logger::debug("used memory: ".(memory_get_usage(false)/1000000 ));
    return $image;
  }

  static function get_minithumb(int $id): ?string {
    $imagedata = wp_get_attachment_metadata($id);

    if (!is_array($imagedata))
      return null;

    $filename = false;
    $medium_exists = \codeneric\phmm\Utils::array_nested_key_exist(
      'sizes.medium.file',
      $imagedata,
    );
    $thumb_exists = \codeneric\phmm\Utils::array_nested_key_exist(
      'sizes.thumbnail.file',
      $imagedata,
    );

    if ($medium_exists || $thumb_exists) {
      $size_name = $medium_exists ? 'medium' : 'thumbnail'; //prefer medium over thumbnail
      $filename = $imagedata['sizes'][$size_name]['file'];

      $o_path = get_attached_file($id, true);
      $filename = dirname($o_path)."/$filename";

      if (!file_exists($filename))
        return null;
    }

    if ($filename === false)
      return null;

    list($width, $height, $image_type) = getimagesize($filename);
    $b64_path = dirname($filename)."/$id.b64";
    if (!file_exists($b64_path)) {
      $newwidth = min(20, intval(20 * ($width / $height)));
      $newheight = min(20, intval(20 * ($height / $width)));

      $minithumb = imagecreatetruecolor($newwidth, $newheight);
      $image_type_str = null;
      $source = null;

      switch ($image_type) {
        case IMAGETYPE_GIF:
          $source = imagecreatefromgif($filename);
          break;
        case IMAGETYPE_JPEG:
          $source = imagecreatefromjpeg($filename);
          $image_type_str = 'jpeg';
          break;
        case IMAGETYPE_PNG:
          $source = imagecreatefrompng($filename);
          $image_type_str = 'png';
          break;
        default:
          break;
      }

      if ($source === null || $image_type_str === null)
        return null;

      imagecopyresized(
        $minithumb,
        $source,
        0,
        0,
        0,
        0,
        $newwidth,
        $newheight,
        $width,
        $height,
      );

      ob_start();
      imagejpeg($minithumb);
      $img = ob_get_clean();
      $b64 = "data:image/$image_type_str;base64,".base64_encode($img);

      imagedestroy($minithumb);
      imagedestroy($source);

      $fp = fopen($b64_path, 'w');
      fwrite($fp, $b64);
      fclose($fp);

      return $b64;

    } else {
      $fp = fopen($b64_path, 'r');
      $res = fread($fp, filesize($b64_path));
      fclose($fp);
      return $res;
    }
  }

  /**
   * Delete the attachement post and file. It does not remove any references in other posts.
   *
   * @since      4.0.0
   * @author     Codeneric <support@codeneric.com>
   */
  static function delete(int $image_id): void {
    wp_delete_attachment($image_id, true);
  }

  static function get_original_image_url(
    int $id,
    array<string, string> $query_args = [],
  ): ?shape("url" => string) {
    $query_args['attach_id'] = "$id";
    // returns (false|array): Returns an array (url, width, height, is_intermediate), or false, if no image is available.
    $img_arr = \wp_get_attachment_image_src($id, 'full');
    if ($img_arr === false)
      return null;
    invariant(
      \is_array($img_arr) &&
      \count($img_arr) >= 4 &&
      \is_string($img_arr[0]),
      '%s',
      new Error("Bad format of wp_get_attachment_image_src return."),
    );

    $url = (string) \add_query_arg($query_args, $img_arr[0]);
    $url = Utils::get_protocol_relative_url($url);

    return shape('url' => $url);

  }

  // auhr
}
