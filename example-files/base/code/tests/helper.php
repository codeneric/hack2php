<?hh

class Codeneric_UnitTest_Helper extends WP_UnitTestCase {

  public static function makeConfig(): codeneric\phmm\type\configuration\I {

    $manifestPath = __DIR__.'/../assets/js/manifest.json';
    $plugin_file_path = __DIR__.'/../../photography_management.php';
    $string = file_get_contents($manifestPath);
    $manifest = json_decode($string, true);

    return \codeneric\phmm\Configuration::get();
  }

}
