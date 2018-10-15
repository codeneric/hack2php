<?hh //strict

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       codeneric.com
 * @since      1.0.0
 *
 * @package    Phmm
 * @subpackage Phmm/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Phmm
 * @subpackage Phmm/includes
 * @author     Codeneric <plugin@codeneric.com>
 */
namespace codeneric\phmm\base\includes;
class i18n {

  /**
   * Load the plugin text domain for translation.
   *
   * @since    1.0.0
   */
  public function load_plugin_textdomain(): void {

    load_plugin_textdomain(
      'phmm',
      false,
      dirname(dirname(plugin_basename(__FILE__))).'/languages/',
    );

  }

}
