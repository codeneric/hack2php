<?php //strict
namespace codeneric\phmm\base\admin;
use \codeneric\phmm\Configuration;
use \codeneric\phmm\base\includes\Error;
class SupportPage {

  const page_name = 'phmm_support';

  public static function init(){}

  public static function add_page(){

    \add_submenu_page(
      'edit.php?post_type='.Configuration::get()['client_post_type'],
      'PHMM '.\__('Support'),
      \__('Support'),
      'manage_options',
      self::page_name,
      array(self::class, 'render_page')    );

  }

  public static function render_page(){

    $title = "<h2>".\__('Support')."</h2>";

    // $settings = self::getCurrentSettings();

    // $json = json_encode($settings);

    $fbJoin =
      "<strong>".
      \__(
        'Join our <a style=\'color: coral\' target=\'_blank\' href=\'https:\/\/www.facebook.com/groups/1529247670736165/\'>facebook group</a> to get immediate help or get in contact with other photographers using WordPress!',
        Configuration::get()['plugin_name']      ).
      "</strong>";

    echo "<form action='options.php' method='post'>
            $title
      <div class='postbox'>
                <div class='inside'>";

    echo
      "<div id='cc_phmm_support_page'>
       <div style=\"background:url('images/spinner.gif') no-repeat;background-size: 20px 20px;vertical-align: middle;margin: 0 auto;height: 20px;width: 20px;display:block;\"></div>
    </div>"
    ;

    echo "<hr />".$fbJoin."</div></div></form>";

  }

}
