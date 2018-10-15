<?php
use \codeneric\phmm\base\admin\CannedEmail\Handler as CannedEmail;
use
  \  codeneric\phmm\base\admin\CannedEmail\Placeholder as CannedEmailPlaceholder
;

// PHPUNIT assertions: https://phpunit.de/manual/4.8/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.test-dependencies

final class CannedEmailTest extends Codeneric_UnitTest {

  public function setUp() {
    // delete_option(Settings::option_name);
  }

  public function testProcessPlaceholders() {
    $this->markTestIncomplete();
  }

}
