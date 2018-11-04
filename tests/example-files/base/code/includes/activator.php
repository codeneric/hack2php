<?hh //strict

/**
 * Fired during plugin activation
 *
 * @link       codeneric.com
 * @since      1.0.0
 *
 * @package    Phmm
 * @subpackage Phmm/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Phmm
 * @subpackage Phmm/includes
 * @author     Codeneric <plugin@codeneric.com>
 */
namespace codeneric\phmm\base\includes;
use codeneric\phmm\Utils;
use codeneric\phmm\base\includes\Error;

class Activator {

  /**
   * Short Description. (use period)
   *
   * Long Description.
   *
   * @since    1.0.0
   */
  public static function activate(): void {

    // if (!Utils::php_version_is_at_least('5.6.0')) {
    //   invariant(
    //     false,
    //     "%s",
    //     new Error(
    //       'Your PHP version is older than 5.6.0, but PHMM requires 5.6.0!',
    //     ),
    //   );
    // }
    \codeneric\phmm\base\admin\Main::register_client_post_type();
    \codeneric\phmm\base\admin\Main::register_project_post_type();
    flush_rewrite_rules();

    $upload_dir = wp_upload_dir();
    $upload_dir = $upload_dir['basedir'].'/photography_management';
    if (!file_exists($upload_dir)) {
      mkdir($upload_dir);

    }
    if(function_exists('is_plugin_active') && !is_plugin_active('phmm-fast-images/phmm_fast_images.php')){ 
      $htaccess_suc =
      Photography_Management_Base_Generate_Htaccess("$upload_dir/.htaccess"); 
    } 
    

    Utils::get_plugin_id();
  }

}
