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
  // public function testPHPOnlyFeature(): void {
  //   $d = \dirname(\dirname(__FILE__));
  //   $t = "$d/example-files/temp";
  //   if (!\file_exists($t))
  //     \mkdir($t);

  //   $files = $this->rglob("example-files/*.php");
  //   // $files = $this->rglob("example-files/phmm/vendor/giorgiosironi/*.php");
  //   $i = 0;
  //   echo \count($files)." hack files to compile...";

  //   foreach ($files as $filename) {

  //     // echo "Testing $filename...\n"; 
  //     $tf = "temp_".\basename($filename);
  //     $res = \exec("$d/bin/hack2php $filename | php -l ");
  //     expect($res)->toBeSame(
  //       "No syntax errors detected in Standard input code",
  //       "Syntax error in file $filename:\n$res",
  //     );
  //   }


  // }

  public function testPHPOnlyFeature(): void {
    $d = \dirname(\dirname(__FILE__));
    $t = "$d/example-files/temp";
    if (!\file_exists($t))
      \mkdir($t);

    $files = $this->rglob("example-files/*.php");
    // $files = $this->rglob("example-files/phmm/vendor/giorgiosironi/*.php");
    $i = 0;
    echo \count($files)." hack files to compile...";

    foreach ($files as $filename) {

      $php = \Codeneric\run($filename);
      $php_file_path = "$t/the-file.php";
      \file_put_contents($php_file_path, $php);

      // echo "Testing $filename...\n"; 
      $res = \exec("cat $php_file_path | php -l ");
      expect($res)->toBeSame(
        "No syntax errors detected in Standard input code",
        "Syntax error in file $filename:\n$res",
      );
    }


  }
}
