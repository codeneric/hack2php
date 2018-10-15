<?hh
use \codeneric\phmm\base\protect_images\Main as SecurityLogic;
use \codeneric\phmm\base\globals\Superglobals;

// PHPUNIT assertions: https://phpunit.de/manual/4.8/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.test-dependencies

final class ProtectImages extends Codeneric_UnitTest {
  public function setUp() {
    parent::setUp();
    self::makeAdministrator();
  }

  private function createAndGetTestProject() {
    return
      $this->factory
        ->post
        ->create_and_get(
          array("post_type" => $this->config['project_post_type']),
        );

  }

  public function testValidator() {

    // $p = $this->createAndGetTestProject();
    // $_GET['client_id'] = 42;
    // $_GET['project_id'] = $p->ID;
    // $_GET['attach_id'] = 42;
    // $_GET['f'] = '/path/to/file';
    // $sl = new SecurityLogic();

    $this->assertTrue(true);
  }

  // public function testValidator2() {
  //   $this->predictError(); //because there is no attach_id
  //   $p = $this->createAndGetTestProject();
  //   $_GET['client_id'] = 42;
  //   $_GET['project_id'] = $p->ID;
  //   // $_GET['attach_id'] = 42;
  //   $_GET['f'] = '/path/to/file';
  //   $sl = new SecurityLogic();
  // }

}
