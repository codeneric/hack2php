<?hh //strict

namespace Codeneric\Hack2PHP\Filters;
use type \Facebook\HHAST\{
  EditableNode,
  FunctionCallExpression,
  QualifiedName,
  NameToken,
  EditableToken,
  MarkupSection,
  Script,
  CommaToken,
  EditableList,
};


function ast_from_code(string $code): EditableNode {
  $ast = \Facebook\HHAST\from_code($code);
  $ast = $ast->removeWhere(($n, $v) ==> $n instanceof MarkupSection);
  invariant($ast instanceof Script, 'AST has to be of type Script!');
  $expressions = $ast
    ->getDeclarations()
    ->getChildren();
  foreach ($expressions as $expression) {
    return $expression;
  }
  invariant(false, 'ast_from_code failed!');
}

function add_i18n_domain(
  string $domain,
  FunctionCallExpression $child,
): FunctionCallExpression {
  $receiver = $child->getReceiver();
  $args_list = $child->getArgumentList();
  if (!\is_null($args_list)) {
    $last_token = $args_list->getLastToken();
    $new_args_list = $args_list;
    if ($last_token instanceof CommaToken) {
      $new_args_list = $args_list->removeWhere(($n, $v) ==> $last_token === $n);
    }
    invariant(
      $new_args_list instanceof EditableList,
      'new_args_list has to be instance of EditableList',
    );


    if (\count($new_args_list->getItems()) === 1) {
      $args_list_code = $new_args_list->getCode();
      $f_call = $receiver->getCode();
      $sub_ast = ast_from_code(
        "$f_call($args_list_code, '$domain')",
      ); //removes leadning and trailing arrays
      //   $node = $node->replace($child, $sub_ast);
      $res = $sub_ast->getDescendantsOfType(FunctionCallExpression::class);
      $elems = [];
      foreach ($res as $fce) {
        $elems[] = $fce;
      }

      invariant(
        \count($elems) === 1,
        'there are more FunctionCallExpressions than expected',
      );
      return $elems[0];
    }
    return $child;
  } else {
    invariant(false, 'args_list is empty in an i18n function!');

  }
}


function get_filters(
): array<(function(EditableNode, EditableNode): EditableNode)> {

  $i18n_filter =
    function(EditableNode $node, EditableNode $child): EditableNode {
      if ($child instanceof FunctionCallExpression) {

        $receiver = $child->getReceiver();


        if ($receiver instanceof QualifiedName) {
          $fn = '';
          $a = $receiver->getDescendantsOfType(NameToken::class);
          foreach ($a as $b) {
            $fn .= $b->getText();
          }

          if ($fn === '__') {
            $sub_ast = add_i18n_domain('photography-management', $child);
            $node = $node->replace($child, $sub_ast);
            return $node;
          }
        }
        if ($receiver instanceof NameToken) {
          $fn = $receiver->getText();

          if ($fn === '__') {
            $sub_ast = add_i18n_domain('photography-management', $child);
            $node = $node->replace($child, $sub_ast);
            return $node;
          }
        }


      }
      return $node;
    };

  return [$i18n_filter];
}
