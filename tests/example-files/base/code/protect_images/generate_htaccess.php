<?hh //strict
/**
 * Created by PhpStorm.
 * User: denis_000
 * Date: 12.11.2015
 * Time: 03:33
 */

function Photography_Management_Base_Generate_Htaccess(
  string $htaccess_path,
  ?string $new_site_url = null,
): bool {
  // $upload_dir = wp_upload_dir(); //['basedir'].'/photography_management';
  //   $upload_dir = $upload_dir['baseurl'];
  // $protect_url = plugins_url('load.plain.php', __FILE__);
  $protect_url = is_null($new_site_url) ? get_site_url() : $new_site_url;
  $htaccess = "RewriteEngine On".
    PHP_EOL.
    "RewriteCond %{REQUEST_URI} !protect.php".
    PHP_EOL.
    "RewriteCond %{QUERY_STRING} ^(.*)".
    PHP_EOL.
    "RewriteRule ^(.+)$ $protect_url/?codeneric_load_image=1&%1&f=$1 [L,NC]";
  // "RewriteRule ^(.+)$ $protect_url?%1&f=$1 [L,NC]";

  //    if(!is_writable( dirname(__FILE__).'/apache_htaccess'))return false;
  return insert_with_markers($htaccess_path, 'CODENERIC PHMM', $htaccess);

}
