<?hh //strict 


function get_cli_args(): array<string, string> {
  $root = realpath(__DIR__.'/..');
  $found_autoloader = false;
  while (true) {
    $autoloader = $root.'/vendor/hh_autoload.php';
    if (file_exists($autoloader)) {
      $found_autoloader = true;
      require_once($autoloader);
      break;
    }
    if ($root === '') {
      break;
    }
    $parts = explode('/', $root);
    array_pop(&$parts);
    $root = implode('/', $parts);
  }

  if (!$found_autoloader) {
    fprintf(STDERR, "Failed to find autoloader.\n");
    exit(1);
  }


  require_once $root.'/src/logic.php';

  $shortopts = "";
  $shortopts .= "i:"; // Required value
  $shortopts .= "o:"; // Required value
  //   $shortopts .= "e::"; // Optional value
  $shortopts .= "h"; // These options do not accept values


  // getopt($shortopts);

  $options = getopt($shortopts);


  $help_text = "Required parameters: -i, -o\n".
    "-i: input directoy\n".
    "-o: output directory.\n".
    // "-e: path to extension hack file.\n".
    "\n";
  /* HH_IGNORE_ERROR[1002] */
  if (\array_key_exists('h', $options)) {
    echo $help_text;
    exit();
  }

  if (!\array_key_exists('i', $options) || !\is_string($options['i'])) {
    echo "Parameter -i has to be defined exactly once. Use -h for help.\n";
    exit();
  }
  if (!\array_key_exists('o', $options) || !\is_string($options['o'])) {
    echo "Parameter -o has to be defined exactly once. Use -h for help.\n";
    exit();
  }
  //   if (!\array_key_exists('e', $options) || !\is_string($options['e'])) {
  //     $options['e'] = null;
  //   }

  return $options;
}


function _join_paths(string $a, string $b): string {
  return implode('/', array_filter(explode('/', "$a/$b")));
}

function rglob(string $pattern, int $flags = 0): array<string> {
  $p1 = _join_paths($pattern, '/*.php');
  $p2 = _join_paths($pattern, '/*');
  $files = \glob($p1, $flags);
  foreach (\glob($p2, \GLOB_ONLYDIR | \GLOB_NOSORT) as $dir) {
    // if ($dir !== $pattern) {
    // }
    $files = \array_merge($files, rglob($dir, $flags));

    // echo $dir;
  }
  return $files;
}

function get_cache_hash(string $file, ?string $ext_file = null): string {
  $ftime = \filemtime($file);
  $ftime_ext_file = \is_null($ext_file) ? 0 : \filemtime($ext_file);
  $cache_hash = \md5("$file:$ftime:$ftime_ext_file");
  return $cache_hash;
}

function is_cache_stale(
  string $file,
  string $cache_dir,
  ?string $ext_file = null,
): bool {
  $cache_hash = get_cache_hash($file, $ext_file);
  return !\file_exists('/'._join_paths($cache_dir, $cache_hash));
}

function get_cache(
  string $file,
  string $cache_dir,
  ?string $ext_file = null,
): string {
  $cache_hash = get_cache_hash($file, $ext_file);
  return file_get_contents('/'._join_paths($cache_dir, $cache_hash));
}
function put_cache(
  string $content,
  string $file,
  string $cache_dir,
  ?string $ext_file = null,
): void {
  $cache_hash = get_cache_hash($file, $ext_file);
  $c_path = '/'._join_paths($cache_dir, $cache_hash);
  // echo "\n$c_path\n";
  file_put_contents($c_path, $content);
}


function transpile_dir(): void {
  $options = get_cli_args();
  $files = rglob($options['i']);
  $abs_input_path = \dirname($options['i']);

  $cache_dir = '/hack2php-cache';
  if (!\file_exists($cache_dir)) {
    \mkdir($cache_dir);
  }

  foreach ($files as $filename) {
    echo "compiling $filename to ";
    if (is_cache_stale($filename, $cache_dir)) {
      $php = \Codeneric\run($filename);
      put_cache($php, $filename, $cache_dir);
    } else {
      $php = get_cache($filename, $cache_dir);
    }
    $out_path = str_replace($options['i'], '', $filename);
    $out_path = _join_paths($options['o'], $out_path);
    $out_dir = \dirname($out_path);
    if (!file_exists($out_dir)) {
      mkdir($out_dir, 0777, true);
    }

    echo "$out_path\n";

    file_put_contents($out_path, $php);

  }

}
