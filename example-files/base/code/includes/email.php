<?hh //strict

namespace codeneric\phmm\base\includes;

use \codeneric\phmm\base\includes\Error;

type params = shape(
  "to" => array<string>,
  "subject" => string,
  "message" => string,
  "headers" => string,
  "attachments" => array<string>,
);

class Email {

  public static function send(params $data): bool {
    return wp_mail(
      $data['to'],
      $data['subject'],
      $data['message'],
      $data['headers'],
      $data['attachments'],
    );

  }
}

