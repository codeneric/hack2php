<?php

use \codeneric\phmm\base\includes\Error;
use \codeneric\phmm\base\includes\RecoverEnum;
use \codeneric\phmm\base\includes\ErrorSeverity;
use \Eris\Generator;

final class ErrorTest extends Codeneric_UnitTest {

  public function setUp() {
    parent::setUp();
    self::makeAdministrator();
  }

  public function testError() {
    $this->forAll(Generator\string())->then(
      function($message) {
        $err = new Error($message);

        $this->assertSame($err->message, $message);
        $this->assertSame($err->severity, ErrorSeverity::CRITICAL);

      }    );
  }

  public function testError2() {
    $this->forAll(
      Generator\string(),
      Generator\oneOf(
        Generator\constant(ErrorSeverity::CRITICAL),
        Generator\constant(ErrorSeverity::WARNING)      )    )->then(
      function($message, $severity) {
        $err = new Error($message, [], $severity);

        $s = $err->__toString();

        $unserialized = Error::unseralize($s);

        $this->assertTrue($unserialized instanceof Error);
\HH\invariant($unserialized instanceof Error, "");

        $this->assertSame($unserialized->message, $message);
        $this->assertSame($unserialized->severity, $severity);

      }    );
  }
  public function testError3() {
    $err = new Error(
      "My message",
      [],
      ErrorSeverity::CRITICAL,
      RecoverEnum::recoverOption,
      ["hans" => "peter"] /*
       */    );

    $err->recover();
  }

}
