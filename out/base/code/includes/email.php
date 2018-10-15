<?php //strict
namespace codeneric\phmm\base\includes;

use \codeneric\phmm\base\includes\Error;

class Email {

  public static function send($data){
    return wp_mail(
      $data['to'],
      $data['subject'],
      $data['message'],
      $data['headers'],
      $data['attachments']    );

  }
}
