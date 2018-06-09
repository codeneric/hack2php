# hack2php
hack2php is a project which aims to implement a compiler to translate Hack files to PHP files. 
This ability becomes useful when you have no control over the environment in which you code is supposed to run, but you still want to write your code in Hack.
An example might be the development of WordPress plugins or themes. 

# Disclaimer 
This project is in a very early stage, so you will probably encounter problems in some cases. If so, please create a PR and an issue as described in the Contribution section!

# Getting started
Clone this repository and run `hhvm composer.phar install`

Comiple a Hack file to PHP: `./bin/hack2php <hack-file>` 

# Tests
There is only one test (HackToPhpTest). It reads each Hack file from the example-files directory, compiles it to PHP and checks the PHP syntax for errors. If no error were found, the test succeeds. Otherwise it fails. 
To add a test, simply create a new Hack file in example-files. 
Run the test: ` hhvm vendor/bin/phpunit`  

# Contributing
If you find an issue you can help to fix it. Please add a Hack file which is not compiled correctly in the example-files folder and create a PR. 
Or you can fix the issue directly :)
But please add an example Hack file to the example-files folder nonetheless. 

