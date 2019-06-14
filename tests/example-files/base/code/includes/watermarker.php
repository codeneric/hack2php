<?php //strict
namespace codeneric\phmm\base;

class Watermarker {
  public static function watermark_image(
$args  ){

    //        ini_set("log_errors", 1);
    //        ini_set("error_log", dirname(__FILE__)."/php-error.log");
    // $settings = \codeneric\phmm\base\admin\Settings::getCurrentSettings();
    // $options = $settings['watermark'];
    $filename = $args['file'];
    $options = $args['wms'];
    list($w_img, $h_img, $image_type) = \getimagesize($filename);

    $default_watermark_path =
      \dirname(__FILE__).'/../assets/img/placeholder.png';
    $watermark_image_id =
      \is_null($options['image_id']) ? -1 : $options['image_id'];
    $watermark_position =
      \is_null($options['position']) ? 'center' : $options['position'];
    $watermark_scale = \is_null($options['scale']) ? 20 : $options['scale'];
\HH\invariant(!\is_null($watermark_scale), '%s', 'watermark_scale is null!');

    $position_map = array(
      'center' => array('h' => 0.5, 'v' => 0.5),
      'left_bottom' => array('h' => 0.1, 'v' => 0.9),
      'right_bottom' => array('h' => 0.9, 'v' => 0.9),
      'left_top' => array('h' => 0.1, 'v' => 0.1),
      'right_top' => array('h' => 0.9, 'v' => 0.1),
    );

    $watermark_path = \get_attached_file($watermark_image_id, true);
    if ($watermark_path === '') {
      $watermark_path = $default_watermark_path;
    }

    $input_image_is_watermark_itself = $watermark_path === $filename; //not very accurate, might be resized watermark

    list($w_watermark, $h_watermark) = \getimagesize($watermark_path);

    //        $pos_h = get_option('codeneric/phmm/watermark/pos-h', 0.5); //50% is default
    //        $pos_v = get_option('codeneric/phmm/watermark/pos-v', 0.5); //50% is default
    //        $scale = get_option('codeneric/phmm/watermark/scale', 1.0); //50% is default

    $pos_h = $position_map[$watermark_position]['h'];
    $pos_v = $position_map[$watermark_position]['v'];

    $scale = $watermark_scale / 100;

    $ratio = \min($w_img / $w_watermark, $h_img / $h_watermark) * $scale;

    //        $w_watermark_new = ceil( $w_img * $fraction_h);
    //        $h_watermark_new = ceil( ($w_watermark_new / $w_watermark) * $h_watermark);
    $w_watermark_new = \ceil($w_watermark * $ratio);
    $h_watermark_new = \ceil($h_watermark * $ratio);

    if (\function_exists('ini_set')) {
      // $memory = ceil((($w_watermark * $h_watermark+ $w_img * $h_img + $w_watermark_new * $h_watermark_new ) * 16 ) / (8 * 1000 * 1000)) + ceil(memory_get_usage(true)/(1000*1000));
      // ini_set("memory_limit",$memory."M");
      \ini_set("memory_limit", "-1");
      // error_log( "Set memory: " . $memory . "M" );
    }

    switch ($image_type) {
      case 1:
        $dest = \imagecreatefromgif($filename);
        break;
      case 2:
        $dest = \imagecreatefromjpeg($filename);
        break;
      case 3:
        $dest = \imagecreatefrompng($filename);
        break;
      default:
        return;
    }

    //try to load image
    $watermark_src = \imagecreatefrompng($watermark_path);

    $abs_pos_h = \ceil($pos_h * $w_img - $w_watermark_new / 2);
    $abs_pos_v = \ceil($pos_v * $h_img - $h_watermark_new / 2);

    $abs_pos_h =
      $abs_pos_h + $w_watermark_new <= $w_img
        ? $abs_pos_h
        : $abs_pos_h - ($abs_pos_h + $w_watermark_new - $w_img);
    $abs_pos_v =
      $abs_pos_v + $h_watermark_new <= $h_img
        ? $abs_pos_v
        : $abs_pos_v - ($abs_pos_v + $h_watermark_new - $h_img);

    $abs_pos_h = $abs_pos_h >= 0 ? $abs_pos_h : 0;
    $abs_pos_v = $abs_pos_v >= 0 ? $abs_pos_v : 0;

    $watermark = \imagecreatetruecolor($w_watermark_new, $h_watermark_new);
    \imagealphablending($watermark, false);
    \imagesavealpha($watermark, true);

    \imagecopyresampled(
      $watermark,
      $watermark_src,
      0,
      0,
      0,
      0,
      $w_watermark_new,
      $h_watermark_new,
      $w_watermark,
      $h_watermark    );

    //        imagecopymerge($dest, $watermark, 10, 9, 0, 0, 181, 180, 75); //have to play with these numbers for it to work for you, etc.
    if (!$input_image_is_watermark_itself) {
      \imagecopy(
        $dest,
        $watermark,
        $abs_pos_h,
        $abs_pos_v,
        0,
        0,
        $w_watermark_new,
        $h_watermark_new      ); //have to play with these numbers for it to work for you, etc.
    }

    //        imagejpeg($dest);
    switch ($image_type) {
      case 1:
        \imagegif($dest);
        break;
      case 2:
        \imagejpeg($dest);
        break; // best quality
      case 3:
        \imagepng($dest);
        break; // no compression
      default:
        echo '';
        break;
    }

    \imagedestroy($dest);
    \imagedestroy($watermark_src);
    \imagedestroy($watermark);
    //        exit;
  }
}
