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

final class HackToPHPTest extends \PHPUnit\Framework\TestCase {

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
    $d = \dirname(__FILE__);

    $fSetup = \tmpfile();
    $php_file_path = \stream_get_meta_data($fSetup)['uri'];


    $files = $this->rglob("$d/example-files/*.php");
    // $files = $this->rglob("example-files/phmm/vendor/giorgiosironi/*.php");
    $i = 0;
    // echo \count($files)." hack files to compile...";

    foreach ($files as $filename) {

      $php = \Codeneric\run($filename);

      // $php_file_path = "$t/the-file.php";
      \file_put_contents($php_file_path, $php);

      // echo "Testing $filename...\n"; 
      // $res = \exec("cat $php_file_path | php -l ");
      $res = \exec("php -l $php_file_path");
      
      // $this->assertSame(
      //   "No syntax errors detected in -",
      //   $res,
      //   "Syntax error in file $filename:\n$res\n$php",
      // );
      $this->assertSame(
        null,
        $res,
        "Syntax error in file $filename:\n$res\n$php",
      );
    }

    \unlink($php_file_path);

  }
}
