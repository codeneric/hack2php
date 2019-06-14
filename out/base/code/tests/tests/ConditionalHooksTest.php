<?php

final class ConditionalHooksTest extends Codeneric_UnitTest {

  public static function setUpBeforeClass(){
    parent::setUpBeforeClass();
    $GLOBALS['wp_version'] = "1.0.0";
  }
  public function setUp(){
    parent::setUp();

    $GLOBALS['wp_version'] = "1.0.0";

    // $this->phmm = new \codeneric\phmm\base\includes\Phmm();
    // $this->phmm->run();

    // $this->config = $this->phmm->get_public_config();
  }

  private function expectHookToBeRegistered(
$tag,
$target,
$expectation = true  ) {

    // check for int, since return is only bool when no action attachment found
    $class = $target[0];
    $class = is_string($class) ? $class : get_class($class);
    $this->assertSame(
      $expectation,
      is_int(has_action($tag, $target)),
      $class.'::'.$target[1]." should be registered to ".$tag    );
  }

  public function testConditionalHookForWpVersion1_0_0() {
    $this->markTestIncomplete();

    $public = \codeneric\phmm\base\frontend\Main::class;

    $this->expectHookToBeRegistered(
      "the_password_form",
      array($public, 'the_password_form_hook')    );

  }

}
