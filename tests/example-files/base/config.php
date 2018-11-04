<?php

//TODO(denis) make class

class Photography_Management_Base_Config
{
    /**
     * Generates globally accessible config-data.
     *
     * @since    1.0.0
     * @access   public
     */



    public static function set($env){
        if ( ! function_exists( 'get_plugins' ) ) {
        	require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $env = $env !== 'production' ? 'development' : 'production';

        $all_plugins = get_plugins();
        $plugin_data = get_plugin_data(dirname(__FILE__) .'/photography_management.php');
//        $this->version = $plugin_data['Version'];
//       var_dump($all_plugins);
        $phmm_premium_key = 'photography-management-premium/photography_management-premium.php';
        $has_premium_ext = isset($all_plugins[$phmm_premium_key]);
        $a_p = get_option('active_plugins');
//        $premium_ext_active = isset($a_p[$phmm_premium_key]);
        $the_plugs = get_site_option('active_sitewide_plugins'); //multisite;
        $premium_ext_active = in_array($phmm_premium_key, $a_p) || isset($the_plugs[$phmm_premium_key]);

        $premium_version = '0.0.0'; // so php doesnt cry
        if ( file_exists( dirname( __FILE__ ) . '/../' . $phmm_premium_key ) ) {

            $premium_data = get_plugin_data( dirname( __FILE__ ) . '/../' . $phmm_premium_key );

            $premium_version = $premium_data['Version'];
        }






        $config = array(
            "development" => array(
                "env" => "development",
                "wpps_url" => 'http://headgame.draco.uberspace.de/sandbox.wpps',
                "landing_url" => 'https://sandbox.phmm.codeneric.com',
                "slug" => "client",
                "plugin_name" => "photography-management",
                "premium_plugin_name" => "photography-management-premium",
                "plugin_slug_abbr" => "phmm",
                "version" => $plugin_data['Version'],
                "has_premium_ext" => $has_premium_ext,
                "premium_ext_active" => $premium_ext_active,
//                "premium_plugin_key" => 'latest/hello_KRASS.php',
                "premium_plugin_key" => $phmm_premium_key,
                "update_check_cool_down" => 5,
                "js_admin_entry"              => 'http://localhost:3000/entry.edit.js',
                "js_admin_premium_page_entry" => 'http://localhost:3000/entry.premium-page.js',
                "js_public_entry"             => 'http://localhost:3000/entry.public.js',
                "js_admin_entry_premium"      => 'http://localhost:3001/entry.edit.js',
                "js_public_entry_premium"     => 'http://localhost:3001/entry.public.js',
                
                "js_settings_entry"     => 'http://localhost:3000/entry.settings.js',
                "js_settings_entry_premium"     => 'http://localhost:3001/entry.settings.js',

                "paypal_merchant" => "elance-facilitator@codeneric.com",
                "paypal_post_url" => "https://www.sandbox.paypal.com/cgi-bin/webscr",
                "paypal_env"      => "sandbox",

                "stripe_key" => "pk_test_uyLxBWH0UDBwlaXCzdmAzsjv",
                "max_zip_part_size" => 10 //MB
            ),
            "production" => array(
                "env" => "production",
                "wpps_url" => 'http://headgame.draco.uberspace.de/wpps',
                "landing_url" => 'https://phmm.codeneric.com',
                "slug" => "client",
                "plugin_name" => "photography-management",
                "premium_plugin_name" => "photography-management-premium",
                "plugin_slug_abbr" => "phmm",
                "version" => $plugin_data['Version'],
                "has_premium_ext" => $has_premium_ext,
                "premium_ext_active" => $premium_ext_active,
                "premium_plugin_key" => $phmm_premium_key,
                "update_check_cool_down" => 60 * 60,
                "js_admin_entry"              => plugin_dir_url( __FILE__ ) . '/admin/js/edit.bundle.base-' . $plugin_data['Version'] . '.min.js',
                "js_settings_entry"              => plugin_dir_url( __FILE__ ) . '/admin/js/settings.bundle.base-' . $plugin_data['Version'] . '.min.js',
                "js_settings_entry_premium"              => plugin_dir_url( __FILE__ ) . '../photography-management-premium/admin/js/settings.bundle.base-' . $plugin_data['Version'] . '.min.js',
                "js_public_entry"             => plugin_dir_url( __FILE__ ) . '/public/js/public.bundle.base-' . $plugin_data['Version'] . '.min.js',
                "js_admin_premium_page_entry" => plugin_dir_url( __FILE__ ) . '/admin/js/premium-page.bundle.base-' . $plugin_data['Version'] . '.min.js',
                "js_admin_entry_premium"      => plugin_dir_url( __FILE__ ) . '../photography-management-premium/admin/js/edit.bundle.premium-' . $premium_version . '.min.js',
                "js_public_entry_premium"     => plugin_dir_url( __FILE__ ) . '../photography-management-premium/public/js/public.bundle.premium-' . $premium_version . '.min.js',
                
               

                "paypal_merchant" => "elance@codeneric.com",
                "paypal_post_url" => "https://www.paypal.com/cgi-bin/webscr",
                "paypal_env"      => "www",

                "stripe_key" => 'pk_live_dvPEBGQnKz9rpcoddxTJ21Rf',
                "max_zip_part_size" => 200 //MB

            )

        );


//        $GLOBALS["cc_phmm_config"] = $config[$env];
        return $config[$env];
    }




//    public static function get_config()
//    {
//
//        $env = 'development';
//
//
//        $config = array(
//            "development" => array(
//                "wpps_url" => 'https://headgame.draco.uberspace.de/sandbox.wpps'
//            ),
//            "production" => array(
//                "wpps_url" => 'https://headgame.draco.uberspace.de/wpps'
//            )
//        );
//
//
//        $config = $config[$env];
//        return $config;
//    }
//
//    public static function codeneric_phmm_get_scripts_wl()
//    {
//        return array('common', 'admin-bar', 'post',
//            'utils', 'svg-painter', 'wp-auth-check',
//            'media-editor', 'media-audiovideo',
//            'mce-view', 'image-edit', 'media-upload',
//            'jquery', 'wp-pointer', 'stripe',
//            'jquery-ui');
//    }
//    public static function codeneric_phmm_get_styles_wl()
//    {
//        return array('admin-bar', 'colors', 'ie', 'wp-auth-check',
//            'media-views', 'imgareaselect', 'metabox-css',
//            'wp-pointer');
//    }
}