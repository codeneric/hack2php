 <?php 
 

$GLOBALS['HACKLIB_ROOT'] = __DIR__ .'/../../vendor/hacklib/hacklib.php'; // will be injected by gulp
require_once dirname( dirname( __FILE__ ) ) .'/../vendor/autoload.php';

// require_once('./wordpress-develop/tests/phpunit/includes/bootstrap.php');
// $_tests_dir = getenv( 'WP_TESTS_DIR' );





/**
 * Set up environment for my plugin's tests suite.
 */
               
/**
 * The path to the WordPress tests checkout.
 */ 
define( 'WP_TESTS_DIR', './wordpress-develop/tests/phpunit/' );     
  
/**
 * The path to the main file of the plugin to test.
 */
define( 'TEST_PLUGIN_FILE', __DIR__ .'/../../photography_management.php' );
 
/** 
 * The WordPress tests functions.
 *
 * We are loading this so that we can add our tests filter
 * to load the plugin, using tests_add_filter().
 */
require_once WP_TESTS_DIR . 'includes/functions.php';
 
/**
 * Manually load the plugin main file. 
 *
 * The plugin won't be activated within the test WP environment,
 * that's why we need to load it manually.
 *
 * You will also need to perform any installation necessary after
 * loading your plugin, since it won't be installed.
 */
function _manually_load_plugin() {
 
    require TEST_PLUGIN_FILE;
 
    // Make sure plugin is installed here ...
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );
  
/**
 * Sets up the WordPress test environment.
 *
 * We've got our action set up, so we can load this now,
 * and viola, the tests begin.
 */
require WP_TESTS_DIR . 'includes/bootstrap.php';






require_once (__DIR__.'/baseClass.php');
