<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://codeneric.com
 * @since             1.0.0
 * @package           Photography_Management
 *
 * @wordpress-plugin
 * Plugin Name:       Photography Management
 * Plugin URI:        phmm.codeneric.com
 * Description:       Provide your clients with links to (optionally password protected) photographs.
 * Version:           %%PLUGIN_VERSION%%
 * Author:            Codeneric
 * Author URI:        http://codeneric.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       photography-management
 * Domain Path:       /languages
 */ 
 
 if(isset($_GET['codeneric_phmm_deactivate']) &&
 get_transient('codeneric_phmm_deactivate') !== false &&  
  strval(get_transient('codeneric_phmm_deactivate')) === strval($_GET['codeneric_phmm_deactivate']) ){     
       deactivate_plugins( 'photography-management/photography_management.php'); 
 }


$actual_php = phpversion();
$bad_php_version =  version_compare($actual_php, '5.6', '<');
if($bad_php_version){
  if ( ! function_exists( 'cc_phmm_base_exception_disable' ) ) {
      function cc_phmm_base_exception_disable() {
        deactivate_plugins(
            array('photography-management/photography_management.php'),
            true
          ); 
      }
    }
  if(!function_exists('codeneric_phmm_php_too_old')){
    function codeneric_phmm_php_too_old(){
      $actual_php = phpversion();
      echo '<div class="notice notice-error"><p><strong>Photography Management:</strong> Your PHP version is too old. At least PHP 5.6 is required, but '.$actual_php.' installed! </p></div>';
    }
    
  } 

  add_action( 'admin_notices', 'codeneric_phmm_php_too_old' );
	add_action( 'admin_init', 'cc_phmm_base_exception_disable' );

}else{



// If this file is called directly, abort. 
if (!defined('WPINC')) {
  die(0); 
}  



 

require_once plugin_dir_path(__FILE__).'vendor/autoload.php';

if (!isset($GLOBALS['HACKLIB_ROOT'])) {
  $GLOBALS['HACKLIB_ROOT'] =
    plugin_dir_path(__FILE__).'lib/hacklib/hacklib.php'; // will be injected by gulp
}



/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__).'code/includes/phmm.php';

/* Premium Version Check */
$prem_plugin_key = 'photography-management-premium/photography_management-premium.php'; 
$prem_plugin_file_path = dirname(__FILE__) ."/../$prem_plugin_key";
if(file_exists($prem_plugin_file_path)){
  $plugin_data = get_plugin_data($prem_plugin_file_path);
  $p_v = $plugin_data['Version'];
  if(version_compare($p_v, '4.0.0', '<')){
    do_action('codeneric/phmm/base-plugin-updated');    
    deactivate_plugins(
        array($prem_plugin_key),
        true
      );  
    add_action( 'admin_notices', function(){
      echo '<div class="notice notice-error"><p> Your <strong>Photography management Premium</strong> version is too old. Delete the current version and download the new version from your premium tab.</p></div>';
    } );
  }
} 

/* Migrator Check */
$current_version = get_option('cc_photo_manage_curr_version', null);
if ( !is_null($current_version) &&  version_compare($current_version, '4.0.0', '<')  ) { 
  require_once plugin_dir_path(__FILE__).'code/includes/migrator.php';
  codeneric\phmm\Migrator::init(); 
  return;
}


require_once plugin_dir_path(__FILE__).'code/utils.php';
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-phmm-activator.php
 */
function activate_phmm() {
  require_once plugin_dir_path(__FILE__).'code/includes/activator.php';
  \codeneric\phmm\base\includes\Activator::activate();

}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-phmm-deactivator.php
 */
function deactivate_phmm() {
  require_once plugin_dir_path(__FILE__).'code/includes/deactivator.php';
  \codeneric\phmm\base\includes\Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_phmm');
register_deactivation_hook(__FILE__, 'deactivate_phmm');




/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_phmm() {
  try {    
    // good life 




    
    $plugin = new codeneric\phmm\base\includes\Phmm();  
    $plugin->run();
  }
  catch (Exception $e) {
    // bad life
    if ( ! function_exists( 'cc_phmm_base_exception_notice' ) ) {
      function cc_phmm_base_exception_notice() {
      echo '<div class="updated"><p><strong>Photography management</strong> was <strong>deactivated</strong> due to a fatal error.</p></div>';
      if ( isset( $_GET['activate'] ) )
          unset( $_GET['activate'] );
      }
    }
    if ( ! function_exists( 'cc_phmm_base_exception_disable' ) ) {
      function cc_phmm_base_exception_disable() {
        deactivate_plugins(
            array('photography-management/photography_management.php'),
            true
          );
      }
    }
		add_action( 'admin_notices', 'cc_phmm_base_exception_notice' );
		add_action( 'admin_init', 'cc_phmm_base_exception_disable' );	
  
  return; 
      
  }


}
run_phmm();
} 
