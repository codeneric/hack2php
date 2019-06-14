<?php //strict
namespace codeneric\phmm\base\includes;
class i18n {

  /**
   * Load the plugin text domain for translation.
   *
   * @since    1.0.0
   */
  public function load_plugin_textdomain(){

    \load_plugin_textdomain(
      'phmm',
      false,
      \dirname(\dirname(\plugin_basename(__FILE__))).'/languages/'    );

  }

}
