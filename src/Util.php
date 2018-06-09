<?hh //strict

namespace codeneric\util;

use type Facebook\HHAST\{LambdaExpression, EditableNode, VariableExpression};

type error_message = shape(
    "descr" => string,
    "path" => string,
    "line" => int,
    "start" => int,
    "end" => int,
    "code" => int,
);


type check_error = shape(
    "message" => array<error_message>,
);


function get_closure_variables(LambdaExpression $lambda): array<string> {
    $code = generate_lambda_code($lambda);
    $errors = check_code($code);
    $closure_variables = [];
    foreach ($errors as $error) {
        if (is_undefined_variable_error($error)) {
            $var = get_variable_by_error($error);
            if (!\is_null($var)) {
                $closure_variables[] = $var;
            }
        }
    }

    return $closure_variables;
}

function generate_lambda_code(LambdaExpression $lambda): string {
    $lc = $lambda->getCode();
    return
        "<?hh\n function codeneric_randomddndndoorgnq(){ \$codeneric_hdhbchjbcshbbs = $lc;}";
}

function check_code(string $code): array<check_error> {
    $dir = tempdir();
    \touch("$dir/.hhconfig");
    \file_put_contents("$dir/code.php", $code);
    $o = [];
    $rv = 0;
    // \ob_start();

    $json = \exec("hh_client check --json  $dir 2>&1", &$o);


    $objs = \json_decode($json, true);

    return /*UNSAFE_EXPR*/ $objs['errors'];
}

function is_undefined_variable_error(check_error $error): bool {
    foreach ($error['message'] as $m) {
        if ($m['code'] === 2050) {
            return true;
        }
    }
    return false;
}

function get_variable_by_error(check_error $error): ?string {
    foreach ($error['message'] as $m) {
        if ($m['code'] === 2050) {
            $lines = \file($m['path']);
            $line = $lines[$m['line'] - 1];
            $var = \substr($line, $m['start']-1, $m['end'] - $m['start']+1);
            return $var;
        }
    }
    return null;
}

function tempdir(): string {
    $tempfile = \tempnam(\sys_get_temp_dir(), '');
    // you might want to reconsider this line when using this snippet.
    // it "could" clash with an existing directory and this line will
    // try to delete the existing one. Handle with caution.
    if (\file_exists($tempfile)) {
        \unlink($tempfile);
    }
    \mkdir($tempfile);
    return $tempfile;
}
