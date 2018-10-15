<?hh
/*
 *  Copyright (c) 2017-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

require './vendor/hh_autoload.php';

$shortopts = "";
$shortopts .= "i:"; // Required value
$shortopts .= "o:"; // Required value
// $shortopts .= "v::"; // Optional value
$shortopts .= "h"; // These options do not accept values


getopt($shortopts);

$options = getopt($shortopts);


$help_text = "Required parameters: -i, -o\n".
  "-i: input pattern. Has to be relative and in quotes!\n".
  "-o: output directory.\n".
  "\n";

if (\array_key_exists('h', $options)) {
  echo $help_text;
  exit;
}

if (!\array_key_exists('i', $options) || !\is_string($options['i'])) {
  echo "Parameter -i has to be defined exactly once. Use -h for help.\n";
  exit;
}
if (!\array_key_exists('o', $options) || !\is_string($options['o'])) {
  echo "Parameter -o has to be defined exactly once. Use -h for help.\n";
  exit;
}

function greatest_common_prefix(array<string> $array): string {
  $prefix = array_shift(&$array); // take the first item as initial prefix
  $length = strlen($prefix);
  // compare the current prefix with the prefix of the same length of the other items
  foreach ($array as $item) {
    // check if there is a match; if not, decrease the prefix by one character at a time
    while ($length && substr($item, 0, $length) !== $prefix) {
      $length--;
      $prefix = substr($prefix, 0, -1);
    }
    if (!$length) {
      break;
    }
  }
  return $prefix;
}

function rglob(string $pattern, int $flags = 0): array<string> {
  $files = \glob($pattern, $flags);
  foreach (
    \glob(\dirname($pattern).'/*', \GLOB_ONLYDIR | \GLOB_NOSORT) as $dir
  ) {
    $files = \array_merge($files, rglob($dir.'/'.\basename($pattern), $flags));
  }
  return $files;
}


$files = rglob($options['i']);
$abs_input_path = \dirname($options['i']);

foreach ($files as $filename) {
  echo "compiling $filename\n";
  $php = \Codeneric\run($filename);
  $out_path = str_replace($abs_input_path, $options['o'], $filename);
  $out_dir = \dirname($out_path);
  if (!file_exists($out_dir)) {
    mkdir($out_dir, 0777, true);
  }

  file_put_contents($out_path, $php);

}
