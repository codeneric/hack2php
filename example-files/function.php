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


$processAddon = function(
    string $scripthandle,
    string $pathFilter,
    string $globalsVarName,
    string $globalsFilter,
    array<string> $dependencies,
    string $version,
): void {};
