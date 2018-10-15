<?php
use \codeneric\phmm\base\includes\Client;
use \codeneric\phmm\base\includes\Error;
use \codeneric\phmm\base\includes\ErrorSeverity;
use \Eris\Generator;
// PHPUNIT assertions: https://phpunit.de/manual/4.8/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.test-dependencies

/* HH_IGNORE_ERROR[3015] The member is always defined with setUp() */
final class ConfigurationTest extends Codeneric_UnitTest {

  public function testConfiguration() {
    // doesnt throw
    \codeneric\phmm\Configuration::get();
    $this->assertTrue(true);
  }
  public function testConfiguration2() {
    // doesnt throw
    $conifg = \codeneric\phmm\Configuration::get();
    $this->assertSame($conifg['target'], "base");
  }

}
