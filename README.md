# hack2php
[![Build Status](https://travis-ci.org/codeneric/hack2php.svg?branch=master)](https://travis-ci.org/codeneric/hack2php) 

hack2php is a project which aims to implement a compiler to translate Hack files to PHP 5.4 files. 
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




# Disclaimer 
This project is in a very early stage, so you will probably encounter problems in some cases. If so, please create a PR and an issue as described in the Contribution section!

# Getting started
Clone this repository and run `hhvm composer.phar install`

Compile a a folder with Hack files to PHP files: `./bin/hack2php -i "tests/example-files/*.php" -o out -b tests/example-files` 

# Tests
There is only one test (HackToPhpTest). It reads each Hack file from the example-files directory, compiles it to PHP and checks the PHP syntax for errors. If no error were found, the test succeeds. Otherwise it fails. 
To add a test, simply create a new Hack file in example-files. 
Run the test: ` hhvm vendor/bin/phpunit`  

# Contributing
If you find an issue you can help to fix it. Please add a Hack file which is not compiled correctly in the example-files folder and create a PR. 
Or you can fix the issue directly :)
But please add an example Hack file to the example-files folder nonetheless. 

