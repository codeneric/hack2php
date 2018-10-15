<?hh //strict

use codeneric\phmm\base\includes\ErrorSeverity;

require_once __DIR__.'/helper.php';
require_once __DIR__.'/../admin/admin.php';
require_once __DIR__.'/../admin/settings.php';
require_once __DIR__.'/../admin/frontendHandler.php';
require_once __DIR__.'/../admin/ajax/endpoints.php';
require_once __DIR__.'/../includes/project.php';
require_once __DIR__.'/../includes/client.php';
require_once __DIR__.'/../includes/error.php';
require_once __DIR__.'/../includes/labels.php';
require_once __DIR__.'/../types.php';
require_once __DIR__.'/../includes/superglobals.php';
require_once __DIR__.'/../protect_images/security-logic.php';

/* HH_IGNORE_ERROR[3015] The member is always defined with setUp() */
/* HH_IGNORE_ERROR[4123] sshhh */
class Codeneric_UnitTest extends WP_UnitTestCase {
  /* HH_IGNORE_ERROR[2049] sshhh */
  use \Eris\TestTrait;

  protected codeneric\phmm\type\configuration\I $config;
  protected \codeneric\phmm\validate\Handler $schema;
  protected \codeneric\phmm\base\admin\Settings $settings;
  protected \codeneric\phmm\base\admin\FrontendHandler $frontendHandler;
  protected \codeneric\phmm\base\includes\Project $project;
  protected \codeneric\phmm\base\admin\Main $admin;
  protected \codeneric\phmm\base\includes\Client $client;
  protected \codeneric\phmm\base\admin\ajax\Endpoints $ajaxEndpoints;
  protected \codeneric\phmm\base\includes\Labels $labels;
  protected \codeneric\phmm\base\includes\Phmm $phmm;

  public static function hacklib_initialize_statics_TestTrait(): void {}

  public function setUp(): void {
    parent::setUp();

    $this->config = Codeneric_UnitTest_Helper::makeConfig();

    $this->schema = new \codeneric\phmm\validate\Handler();
    $this->project = new \codeneric\phmm\base\includes\Project();
    $this->client = new \codeneric\phmm\base\includes\Client();
    $this->labels = new \codeneric\phmm\base\includes\Labels();

    $this->admin = new \codeneric\phmm\base\admin\Main();
    $this->ajaxEndpoints = new \codeneric\phmm\base\admin\ajax\Endpoints();
  }
  public function tearDown(): void {
    // die("tears");

    /* UNSAFE_EXPR */
    unset($this->config);
    /* UNSAFE_EXPR */
    unset($this->schema);
    /* UNSAFE_EXPR */
    unset($this->client);
    /* UNSAFE_EXPR */
    unset($this->proect);
    /* UNSAFE_EXPR */
    unset($this->labels);

    parent::tearDown();

  }

  public function predictError(?string $msgContains = null): void {
    // /* UNSAFE_EXPR */
    $this->setExpectedException("\WPDieException", $msgContains);
    // TODO ^ rework!
    // we do not throw an \HH\InvariantException::class. We just use the InvariantCallback,
    // so this prediction method is no longer working!
    // var_dump(  /* UNSAFE_EXPR */ $GLOBALS[\codeneric\phmm\base\admin\FrontendHandler::ERROR_GLOBAL_KEY]);
  }

  // public function expectError(
  //   ?string $expectedMessage = null,
  //   ?ErrorType $type = null,
  //   ?ErrorSeverity $severity = null,
  // ): void {

  //   $this->assertTrue(
  //     array_key_exists(
  //       \codeneric\phmm\base\admin\FrontendHandler::ERROR_GLOBAL_KEY,
  //       /* UNSAFE_EXPR */ $GLOBALS,
  //     ),
  //   );

  //   $err = /* UNSAFE_EXPR */
  //     $GLOBALS[\codeneric\phmm\base\admin\FrontendHandler::ERROR_GLOBAL_KEY];

  //   if (!is_null($expectedMessage))
  //     $this->assertContains($expectedMessage, $err['message']);
  //   if (!is_null($type))
  //     $this->assertSame($type, $err['type']);

  //   if (!is_null($severity))
  //     $this->assertSame($severity, $err['severity']);

  //   // var_dump($err);
  // }

  // public function expectNoError(
  //   ?string $expectedMessage = null,
  //   ?ErrorType $type = null,
  //   ?ErrorSeverity $severity = null,
  // ): void {

  //   $this->assertFalse(
  //     array_key_exists(
  //       \codeneric\phmm\base\admin\FrontendHandler::ERROR_GLOBAL_KEY,
  //       /* UNSAFE_EXPR */ $GLOBALS,
  //     ),
  //     "It should not throw an error! Here is the error: ".
  //     json_encode(/* UNSAFE_EXPR */
  //       $GLOBALS[\codeneric\phmm\base\admin\FrontendHandler::ERROR_GLOBAL_KEY],
  //     ),
  //   );
  // }

  /*
   * Test even private/protected methods of a class like this: $this->invokeMethod($myclass, 'myMethodName', array('my', 'args'))
   */

  // /* HH_IGNORE_ERROR[1002] Haters gonna hate*/
  // public function invokeMethod(
  //  mixed &$object,
  //   string $methodName,
  //   $parameters,
  // ): void {
  //   $reflection = new \ReflectionClass(get_class($object));
  //   $method = $reflection->getMethod($methodName);
  //   $method->setAccessible(true);

  //   return $method->invokeArgs($object, $parameters);
  // }
  // public static function tearDownAfterClass(): void {
  //   $wpdb = /*UNSAFE_EXPR*/ $GLOBALS['wpdb'];
  //   @$wpdb->check_connection();

  //   parent::tearDownAfterClass();
  // }

  public static function _setRole(string $role): void {
    $post = /*UNSAFE_EXPR*/ $_POST;
    $user_id = self::factory()->user->create(array('role' => $role));
    wp_set_current_user($user_id);
    $_POST = array_merge(/*UNSAFE_EXPR*/ $_POST, $post);
  }

  public static function makeAdministrator(): void {
    self::_setRole("administrator");
  }

  public function getValidProjectConfiguration(
  ): \codeneric\phmm\type\configuration {
    return shape(
      'commentable' => false,
      'disableRightClick' => false,
      'downloadable' => true,
      'downloadable_favs' => false,
      'downloadable_single' => false,
      'favoritable' => true,
      'showCaptions' => false,
      'showFilenames' => false,
      'watermark' => false,
    );
  }
  public function getInvalidProjectConfiguration(): array<string, mixed> {
    return array(
      'commentable' => false,
      'disableRightClick' => false,
      'downloadable' => true,
      'downloadable_favs' => "not a bool",
      'favoritable' => true,
      'showCaptions' => false,
      'showFilenames' => false,
    );
  }
  public function getValidProjectConfigurationAllTrue(
  ): \codeneric\phmm\type\configuration {
    return shape(
      'commentable' => true,
      'disableRightClick' => true,
      'downloadable' => true,
      'downloadable_favs' => true,
      'favoritable' => true,
      'showCaptions' => true,
      'showFilenames' => true,
      'downloadable_single' => false,
      'watermark' => false,
    );
  }
  public function getValidProjectConfigurationAllFalse(
  ): \codeneric\phmm\type\configuration {
    return shape(
      'commentable' => false,
      'disableRightClick' => false,
      'downloadable' => false,
      'downloadable_favs' => false,
      'downloadable_single' => false,
      'watermark' => false,
      'favoritable' => false,
      'showCaptions' => false,
      'showFilenames' => false,
    );
  }
}
