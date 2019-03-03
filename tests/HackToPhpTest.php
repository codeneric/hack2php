<?hh // strict
/*
 *  Copyright (c) 2017-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */


namespace Facebook\HHAST;

use function Facebook\FBExpect\expect;
use namespace HH\Lib\{C, Str, Vec};

final class HackToPhpTest extends \Facebook\HackTest\HackTest {
  const string TEST_CODE_DIR = '/example-files';

  private function rglob(string $pattern, int $flags = 0): array<string> {
    $files = \glob($pattern, $flags);
    foreach (
      \glob(\dirname($pattern).'/*', \GLOB_ONLYDIR | \GLOB_NOSORT) as $dir
    ) {
      $files = \array_merge(
        $files,
        $this->rglob($dir.'/'.\basename($pattern), $flags),
      );
    }
    return $files;
  }

  public function testPHPOnlyFeature(): void {
    $d = \dirname(\dirname(__FILE__));

    $files = $this->rglob($d.self::TEST_CODE_DIR."/*.php");
    // $files = $this->rglob("example-files/phmm/vendor/giorgiosironi/*.php");

    echo C\count($files)." hack files to compile...\n";

    $log = \tempnam(\sys_get_temp_dir(), 'hack2php_test_');
    \file_put_contents($log, \date('Y-m-d_h:i:sP')."\n");
    foreach ($files as $filename) {

      echo "Testing $filename...\n";
      $res = \exec("$d/bin/hack2php $filename 2>> $log | php -l ");
      expect($res)->toBeSame(
        "No syntax errors detected in Standard input code",
        "PHP Syntax error in file $filename:\n$res",
      );
    }

    expect(\filesize($log))->toBeSame(
      26,
      "Error while compiling.\nLog: %s ",
      $log,
    );

  }
}
