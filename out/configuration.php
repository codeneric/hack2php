<?php 
namespace codeneric\phmm;
final class MODE  { private function __construct() {} 
private static $hacklib_values = array(
"DEVELOPMENT" => "development" ,
"PRODUCTION" => "production" 
);
use \HH\HACKLIB_ENUM_LIKE;
const DEVELOPMENT = "development";
const PRODUCTION = "production";
 }

use \codeneric\phmm\base\includes\Error;

require_once ABSPATH.'wp-admin/includes/plugin.php';
class Configuration {
  static $target = "base";
  const     premium_key = 'photography-management-premium/photography_management-premium.php';

  // public \codeneric\phmm\type\configuration\I $config;

  public static function get(){

    /* experimental */
    /* memoize the result from last call */
    static $result;
    if (!is_null($result)) {
      // respect the possibility of premium hooking into the config.
      // only memoize when the filter is not newly set
      if (self::$target === " base" &&
          !has_filter('codeneric/phmm/premium/manifest'))
        return $result;
      if (self::$target === " premium" &&
          has_filter('codeneric/phmm/premium/manifest'))
        return $result;

    }

    $env = "%%PLUGIN_ENV%%";
    $manifestPath = '';
    /* Load the manifest from premium if defined */
    if (has_filter('codeneric/phmm/premium/manifest')) {
      self::$target = "premium";
      $manifestPath = apply_filters('codeneric/phmm/premium/manifest', null);
    } else {
      $manifestPath = plugin_dir_path(__FILE__).'assets/js/manifest.json';
    }

    /* Load the plugin file from premium if defined */
    // $plugin_file_path = '';

    // if (has_filter('codeneric/phmm/premium/plugin_file')) {
    //   $plugin_file_path =
    //     apply_filters('codeneric/phmm/premium/plugin_file', null);
    // } else {
    //   $plugin_file_path =
    //     plugin_dir_path(__FILE__).'../photography_management.php';
    // }

    $plugin_file_path =
      plugin_dir_path(__FILE__).'../photography_management.php';
\HH\invariant(      is_string($manifestPath),
      '%s',
      new Error("Path to manifest.json not a string."));
\HH\invariant(      file_exists($manifestPath),
      '%s',
      new Error(
        "Given manifest.json does not exist",
        [array('manifestPath', $manifestPath)]      ));
\HH\invariant(      file_exists($plugin_file_path),
      '%s',
      new Error(
        "Given plugin file does not exist",
        [array('manifestPath', $manifestPath)]      ));

    $string = file_get_contents($manifestPath);
    $manifest = json_decode($string, true);
\HH\invariant(      is_array($manifest),
      '%s',
      new Error("Decoding of manifest failed."));
\HH\invariant(      $env === "development" || $env === "production",
      '%s',
      new Error('Setting plugin env failed.'));
\HH\invariant(      file_exists($plugin_file_path),
      '%s',
      new Error(
        'Given plugin file does not exist at path',
        [array('plugin_file_path', $plugin_file_path)]      ));
\HH\invariant(      function_exists('get_plugins'),
      '%s',
      new Error('get_plugins function is not defined.'));

    $all_plugins = get_plugins();
    $plugin_data = get_plugin_data($plugin_file_path);

    $has_premium_ext =
      array_key_exists(Configuration::premium_key, $all_plugins);

    $a_p = get_option('active_plugins');
\HH\invariant(      is_array($a_p),
      '%s',
      new Error('active_plugins did not return an array'));

    $the_plugs = get_site_option('active_sitewide_plugins'); //multisite;

    $premium_ext_active =
      in_array(Configuration::premium_key, $a_p) ||
      (is_array($the_plugs) &&
       array_key_exists(Configuration::premium_key, $the_plugs));

    $premium_version = '0.0.0'; // so php doesnt cry
    if (file_exists(dirname(__FILE__).'/../'.Configuration::premium_key)) {

      $premium_data =
        get_plugin_data(dirname(__FILE__).'/../'.Configuration::premium_key);

      $premium_version = $premium_data['Version'];
    }

    $project_post_type =
      defined('CODENERIC_PHMM_PROJECT_SLUG')
        ? /* UNSAFE_EXPR */ CODENERIC_PHMM_PROJECT_SLUG
        : "project";

    $getJsPath = function($asset) use ($env, $manifestPath) {
      if ($env !== "production")
        return $asset;

      return plugins_url($asset, $manifestPath);

    };

    $config = \HH\Map::hacklib_new(array(      "development" ,      "production" ), array(        array(
          "support_email" =>
            "support@codeneric.com",
          "manifest_path" =>
            $manifestPath,
          "target" =>
            self::$target,
          "revision" =>
            "1.0.0", /* config version in case shape changes */
          "env" =>
            "development",
          "wpps_url" =>
            'https://headgame.draco.uberspace.de/sandbox.wpps',
          "landing_url" =>
            'https://sandbox.phmm.codeneric.com',
          "client_post_type" =>
            "client",
          "project_post_type" =>
            $project_post_type,
          "plugin_name" =>
            "photography-management",
          "premium_plugin_name" =>
            "photography-management-premium",
          "plugin_slug_abbr" =>
            "phmm",
          "version" =>
            $plugin_data['Version'],
          "has_premium_ext" =>
            $has_premium_ext,
          "premium_ext_active" =>
            $premium_ext_active,
          "premium_plugin_key" =>
            Configuration::premium_key,
          "update_check_cool_down" =>
            5,
          'assets' =>
            array(
              'css' => array(
                'public' => array(
                  'projects' => plugins_url(
                    '/assets/css/public.projects.css',
                    __FILE__                  ),
                ),
                'admin' => array(
                  'post' => plugins_url(
                    '/assets/css/post.css',
                    __FILE__                  ),
                  'custom' => plugins_url(
                    '/assets/css/custom.css',
                    __FILE__                  ),
                  'fixes' => plugins_url(
                    '/assets/css/fixes.css',
                    __FILE__                  ),
                ),
              ),
              'js' => array(
                'admin' => array(
                  'common' => $getJsPath($manifest['admin.commons.js']),
                  "client" => $getJsPath($manifest['admin.client.js']),
                  "migration" => $getJsPath(
                    $manifest['admin.migration.js']                  ),
                  "project" => $getJsPath($manifest['admin.project.js']),
                  "premium_page" => $getJsPath(
                    $manifest['admin.premiumpage.js']                  ),
                  "settings" => $getJsPath(
                    $manifest['admin.settings.js']                  ),
                  "support_page" => $getJsPath(
                    $manifest['admin.supportpage.js']                  ),
                  "interactions_page" => $getJsPath(
                    $manifest['admin.interactionspage.js']                  ),
                ),
                'public' => array(
                  'common' => $getJsPath($manifest['public.commons.js']),
                  "client" => $getJsPath($manifest['public.client.js']),
                  "project" => $getJsPath(
                    $manifest['public.project.js']                  ),
                ),
              ),
              'crypto' =>
                array(
                  'pub_key' =>
                    dirname(__FILE__).
                    '/assets/crypto/codeneric_support_rsa.pub',
                ),
            ),
          "max_zip_part_size" =>
            10, //MB
          "plugin_base_url" =>
            "/photography_management",
          "image_size_fullscreen" =>
            "phmm-fullscreen",
          "phmm_posts_logout" =>
            "codeneric_phmm_posts_logout",
          "cookie_wp_postpass" =>
            "wp-postpass_",
          "client_user_role" =>
            "phmm_client",
          'default_thumbnail_id_option_key' =>
            "cc_phmm_default_thumbnail_id",
          'ping_service_url' =>
            'http://172.17.0.1:62449',
          'notification_cool_down' =>
            10,
        ),        array(
          "manifest_path" =>
            $manifestPath,
          "support_email" =>
            "support@codeneric.com",
          "target" =>
            self::$target,
          "revision" =>
            "1.0.0", /* config version in case shape changes */
          "env" =>
            "production",
          "wpps_url" =>
            'https://headgame.draco.uberspace.de/wpps',
          "landing_url" =>
            'https://codeneric.com',
          "client_post_type" =>
            "client",
          "project_post_type" =>
            $project_post_type,
          "plugin_name" =>
            "photography-management",
          "premium_plugin_name" =>
            "photography-management-premium",
          "plugin_slug_abbr" =>
            "phmm",
          "version" =>
            $plugin_data['Version'],
          "has_premium_ext" =>
            $has_premium_ext,
          "premium_ext_active" =>
            $premium_ext_active,
          "premium_plugin_key" =>
            Configuration::premium_key,
          "update_check_cool_down" =>
            60 * 60,
          'assets' =>
            array(
              'css' => array(
                'public' => array(
                  'projects' => plugins_url(
                    '/assets/css/public.projects.css',
                    __FILE__                  ),
                ),
                'admin' => array(
                  'post' => plugins_url(
                    '/assets/css/post.css',
                    __FILE__                  ),
                  'custom' => plugins_url(
                    '/assets/css/custom.css',
                    __FILE__                  ),
                  'fixes' => plugins_url(
                    '/assets/css/fixes.css',
                    __FILE__                  ),
                ),
              ),
              'js' => array( 
                'admin' => array(
                  'common' => $getJsPath($manifest['admin.commons.js']),
                  "client" => $getJsPath($manifest['admin.client.js']),
                  "migration" => $getJsPath(
                    $manifest['admin.migration.js']                  ),
                  "project" => $getJsPath($manifest['admin.project.js']),
                  "premium_page" => $getJsPath(
                    $manifest['admin.premiumpage.js']                  ),
                  "settings" => $getJsPath(
                    $manifest['admin.settings.js']                  ),
                  "support_page" => $getJsPath(
                    $manifest['admin.supportpage.js']                  ),
                  "interactions_page" => $getJsPath(
                    $manifest['admin.interactionspage.js']                  ),
                ),
                'public' => array(
                  'common' => $getJsPath($manifest['public.commons.js']),
                  "client" => $getJsPath($manifest['public.client.js']),
                  "project" => $getJsPath(
                    $manifest['public.project.js']                  ),
                ),
              ),
              'crypto' =>
                array(
                  'pub_key' =>
                    dirname(__FILE__).
                    '/assets/crypto/codeneric_support_rsa.pub',
                ),
            ),
          "max_zip_part_size" =>
            4000, //MB
          "plugin_base_url" =>
            "/photography_management",
          "image_size_fullscreen" =>
            "phmm-fullscreen",
          "phmm_posts_logout" =>
            "codeneric_phmm_posts_logout",
          "cookie_wp_postpass" =>
            "wp-postpass_",
          "client_user_role" =>
            "phmm_client",
          'default_thumbnail_id_option_key' =>
            "cc_phmm_default_thumbnail_id",
          'ping_service_url' =>
            'http://ping.codeneric.com',
          'notification_cool_down' =>
            60 * 5,
        )));
    //        $GLOBALS["cc_phmm_config"] = $config[$env];
    $result = $config[$env];
    return $config[$env];
  }

}
