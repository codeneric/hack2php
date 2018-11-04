<?hh //strict

namespace codeneric\phmm\base\globals;

class Superglobals {

  static function Server(): array<string, mixed> {
    return /*UNSAFE_EXPR*/ $_SERVER;
  }

  static function Get(): array<string, string> {
    return /*UNSAFE_EXPR*/ $_GET;
  }

  static function Post(): array<string, mixed> {
    return /*UNSAFE_EXPR*/ $_POST;
  }

  static function Files(): array<string, mixed> {
    return /*UNSAFE_EXPR*/ $_FILES;
  }

  static function Cookie(): array<string, mixed> {
    return /*UNSAFE_EXPR*/ $_COOKIE;
  }

  static function Session(): array<string, mixed> {
    return /*UNSAFE_EXPR*/ $_SESSION;
  }

  static function Request(): array<string, mixed> {
    return /*UNSAFE_EXPR*/ $_REQUEST;
  }

  static function Env(): array<string, mixed> {
    return /*UNSAFE_EXPR*/ $_ENV;
  }
  static function Globals(string $key): mixed {
    $global = /*UNSAFE_EXPR*/ $GLOBALS;

    if (array_key_exists($key, $global))
      return $global[$key];

    return null;
  }
}

