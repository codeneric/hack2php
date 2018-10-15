<?hh
// require_once __DIR__.'/../../../helper.php';
// require_once __DIR__.'/../../../../admin/admin.php';
// require_once __DIR__.'/../../../../admin/settings.php';
// require_once __DIR__.'/../../../../admin/frontendHandler.php';
// require_once __DIR__.'/../../../../admin/ajax/endpoints.php';
// require_once __DIR__.'/../../../../includes/project.php';
// require_once __DIR__.'/../../../../includes/client.php';
// require_once __DIR__.'/../../../../includes/labels.php';
// require_once __DIR__.'/../../../../types.php';
// require_once __DIR__.'/../../../../../includes/superglobals.php';

use \codeneric\phmm\base\includes\Labels;
use \codeneric\phmm\base\includes\Client;

/* HH_IGNORE_ERROR[3015] The member is always defined with setUp() */
/* HH_IGNORE_ERROR[4123] sshhh */
class AjaxEndpointsTest extends WP_Ajax_UnitTestCase {
  protected codeneric\phmm\type\configuration\I $config;
  public function setUp() {

    parent::setUp();
    $this->config = Codeneric_UnitTest_Helper::makeConfig();
    add_filter('wp_doing_ajax', '__return_true'); // HYPER IMPORTANT OR ELSE CONSECUTIVE TESTS WILL NOT WP_DIE()!!!!
  }
  public function tearDown() {
    parent::tearDown();
    $this->_last_response = '';

    exec("rm -rf ./wordpress-develop/src/wp-content/uploads");
    exec("mkdir ./wordpress-develop/src/wp-content/uploads");

  }
  private function makeCall(string $hook, array $data = array()): object {
    $this->_last_response = '';
    $payload = json_encode($data);
    $_POST['payload'] = $payload;
    try {
      $this->_handleAjax($hook);
      throw new Exception(
        'Probable the given hook is not registered! It should never come this far',
      );
    } catch (Exception $e) {
      if ($e instanceof WPAjaxDieContinueException) {
        // var_dump($e);
        unset($e);
      } else
        throw $e;
    }

    $json = json_decode($this->_last_response);
    if (is_null($json))
      throw new Exception($this->_last_response);

    return $json;
  }

  private function assertUsernameInvalidOrTaken($res) {
    $this->assertTrue($res->success);
    $this->assertFalse($res->data->data);
    // $this->assertSingleErrorWithMessage("Username invalid or taken.", $res);
  }

  private function assertSingleErrorWithMessage(
    string $msg,
    $res,
    $notice = null,
  ) {
    $this->assertFalse($res->success, $notice);
    $error = $res->data->error;

    $this->assertCount(1, $error, $notice);
    $this->assertSame($msg, $error[0]->message, $notice);
  }
  private function assertErrorsWithMessages(array<string> $msgs, $res) {
    $this->assertFalse($res->success);
    $error = $res->data->error;

    $this->assertCount(count($msgs), $error);
    foreach ($msgs as $i => $msg) {
      $this->assertSame($msg, $error[$i]->message);
    }

  }

  private function assertImageOk($image, $id, $minithumb = null) {

    $this->assertInternalType('bool', $image->error);
    $this->assertFalse($image->error);
    $this->assertInternalType('string', $image->filename);
    $this->assertInternalType('int', $image->id);

    if ($minithumb)
      $this->assertSame(
        $image->mini_thumb,
        'data:image/png;base64,/9j/4AAQSkZJRgABAQEAYABgAAD//gA+Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2ODApLCBkZWZhdWx0IHF1YWxpdHkK/9sAQwAIBgYHBgUIBwcHCQkICgwUDQwLCwwZEhMPFB0aHx4dGhwcICQuJyAiLCMcHCg3KSwwMTQ0NB8nOT04MjwuMzQy/9sAQwEJCQkMCwwYDQ0YMiEcITIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIy/8AAEQgAEgAUAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A9o1zWZdHEMn2aB4JFk3SzXaQBXCkonzdSxBHt1NU38Qakl7FZNpFsl3L5JSF9SjDFWQmRtuMkIwI4B3ckdK2dRs/t9hJbiTy3OGjkxny5FIZGx3wwBweDjBrN0W2vZLqS91GWaSWJTaxCSJY8YwJJF29VkZFYZ6Y44NQ73OmDp+zu0rr1+X9dvM3KKKKs5gooooAKKKKAP/Z',
      ); else
      $this->assertInternalType('string', $image->mini_thumb);

    $this->assertInternalType('array', $image->sizes);

    foreach ($image->sizes as $size) {
      $this->assertInternalType('string', $size->url);
      $this->assertInternalType('int', $size->width);
      $this->assertInternalType('int', $size->height);
      $this->assertInternalType('string', $size->name);
    }

    $this->assertInternalType('object', $image->meta);

    $this->assertSame($id, $image->id);

  }
  /* CHECK USERNAME */
  public function testCheckUsername() {
    $res = $this->makeCall('phmm_check_username');

    $this->assertSingleErrorWithMessage(
      "The property username is required",
      $res,
    );

  }
  public function testCheckUsername2() {
    $res = $this->makeCall(
      'phmm_check_username',
      array("username" => "hans", "abc" => "def"),
    );

    $this->assertSingleErrorWithMessage(
      "The property abc is not defined and the definition does not allow additional properties",
      $res,
    );
  }
  public function testCheckUsername3() {
    $res = $this->makeCall('phmm_check_username', array("username" => ""));

    $this->assertSingleErrorWithMessage(
      "Must be at least 1 characters long",
      $res,
    );
  }
  public function testCheckUsername4() {
    $res =
      $this->makeCall(
        'phmm_check_username',
        array(
          "username" =>
            "morethan60characters00000000000000000000000000000000000000000000",
        ),
      );

    $this->assertSingleErrorWithMessage(
      "Must be at most 60 characters long",
      $res,
    );
  }
  public function testCheckUsername5() {
    $res =
      $this->makeCall('phmm_check_username', array("username" => "admin"));
    $this->assertUsernameInvalidOrTaken($res);
  }
  public function testCheckUsername6() {
    $res = $this->makeCall(
      'phmm_check_username',
      array(
        "username" =>
          "exactly60characters00000000000000000000000000000000000000000",
      ),
    );
    $this->assertTrue($res->success);
    $this->assertTrue($res->data->data);
  }
  public function testCheckUsername7() {
    $res = $this->makeCall(
      'phmm_check_username',
      array("username" => "Hans Peter"),
    );
    $this->assertTrue($res->success);
    $this->assertTrue($res->data->data);
  }
  public function testCheckUsername8() {
    $this->factory->user->create(array("user_login" => "Hans Peter"));
    $res = $this->makeCall(
      'phmm_check_username',
      array("username" => "Hans Peter"),
    );
    $this->assertUsernameInvalidOrTaken($res);
  }

  /* CHECK EMAIL */

  public function testCheckEmail() {
    $res = $this->makeCall('phmm_check_email');
    // var_dump($res->data);
    $this->assertErrorsWithMessages(['The property email is required'], $res);
  }
  public function testCheckEmail2() {
    $res = $this->makeCall(
      'phmm_check_email',
      ["some" => "bs", "email" => 42, "client_id" => array()],
    );
    $this->assertErrorsWithMessages(
      [
        'Array value found, but an integer is required',
        'Integer value found, but a string is required',
        'The property some is not defined and the definition does not allow additional properties',
      ],
      $res,
    );
  }
  public function testCheckEmail3() {
    $res = $this->makeCall(
      'phmm_check_email',
      ["email" => "", "client_id" => "42"],
    );
    $this->assertErrorsWithMessages(
      ['Must be at least 1 characters long'],
      $res,
    );
  }

  public function testCheckEmail4() {
    $invalidAddresses = [
      'plainaddress',
      '#@%^%#$@#$@#.com',
      '@example.com',
      'email.example.com',
      'email@example',
      'email@example..com',
    ];

    foreach ($invalidAddresses as $email) {
      $res = $this->makeCall(
        'phmm_check_email',
        ["email" => $email, "client_id" => "42"],
      );
      $this->assertSingleErrorWithMessage(
        "Invalid email",
        $res,
        "$email should be an invalid email address",
      );
    }
  }
  public function testCheckEmail5() {
    $validAddresses = [
      'email@example.com',
      'firstname.lastname@example.com',
      'email@subdomain.example.com',
      'firstname+lastname@example.com',
      'email@123.123.123.123',
      'email@[123.123.123.123]',
    ];

    foreach ($validAddresses as $email) {
      $res = $this->makeCall(
        'phmm_check_email',
        ["email" => $email, "client_id" => "42"],
      );
      $this->assertTrue($res->success);
      $this->assertTrue($res->data->data);
    }
  }
  public function testCheckEmail6() {
    $email = 'email@example.com';
    $client =
      $this->factory
        ->post
        ->create_and_get(
          array("post_type" => $this->config['client_post_type']),
        );

    $res = $this->makeCall(
      'phmm_check_email',
      ["email" => $email, "client_id" => $client->ID],
    );
    $this->assertTrue($res->success);
    $this->assertTrue($res->data->data);
  }
  public function testCheckEmail7() {
    $email = 'alex@codeneric.com';
    wp_insert_user(
      [
        "user_login" => "hans peter",
        "user_pass" => NULL,
        "user_email" => $email,
      ],
    );
    $client =
      $this->factory
        ->post
        ->create_and_get(
          array("post_type" => $this->config['client_post_type']),
        );

    $res = $this->makeCall(
      'phmm_check_email',
      ["email" => $email, "client_id" => $client->ID],
    );
    $this->assertTrue($res->success);
    $this->assertFalse($res->data->data);

  }
  public function testCheckEmail8() {
    $email = 'alex@codeneric.com';
    $userID = wp_insert_user(
      [
        "user_login" => "hans peter",
        "user_pass" => NULL,
        "user_email" => $email,
      ],
    );
    $client =
      $this->factory
        ->post
        ->create_and_get(
          array("post_type" => $this->config['client_post_type']),
        );

    update_post_meta($client->ID, 'wp_user', $userID);

    $res = $this->makeCall(
      'phmm_check_email',
      ["email" => $email, "client_id" => $client->ID],
    );
    $this->assertTrue($res->success);
    $this->assertTrue($res->data->data);

  }

  /* FETCH GALLERY IMAGES */
  public function testFetchGalleryImages() {
    $res = $this->makeCall('phmm_fetch_images');

    $this->assertErrorsWithMessages(
      [
        "The property IDs is required",
        "The property project_id is required",
      ],
      $res,
    );
  }
  public function testFetchGalleryImages2() {
    $res = $this->makeCall(
      'phmm_fetch_images',
      array("IDs" => "Hans Peter", "project_id" => "Not int"),
    );

    $this->assertErrorsWithMessages(
      [
        "String value found, but an array is required",
        "String value found, but an integer is required",
      ],
      $res,
    );
  }

  public function testFetchGalleryImages3() {
    $res = $this->makeCall(
      'phmm_fetch_images',
      array("bullshit" => "bs", "IDs" => array(), "project_id" => 42),
    );

    $this->assertSingleErrorWithMessage(
      "The property bullshit is not defined and the definition does not allow additional properties",
      $res,
    );
  }
  public function testFetchGalleryImages4() {
    $res = $this->makeCall(
      'phmm_fetch_images',
      array("IDs" => array("1, 2, 3"), "project_id" => 42),
    );

    $this->assertSingleErrorWithMessage(
      "String value found, but an integer is required",
      $res,
    );
  }
  public function testFetchGalleryImages5() {
    $id =
      $this->factory
        ->attachment
        ->create_upload_object(__DIR__."/../../../images/1.png");

    $res = $this->makeCall(
      'phmm_fetch_images',
      array("IDs" => array(42, $id), "project_id" => 42),
    );

    $this->assertTrue($res->success);
    $this->assertCount(2, $res->data->data);
    $this->assertSame(
      array("id" => 42, "error" => true),
      (array) $res->data->data[0],
    );
    $this->assertFalse($res->data->data[1]->error);
  }

  public function testFetchGalleryImages6() {
    $id =
      $this->factory
        ->attachment
        ->create_upload_object(__DIR__."/../../../images/1.png");

    $res = $this->makeCall(
      'phmm_fetch_images',
      array("IDs" => array($id), "project_id" => 42),
    );

    $image = $res->data->data[0];

    $this->assertImageOk(
      $image,
      $id,
      'data:image/png;base64,/9j/4AAQSkZJRgABAQEAYABgAAD//gA+Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2ODApLCBkZWZhdWx0IHF1YWxpdHkK/9sAQwAIBgYHBgUIBwcHCQkICgwUDQwLCwwZEhMPFB0aHx4dGhwcICQuJyAiLCMcHCg3KSwwMTQ0NB8nOT04MjwuMzQy/9sAQwEJCQkMCwwYDQ0YMiEcITIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIy/8AAEQgAEgAUAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A9o1zWZdHEMn2aB4JFk3SzXaQBXCkonzdSxBHt1NU38Qakl7FZNpFsl3L5JSF9SjDFWQmRtuMkIwI4B3ckdK2dRs/t9hJbiTy3OGjkxny5FIZGx3wwBweDjBrN0W2vZLqS91GWaSWJTaxCSJY8YwJJF29VkZFYZ6Y44NQ73OmDp+zu0rr1+X9dvM3KKKKs5gooooAKKKKAP/Z',
    );

  }
  public function testFetchGalleryImages7() {
    $id =
      $this->factory
        ->attachment
        ->create_upload_object(__DIR__."/../../../images/1.jpg");

    $res = $this->makeCall(
      'phmm_fetch_images',
      array("IDs" => array($id), "project_id" => 42),
    );
    $image = $res->data->data[0];

    $this->assertImageOk($image, $id);
  }

  /* LABEL IMAGES */
  public function testLabelImages() {
    $res = $this->makeCall('phmm_label_image');

    $this->assertErrorsWithMessages(
      array(
        "The property project_id is required",
        "The property photo_ids is required",
        "The property label_id is required",
      ),
      $res,
    );
  }
  public function testLabelImages2() {
    $res = $this->makeCall(
      'phmm_label_image',
      array(
        "project_id" => "hans",
        "photo_ids" => "peter",
        "label_id" => 42,
      ),
    );

    $this->assertErrorsWithMessages(
      array(
        "Integer value found, but a string is required",
        "String value found, but an array is required",
        "String value found, but an integer is required",
      ),
      $res,
    );
  }
  public function testLabelImages3() {
    $res = $this->makeCall(
      'phmm_label_image',
      array(
        "project_id" => 42,
        "photo_ids" => array("string"),
        "label_id" => "someID",
      ),
    );

    $this->assertSingleErrorWithMessage(
      "String value found, but an integer is required",
      $res,
    );
  }
  public function testLabelImages4() {

    $res = $this->makeCall(
      'phmm_label_image',
      array(
        "project_id" => 42,
        "photo_ids" => array(21),
        "label_id" => "someID",
      ),
    );

    $this->assertSingleErrorWithMessage(
      'Only logged in user can label images',
      $res,
    );
  }
  public function testLabelImages5() {
    $this->_setRole('subscriber');

    $res = $this->makeCall(
      'phmm_label_image',
      array(
        "project_id" => 42,
        "photo_ids" => array(21),
        "label_id" => "someID",
      ),
    );

    $this->assertSingleErrorWithMessage(
      'Logged in user is not attached to any client',
      $res,
    );
  }
  public function testLabelImages6() {

    $client =
      $this->factory
        ->post
        ->create_and_get(
          array("post_type" => $this->config['client_post_type']),
        );

    $this->_setRole('subscriber');

    $id = get_current_user_id();

    update_post_meta($client->ID, 'wp_user', $id);
    $res = $this->makeCall(
      'phmm_label_image',
      array(
        "project_id" => 42,
        "photo_ids" => array(21),
        "label_id" => "someID",
      ),
    );
    $this->assertSingleErrorWithMessage(
      'Given client does not have access to this project',
      $res,
    );
  }

  private function prepareClient(array $access) {
    $client =
      $this->factory
        ->post
        ->create_and_get(
          array("post_type" => $this->config['client_post_type']),
        );
    $this->_setRole('subscriber');
    $id = get_current_user_id();

    update_post_meta($client->ID, 'wp_user', $id);
    update_post_meta($client->ID, 'project_access', array($access));

    return $client;
  }
  public function testLabelImages7() {

    $this->prepareClient(array("id" => 42));

    $res = $this->makeCall(
      'phmm_label_image',
      array(
        "project_id" => 42,
        "photo_ids" => array(21),
        "label_id" => "someID",
      ),
    );
    $this->assertSingleErrorWithMessage('Given label does not exist', $res);
  }
  public function testLabelImages8() {
    $client = $this->prepareClient(array("id" => 42));

    $set = Labels::get_set($client->ID, 42, "1111111111111");
    $this->assertSame([], $set);
    $res = $this->makeCall(
      'phmm_label_image',
      array(
        "project_id" => 42,
        "photo_ids" => array(21),
        "label_id" => "1111111111111",
      ),
    );
    $this->assertTrue($res->success);

    $set = Labels::get_set($client->ID, 42, "1111111111111");
    $this->assertSame([21], $set);
  }
  public function testLabelImages9() {

    $client = $this->prepareClient(array("id" => 42));

    $res = $this->makeCall(
      'phmm_label_image',
      array(
        "project_id" => 42,
        "photo_ids" => array(21, 18, 15),
        "label_id" => "1111111111111",
      ),
    );
    $this->assertTrue($res->success);

    $set = Labels::get_set($client->ID, 42, "1111111111111");
    $this->assertSame([21, 18, 15], $set);

  }
}
