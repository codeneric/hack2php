<?php

// PHPUNIT assertions: https://phpunit.de/manual/4.8/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.test-dependencies
use \codeneric\phmm\base\admin\ajax\Helper;
/* HH_IGNORE_ERROR[3015] The member is always defined with setUp() */
final class EndpointHelperTest extends Codeneric_UnitTest {

  public function testUsernameFallback() {
    $this->assertFalse(Helper::validate_username_fallback(''));

    $this->assertFalse(Helper::validate_username_fallback('!"§$%&/"'));
    $this->assertTrue(Helper::validate_username_fallback('abc'));
    $this->assertFalse(Helper::validate_username_fallback('abc!?'));
  }

  //
}