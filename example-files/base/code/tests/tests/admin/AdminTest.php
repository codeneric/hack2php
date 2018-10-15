<?hh

// PHPUNIT assertions: https://phpunit.de/manual/4.8/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.test-dependencies

use codeneric\phmm\base\admin\Main as Admin;

/* HH_IGNORE_ERROR[3015] The member is always defined with setUp() */
final class AdminTest extends WP_UnitTestCase {

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

  public function setUp(): void {
    parent::setUp();

    $this->config = Codeneric_UnitTest_Helper::makeConfig();

    $this->schema = new \codeneric\phmm\validate\Handler();

    // $this->project =
    //   new \codeneric\phmm\base\includes\Project($this->schema, $this->config);

    $proph = $this->prophesize(\codeneric\phmm\base\includes\Client::class);
    $this->client = $proph->reveal();
    $this->clientMock = $proph;

    $proph = $this->prophesize(\codeneric\phmm\base\includes\Project::class);
    $this->project = $proph->reveal();
    $this->projectMock = $proph;

    $this->admin = new \codeneric\phmm\base\admin\Main();

  }
  public function testPostTypesExist() {
    $this->assertTrue(post_type_exists($this->config['client_post_type']));
    $this->assertTrue(post_type_exists($this->config['project_post_type']));
  }
  public function testPostTypesAreSetupCorrectly() {
    $clientConfiguration =
      get_post_type_object($this->config['client_post_type']);
    $projectConfiguration =
      get_post_type_object($this->config['project_post_type']);
      
    $this->assertFalse(is_null( $clientConfiguration));
    $this->assertFalse(is_null( $projectConfiguration));
  
  }

  public function testSaveMetaBoxData() {
    /* UNSAFE_EXPR  */
    $this->assertFalse(Admin::save_meta_box_data(null, null, false));
  }

  public function testSaveMetaBoxData2() {
    // arbitrary post
    $post = $this->factory->post->create_and_get();

    $this->assertFalse(Admin::save_meta_box_data($post->ID, $post, true));
  }
  public function testSaveMetaBoxData3() {

    $post = $this->factory->post->create_and_get();

    $this->assertFalse(Admin::save_meta_box_data($post->ID, $post, true));

  }
  // public function testSaveMetaBoxData4() {
  //   $client =
  //     $this->factory
  //       ->post
  //       ->create_and_get(
  //         array("post_type" => $this->config['client_post_type']),
  //       );
  //   $_POST = ["project_access" => []];
    
  //   $this->assertTrue(Admin::save_meta_box_data($client->ID, null, true));     
    
  //   // $proph->save($client->ID, $_POST)->shouldBeCalled();
  // }
  // public function testSaveMetaBoxData5() {
  //   $client =
  //     $this->factory
  //       ->post
  //       ->create_and_get(
  //         array("post_type" => $this->config['project_post_type']),
  //       );
  //   $_POST = array("testing" => "data");
    
  //   $this->assertTrue(Admin::save_meta_box_data($client->ID, null, true));
  //   $this->projectMock->save_project($client->ID, $_POST)->shouldBeCalled();
  // }

  public function testReferenceCleanupBeforeProjectDeletion() {
    $this->markTestIncomplete();
  }
}
