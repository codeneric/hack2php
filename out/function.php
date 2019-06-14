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


$processAddon = function(
$scripthandle,
$pathFilter,
$globalsVarName,
$globalsFilter,
$dependencies,
$version){};
