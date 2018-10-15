<?php //strict
namespace codeneric\phmm;
use \codeneric\phmm\base\includes\Error;
use \codeneric\phmm\base\globals\Superglobals;

class Utils {

    public static function admin_user_ids(){
        //Grab wp DB
        $wpdb = Superglobals::Globals('wpdb');
\HH\invariant(            $wpdb instanceof \wpdb,
            '%s',
            new Error('Can not get global wpdb object!'));
        //Get all users in the DB
        $wp_user_search = $wpdb->get_results(
            "SELECT ID, display_name FROM $wpdb->users ORDER BY ID"        );

        //Blank array
        $adminArray = array();
        //Loop through all users
        foreach ($wp_user_search as $userid) {
            //Current user ID we are looping through
            $curID = $userid->ID;
            //Grab the user info of current ID
            $curuser = get_userdata($curID);
            //Current user level
            if ($curuser instanceof \WP_User) {
                $user_level = $curuser->user_level;
                //Only look for admins
                if ($user_level >= 8) { //levels 8, 9 and 10 are admin
                    //Push user ID into array
                    $adminArray[] = $curID;
                }
            }

        }
        return $adminArray;
    }

    public static function is_current_user_admin(){
        return current_user_can('administrator');
    }

    public static function string_contains(
$haystack,
$needle    ){
        $strpos = strpos($haystack, $needle);

        return $strpos !== false;
    }

    private static function generate_unique_id(){
        $uniqid = uniqid('', true);
        $uniqid = str_replace('.', '', $uniqid);

        return $uniqid;
    }

    static function get_plugin_id(){
        $id = get_option('cc_photo_manage_id', null);

        if (!is_string($id)) {
            $id = self::generate_unique_id();
            update_option('cc_photo_manage_id', $id);
        }

        return $id;

    }

    static function apply_filter_or(
$filterHandle,
$arg,
$or    ){
        if (has_filter($filterHandle)) {
            return apply_filters($filterHandle, $arg);
        } else
            return $or;
    }

    static function wp_version_is_at_least($version){
        $actual = get_bloginfo('version');

        return version_compare($actual, $version, '>=');
    }

    static function php_version_is_at_least($version){
        $actual = phpversion();

        return version_compare($actual, $version, '>=');
    }
    static function wp_version_is_lower_than($version){
        $actual = get_bloginfo('version');

        return version_compare($actual, $version, '<');
    }

    static function array_nested_key_exist(
$path,
$array,
$separator = "."    ){

        if (!is_array($array))
            return false;

        $paths = explode($separator, $path);
        $p = array_shift($paths);

        if (array_key_exists($p, $array)) {
            $arr = $array[$p];

            $path = implode($separator, $paths);

            if ($path === '')
                return true;

            return self::array_nested_key_exist($path, $arr, $separator);

        }

        return false;

    }

    static function get_temp_file(
$prefix    ){
        $tmp_dir = sys_get_temp_dir();
        $temp_file = tempnam($tmp_dir, $prefix);
\HH\invariant(            is_string($temp_file),
            '%s',
            new Error('Unable to get a filename for a temporary file.'));
        $handle = fopen($temp_file, "w");
\HH\invariant(            is_resource($handle),
            '%s',
            new Error('Could not create temporary file.'));
        return array('resource' => $handle, 'name' => $temp_file);
        // fwrite($handle, "writing to tempfile");
        // fclose($handle);

        // // do here something

        // unlink($temp_file);
    }

    static function close_and_delete_file(
$handle,
$name    ){
        return fclose($handle) && unlink($name);

    }

    static function get_current_user_id(){
        return get_current_user_id(); // 0 it nobody is logged in
    }

    static function get_post_meta_ONLY_USE_IN_HELPER_FUNCTIONS(
$postID,
$key    ){
        $meta = get_post_meta($postID, (string)$key, true);

        if ($meta === "")
            return null;
        else
            return $meta;

    }

    static function array_reduce(
$array,
$f,
$init    ){
        // $temp = array_values($init);
        $temp = $init;
        $res = [];
        foreach ($array as $i => $item) {
            $temp = $f($temp, $item);
        }
        return $temp;
    }

    static function array_merge($a, $b){
        // $temp = array_values($init);
        $res = $a;
        foreach ($b as $item) {
            $res[] = $item;
        }
        return $res;
    }

    static function get_uncropped_image_sizes(
    ){

        // $_wp_additional_image_sizes =
        //   Superglobals::Globals('_wp_additional_image_sizes');
        $_wp_additional_image_sizes = wp_get_additional_image_sizes();
        $sizes = [];

        foreach (get_intermediate_image_sizes() as $_size) {
            if (
                in_array(
                    $_size,
                    array('thumbnail', 'medium', 'medium_large', 'large')                )
            ) {
                $sizes[$_size] =
                    array('width' => 0, 'height' => 0, 'crop' => true);
                $sizes[$_size]['width'] = (int)get_option("{$_size}_size_w");
                $sizes[$_size]['height'] = (int)get_option("{$_size}_size_h");
                $sizes[$_size]['crop'] = (bool)get_option("{$_size}_crop");

            } else if (
                is_array($_wp_additional_image_sizes) &&
                array_key_exists($_size, $_wp_additional_image_sizes)
            ) {
                $sizes[$_size] = array(
                    'width' =>
                        (int)$_wp_additional_image_sizes[$_size]['width'],
                    'height' =>
                        (int)$_wp_additional_image_sizes[$_size]['height'],
                    'crop' => (bool)$_wp_additional_image_sizes[$_size]['crop'],
                );
            }
        }

        $sizes = array_filter(
            $sizes,
            function($s) {
                return !$s['crop'];
            }        );

        return $sizes;
    }

    static function time(){
        return (float)microtime(true);
    }

}
