<?hh // strict
/*
 *  Copyright (c) 2017-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 *  https://github.com/hhvm/hhast/commit/c18964a2ad8132b7856e366e59c61fd91042a81b
 *
 */

namespace codeneric\hack2php;


use namespace Facebook\HHAST\__Private as AST_Prv;
use namespace Facebook\HHAST\Linters;

use namespace HH\Lib\{Math, Str, Vec};

use type Facebook\CLILib\CLIWithArguments;
use namespace Facebook\CLILib\CLIOptions;

final class TranspilerCLI extends CLIWithArguments {
  private bool $xhprof = false;
  private AST_Prv\LinterCLIMode $mode = AST_Prv\LinterCLIMode::PLAIN;

  use AST_Prv\CLIWithVerbosityTrait;

  <<__Override>>
  public static function getHelpTextForOptionalArguments(): string {
    return 'PATH';
  }

  <<__Override>>
  protected function getSupportedOptions(): vec<CLIOptions\CLIOption> {
    return vec[
      CLIOptions\flag(
        () ==> {
          $this->xhprof = true;
        },
        'Enable XHProf profiling',
        '--xhprof',
      ),
      CLIOptions\with_required_enum(
        AST_Prv\LinterCLIMode::class,
        $m ==> {
          $this->mode = $m;
        },
        'Set the output mode; supported values are '.
        Str\join(AST_Prv\LinterCLIMode::getValues(), ' | '),
        '--mode',
        '-m',
      ),
      CLIOptions\with_required_string(
        $_ ==> {},
        'Name of the caller; intended for use with `--mode json` or `--mode lsp`',
        '--from',
      ),
      $this->getVerbosityOption(),
    ];
  }

  <<__Override>>
  public async function mainAsync(): Awaitable<int> {
    if ($this->xhprof) {
      AST_Prv\XHProf::enable();
    }

    $result = await $this->mainImplAsync();

    if ($this->xhprof) {
      AST_Prv\XHProf::disableAndDump(\STDERR);
    }

    return $result;
  }

  private async function mainImplAsync(): Awaitable<int> {
    $terminal = $this->getTerminal();
    if ($this->mode === AST_Prv\LinterCLIMode::LSP) {
      return await (new AST_Prv\LSPImpl\Server($terminal))->mainAsync();
    }

    $err = $this->getStderr();
    $roots = $this->getArguments();


    /*    if (C\is_empty($roots)) {
          $config = AST_Prv\LintRunConfig::getForPath(\getcwd());
          $roots = $config->getRoots();
          if (C\is_empty($roots)) {
            await $err->writeAsync(
              "You must either specify PATH arguments, or provide a configuration".
              "file.\n",
            );
            return 1;
          }
        } else {
          foreach ($roots as $root) {
            $path = \realpath($root);
            if (\is_dir($path)) {
              $config_file = $path.'/hhast-lint.json';
              if (\file_exists($config_file)) {
                /* HHAST_IGNORE_ERROR[DontAwaitInALoop] * /
                await $err->writeAsync(
                  "Warning: PATH arguments contain a hhast-lint.json, ".
                  "which modifies the linters used and customizes behavior. ".
                  "Consider 'cd ".
                  $root.
                  "; vendor/bin/hhast-lint'\n\n",
                );
              }
            }
          }
          $config = null;
        }
    */

    $config = AST_Prv\LintRunConfig::getForPath(__DIR__ . '/lint-config');

    switch ($this->mode) {
      case AST_Prv\LinterCLIMode::PLAIN:
        $error_handler = new AST_Prv\LintRunCLIEventHandler($terminal);
        break;
      case AST_Prv\LinterCLIMode::JSON:
        $error_handler = new AST_Prv\LintRunJSONEventHandler($terminal);
        break;
      case AST_Prv\LinterCLIMode::LSP:
        invariant_violation('should have returned earlier');
    }

    try {
      $result = await (new AST_Prv\LintRun($config, $error_handler, $roots))
        ->runAsync();
    } catch (Linters\LinterException $e) {
      $orig = $e->getPrevious() ?? $e;
      $err = $terminal->getStderr();
      $pos = $e->getPosition();
      await $err->writeAsync(Str\format(
        "A linter threw an exception:\n  Linter: %s\n  File: %s%s\n",
        $e->getLinterClass(),
        \realpath($e->getFileBeingLinted()),
        $pos === null ? '' : Str\format(':%d:%d', $pos[0], $pos[1] + 1),
      ));
      if ($pos !== null && \is_readable($e->getFileBeingLinted())) {
        list($line, $column) = $pos;
        $content = \file_get_contents($e->getFileBeingLinted());
        await \file_get_contents($e->getFileBeingLinted())
          |> Str\split($$, "\n")
          |> Vec\take($$, $line)
          |> Vec\slice($$, Math\maxva($line - 3, 0))
          |> Vec\map($$, $line ==> '    > '.$line)
          |> Str\join($$, "\n")
          |> Str\format("%s\n      %s^ HERE\n", $$, Str\repeat(' ', $column))
          |> $err->writeAsync($$);
      }
      await $err->writeAsync(Str\format(
        "  Exception: %s\n"."  Message: %s\n",
        \get_class($orig),
        $orig->getMessage(),
      ));
      await $err->writeAsync(
        $orig->getTraceAsString()
          |> Str\split($$, "\n")
          |> Vec\map($$, $line ==> '    '.$line)
          |> Str\join($$, "\n")
          |> "  Trace:\n".$$."\n\n",
      );
      return 2;
    }

    switch ($result) {
      case AST_Prv\LintRunResult::NO_ERRORS:
      case AST_Prv\LintRunResult::HAD_AUTOFIXED_ERRORS:
        return 0;
      case AST_Prv\LintRunResult::HAVE_UNFIXED_ERRORS:
        return 1;
    }
  }
}
