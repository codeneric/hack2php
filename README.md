# hack2php
[![Build Status](https://travis-ci.org/codeneric/hack2php.svg?branch=master)](https://travis-ci.org/codeneric/hack2php) 

hack2php is a project which aims to implement a compiler to translate Hack files to PHP 7 files.  
This ability becomes useful when you have no control over the environment in which your code is supposed to run, but you still want to write your code in Hack.
An example might be the development of WordPress plugins or themes.

E.g. this Hack code:

```php
<?hh //strict

namespace codeneric\phmm\legacy\validate;
use codeneric\phmm\legacy\blub;

function blub(string $v): ?string {
    return null;
}

function ano(): void {
    $a = 42;
    $arr = [1, 2, 3, 42, 5, 6];
    $f = ($e) ==> {
        return \in_array($a, $arr);
    };
}
```

is transpiled to this PHP code:

```php
<?php //strict
namespace codeneric\phmm\legacy\validate;
use \codeneric\phmm\legacy\blub;

function blub($v){
    return null;
}


function ano(){
    $a = 42;
    $arr = [1, 2, 3, 42, 5, 6];
    $f = function ($e)  use($a,$arr) {
        return \in_array($a, $arr);
    };
}
```

## Disclaimer

This project is in a very early stage, so you will probably encounter problems in some cases. If so, please create a PR and an issue as described in the Contribution section!

Development is done using version HHVM LTS (3.30), HHVM 4.X and *.hack files have not been tested yet.

## Getting started

Add this to your _composer.json_:

```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/codeneric/hack2php"
        }
    ],
```

Then run `hhvm composer.phar require --dev codeneric\hack2php dev-master`

### Compile a Hack file to PHP

First make sure you have a directory for the new php files (`mkdir <output-target>`).

Compile: `vendor/bin/hack2php <src/file.hh> > <output-target/file.php>`

To validate the php code you can run `php -l <output-target/file.php>`

## Tests

There is only one test (HackToPhpTest). It reads each Hack file from the example-files directory, compiles it to PHP and checks the PHP syntax for errors. If no error were found, the test succeeds. Otherwise it fails.  
To add a test, simply create a new Hack file in example-files.  
Run the test: `hhvm vendor/bin//hacktest tests/`

## Contributing

If you find an issue you can help to fix it. Please add a Hack file which is not compiled correctly in the example-files folder and create a PR.  
Or you can fix the issue directly :)  
But please add an example Hack file to the example-files folder nonetheless.
