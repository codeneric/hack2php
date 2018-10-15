<?php //strict
namespace codeneric\phmm\base\admin;
use \codeneric\phmm\Configuration;
use \codeneric\phmm\base\includes\Error;
class InteractionsPage {

  const page_name = 'interactions';

  public static function init(){}

  public static function add_page(){

    add_submenu_page(
      'edit.php?post_type='.Configuration::get()['client_post_type'],
      'PHMM '.__('Interactions'),
      __('Interactions'),
      'manage_options',
      self::page_name,
      array(self::class, 'render_page')    );

  }

  public static function render_page(){

    // $settings = self::getCurrentSettings();

    // $json = json_encode($settings);

    echo
      "<div id='cc_phmm_interactions_page'>
       <div style=\"background:url('images/spinner.gif') no-repeat;background-size: 20px 20px;vertical-align: middle;margin: 0 auto;height: 20px;width: 20px;display:block;\"></div>
    </div>"
    ;

  }

}
