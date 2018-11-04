<?hh //strict

namespace Codeneric\Hack2PHP\Extensions;

use namespace HH\Lib\{C, Dict, Keyset, Vec};

use type \Facebook\HHAST\EditableNode;
use type \Facebook\HHAST\NamespaceToken;

function post_process_ast(EditableNode $node):void{
    $ns = $node->getChildrenWhere(($c) ==> $c instanceof NamespaceToken);
    
    
}