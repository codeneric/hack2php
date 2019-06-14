<?hh //strict 
/*
 *  Copyright (c) 2017-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Codeneric;

use type \Facebook\HHAST\{
  //syntax:
  AliasDeclaration,
  AlternateElseClause,
  AlternateElseifClause,
  AlternateIfStatement,
  AlternateLoopStatement,
  AlternateSwitchStatement,
  AnonymousClass,
  AnonymousFunction,
  AnonymousFunctionUseClause,
  ArrayCreationExpression,
  ArrayIntrinsicExpression,
  AsExpression,
  Attribute,
  AttributeSpecification,
  AwaitableCreationExpression,
  BinaryExpression,
  BracedExpression,
  BreakStatement,
  CaseLabel,
  CastExpression,
  CatchClause,
  ClassishBody,
  ClassishDeclaration,
  ClassnameTypeSpecifier,
  ClosureParameterTypeSpecifier,
  ClosureTypeSpecifier,
  CollectionLiteralExpression,
  CompoundStatement,
  ConditionalExpression,
  ConstantDeclarator,
  ConstDeclaration,
  ConstructorCall,
  ContinueStatement,
  DarrayIntrinsicExpression,
  DarrayTypeSpecifier,
  DeclareBlockStatement,
  DeclareDirectiveStatement,
  DecoratedExpression,
  DefaultLabel,
  DefineExpression,
  DictionaryIntrinsicExpression,
  DictionaryTypeSpecifier,
  DoStatement,
  EchoStatement,
  ElementInitializer,
  ElseClause,
  ElseifClause,
  EmbeddedBracedExpression,
  EmbeddedMemberSelectionExpression,
  EmbeddedSubscriptExpression,
  EmptyExpression,
  EndOfFile,
  EnumDeclaration,
  Enumerator,
  ErrorSyntax,
  EvalExpression,
  ExpressionStatement,
  FieldInitializer,
  FieldSpecifier,
  FinallyClause,
  ForeachStatement,
  ForStatement,
  FunctionCallExpression,
  FunctionCallWithTypeArgumentsExpression,
  FunctionDeclarationHeader,
  FunctionDeclaration,
  FunctionStaticStatement,
  GenericTypeSpecifier,
  GlobalStatement,
  GotoLabel,
  GotoStatement,
  HaltCompilerExpression,
  IfStatement,
  InclusionDirective,
  InclusionExpression,
  InstanceofExpression,
  IsExpression,
  IssetExpression,
  KeysetIntrinsicExpression,
  KeysetTypeSpecifier,
  LambdaExpression,
  LambdaSignature,
  ListExpression,
  ListItem,
  LiteralExpression,
  MapArrayTypeSpecifier,
  MarkupSection,
  MarkupSuffix,
  MemberSelectionExpression,
  MethodishDeclaration,
  NamespaceBody,
  NamespaceDeclaration,
  NamespaceEmptyBody,
  NamespaceGroupUseDeclaration,
  NamespaceUseClause,
  NamespaceUseDeclaration,
  NullableAsExpression,
  NullableTypeSpecifier,
  ObjectCreationExpression,
  ParameterDeclaration,
  ParenthesizedExpression,
  Php7AnonymousFunction,
  PipeVariableExpression,
  PostfixUnaryExpression,
  PrefixUnaryExpression,
  PropertyDeclaration,
  PropertyDeclarator,
  QualifiedName,
  RequireClause,
  ReturnStatement,
  SafeMemberSelectionExpression,
  ScopeResolutionExpression,
  Script,
  ShapeExpression,
  ShapeTypeSpecifier,
  SimpleInitializer,
  SimpleTypeSpecifier,
  SoftTypeSpecifier,
  StaticDeclarator,
  SubscriptExpression,
  SwitchFallthrough,
  SwitchSection,
  SwitchStatement,
  ThrowStatement,
  TraitUseAliasItem,
  TraitUseConflictResolution,
  TraitUse,
  TraitUsePrecedenceItem,
  TryStatement,
  TupleExpression,
  TupleTypeExplicitSpecifier,
  TupleTypeSpecifier,
  TypeArguments,
  TypeConstant,
  TypeConstDeclaration,
  TypeConstraint,
  TypeParameter,
  TypeParameters,
  UnsetStatement,
  UsingStatementBlockScoped,
  UsingStatementFunctionScoped,
  VariableExpression,
  VariadicParameter,
  VarrayIntrinsicExpression,
  VarrayTypeSpecifier,
  VectorArrayTypeSpecifier,
  VectorIntrinsicExpression,
  VectorTypeSpecifier,
  WhereClause,
  WhereConstraint,
  WhileStatement,
  XHPCategoryDeclaration,
  XHPChildrenDeclaration,
  XHPChildrenParenthesizedList,
  XHPClassAttributeDeclaration,
  XHPClassAttribute,
  XHPClose,
  XHPEnumType,
  XHPExpression,
  XHPOpen,
  XHPRequired,
  XHPSimpleAttribute,
  XHPSimpleClassAttribute,
  XHPSpreadAttribute,
  YieldExpression,
  YieldFromExpression,

  //token:
  AbstractToken,
  AmpersandAmpersandToken,
  AmpersandEqualToken,
  AmpersandToken,
  AndToken,
  ArraykeyToken,
  ArrayToken,
  AsToken,
  AsyncToken,
  AtToken,
  AttributeToken,
  AwaitToken,
  BackslashToken,
  BarBarToken,
  BarEqualToken,
  BarGreaterThanToken,
  BarToken,
  BinaryLiteralToken,
  BooleanLiteralToken,
  BoolToken,
  BreakToken,
  CaratEqualToken,
  CaratToken,
  CaseToken,
  CatchToken,
  CategoryToken,
  ChildrenToken,
  ClassnameToken,
  ClassToken,
  CloneToken,
  ColonColonToken,
  ColonToken,
  CommaToken,
  ConstructToken,
  ConstToken,
  ContinueToken,
  CoroutineToken,
  DarrayToken,
  DecimalLiteralToken,
  DeclareToken,
  DefaultToken,
  DefineToken,
  DestructToken,
  DictToken,
  DollarDollarToken,
  DollarToken,
  DotDotDotToken,
  DotEqualToken,
  DoToken,
  DotToken,
  DoubleQuotedStringLiteralHeadToken,
  DoubleQuotedStringLiteralTailToken,
  DoubleQuotedStringLiteralToken,
  DoubleToken,
  EchoToken,
  ElseifToken,
  ElseToken,
  EmptyToken,
  EnddeclareToken,
  EndforeachToken,
  EndforToken,
  EndifToken,
  EndOfFileToken,
  EndswitchToken,
  EndwhileToken,
  EnumToken,
  EqualEqualEqualToken,
  EqualEqualGreaterThanToken,
  EqualEqualToken,
  EqualGreaterThanToken,
  EqualToken,
  ErrorTokenToken,
  EvalToken,
  ExclamationEqualEqualToken,
  ExclamationEqualToken,
  ExclamationToken,
  ExecutionStringLiteralHeadToken,
  ExecutionStringLiteralTailToken,
  ExecutionStringLiteralToken,
  ExtendsToken,
  FallthroughToken,
  FinallyToken,
  FinalToken,
  FloatingLiteralToken,
  FloatToken,
  ForeachToken,
  ForToken,
  FromToken,
  FunctionToken,
  GlobalToken,
  GotoToken,
  GreaterThanEqualToken,
  GreaterThanGreaterThanEqualToken,
  GreaterThanGreaterThanToken,
  GreaterThanToken,
  HaltCompilerToken,
  HeredocStringLiteralHeadToken,
  HeredocStringLiteralTailToken,
  HeredocStringLiteralToken,
  HexadecimalLiteralToken,
  IfToken,
  ImplementsToken,
  Include_onceToken,
  IncludeToken,
  InoutToken,
  InstanceofToken,
  InsteadofToken,
  InterfaceToken,
  IntToken,
  IssetToken,
  IsToken,
  KeysetToken,
  LeftBraceToken,
  LeftBracketToken,
  LeftParenToken,
  LessThanEqualGreaterThanToken,
  LessThanEqualToken,
  LessThanGreaterThanToken,
  LessThanLessThanEqualToken,
  LessThanLessThanToken,
  LessThanQuestionToken,
  LessThanSlashToken,
  LessThanToken,
  ListToken,
  MarkupToken,
  MinusEqualToken,
  MinusGreaterThanToken,
  MinusMinusToken,
  MinusToken,
  MixedToken,
  NamespaceToken,
  NameToken,
  NewToken,
  NewtypeToken,
  NoreturnToken,
  NowdocStringLiteralToken,
  NullLiteralToken,
  NumToken,
  ObjectToken,
  OctalLiteralToken,
  OrToken,
  ParentToken,
  PercentEqualToken,
  PercentToken,
  PlusEqualToken,
  PlusPlusToken,
  PlusToken,
  PrintToken,
  PrivateToken,
  ProtectedToken,
  PublicToken,
  QuestionAsToken,
  QuestionColonToken,
  QuestionGreaterThanToken,
  QuestionMinusGreaterThanToken,
  QuestionQuestionToken,
  QuestionToken,
  RequiredToken,
  Require_onceToken,
  RequireToken,
  ResourceToken,
  ReturnToken,
  RightBraceToken,
  RightBracketToken,
  RightParenToken,
  SelfToken,
  SemicolonToken,
  ShapeToken,
  SingleQuotedStringLiteralToken,
  SlashEqualToken,
  SlashGreaterThanToken,
  SlashToken,
  StarEqualToken,
  StarStarEqualToken,
  StarStarToken,
  StarToken,
  StaticToken,
  StringLiteralBodyToken,
  StringToken,
  SuperToken,
  SuspendToken,
  SwitchToken,
  ThisToken,
  ThrowToken,
  TildeToken,
  TraitToken,
  TryToken,
  TupleToken,
  TypeToken,
  UnsetToken,
  UseToken,
  UsingToken,
  VariableToken,
  VarrayToken,
  VarToken,
  VecToken,
  VoidToken,
  WhereToken,
  WhileToken,
  XHPBodyToken,
  XHPCategoryNameToken,
  XHPClassNameToken,
  XHPCommentToken,
  XHPElementNameToken,
  XHPStringLiteralToken,
  XorToken,
  YieldToken,

  //freestyle: 
  EditableNode,
  Missing,
  EditableList,
  EditableToken,

};
use function \Facebook\HHAST\{Missing, find_position, find_offset};
use namespace \Facebook\TypeAssert;
use namespace \HH\Lib\{C, Vec};

require_once __DIR__.'/Util.php';

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

function get_php_markup(): MarkupSection {
  $ast = \Facebook\HHAST\from_code("<?php\n");
  invariant($ast instanceof Script, 'AST has to be of type Script!');
  $expressions = $ast
    ->getDeclarations()
    ->getChildren();
  foreach ($expressions as $expression) {
    invariant(
      $expression instanceof MarkupSection,
      'AST has to be of type MarkupSection!',
    );
    return $expression;
  }
  invariant(false, 'ast_from_code failed!');
}

function interate_children(
  EditableNode $node,
  vec<EditableNode> $parents,
  string $php,
): string {
  $next_nodes = $node
    ->getChildren();
  $parents[] = $node;
  foreach ($next_nodes as $next_node) {
    $php = transpile($next_node, $parents, $php);
  }
  return $php;
}

function placeholder(): string {
  return "QQQQQQQQQQQQQQQQQQQ";
}

function sprinft(string $format, string $arg): string {
  // \var_dump($format);

  // return \sprintf($format, $arg);
  return \str_replace(placeholder(), $arg, $format);
}

function apply_ast_filter(
  EditableNode $node,
  EditableNode $child,
): EditableNode {
  //UNSAFE
  if (\file_exists('./ast_filters.php')) {
    require_once('./ast_filters.php');
    $filters = \Codeneric\Hack2PHP\Filters\get_filters();
    foreach ($filters as $filter) {
      $node = $filter($node, $child);
    }
  }
  return $node;
}

function transpile(
  EditableNode $node,
  vec<EditableNode> $parents,
  string $php,
): string {
  $P = placeholder();

  if ($node instanceof Script) {


    $next_nodes = $node
      ->getDeclarations();
    $php = transpile($next_nodes, $parents, $php);
    return $php;
  }

  $childs = $node->getChildren();
  foreach ($childs as $key => $child) {
    $node = apply_ast_filter($node, $child);
    if ($child instanceof SafeMemberSelectionExpression) {

      $safe_member_object = $child->getObject()->getCode();
      $safe_member_name = $child->getName()->getCode();

      $sub_ast = ast_from_code(
        "is_null($safe_member_object) ? null : $safe_member_object->$safe_member_name",
      );
      $node = $node->replace($child, $sub_ast);
    }

    if ($child instanceof MarkupSection) {
      $old_suffix_name = $child->getSuffix()?->getName();
      if (!\is_null($old_suffix_name)) {
        $suffix_name = new NameToken(
          $old_suffix_name->getLeading(),
          $old_suffix_name->getTrailing(),
          'php',
        );
        $php_markup = get_php_markup();
        $node = $node->replace($old_suffix_name, $suffix_name);
      }
    }

    if ($child instanceof CollectionLiteralExpression) {
      $name = $child->getName();
      invariant(
        $name instanceof SimpleTypeSpecifier,
        "Has to be SimpleTypeSpecifier!",
      );
      $t =
        $name->getSpecifier()->getCode() |> \trim($$); //TODO: dont trim shit!
      if ($t === "Map") {
        // $initializers = $child->getInitializers()?->getCode();
        $initializers = $child->getInitializers();
        if (!\is_null($initializers)) {
          $elements = $initializers->getChildren();
          $ks = [];
          $vs = [];
          foreach ($elements as $li) {
            invariant($li instanceof ListItem, "Has to be ListItem!");
            $li = $li->getItem();
            invariant(
              $li instanceof ElementInitializer,
              "Has to be ElementInitializer!",
            );
            $ks[] = $li->getKey()->getCode();
            $vs[] = $li->getValue()->getCode();

          }
          $ks_str = \implode(',', $ks);
          $vs_str = \implode(',', $vs);
          $sub_ast = ast_from_code(
            "\\HH\\Map::hacklib_new(array($ks_str), array($vs_str))",
          );
        } else {
          $sub_ast = ast_from_code("\\HH\\Map::hacklib_new(array(), array())");
        }

        $node = $node->replace($child, $sub_ast);
      }
      if ($t === "Vector") {
        // $initializers = $child->getInitializers()?->getCode();
        $initializers = $child->getInitializers();
        if (!\is_null($initializers)) {
          $code = $initializers->getCode();
          $sub_ast = ast_from_code("\\HH\\Vector::hacklib_new(array($code))");
        } else {
          $sub_ast = ast_from_code("new \\HH\\Vector(array())");
        }

        $node = $node->replace($child, $sub_ast);
      }


      $node = $node->removeWhere(($n, $v) ==> $name === $n);

    }

    if ($child instanceof EnumDeclaration) {
      // $enum_keyword = $node->getKeyword()->getCode();
      $enum_name = $child->getName()->getCode();
      $enumerators = $child->getEnumerators()?->getChildren();
      $enumerators = !\is_null($enumerators) ? $enumerators : [];
      $code = "final class $enum_name { private function __construct() {} \n";


      $code .= "private static \$hacklib_values = array(\n";
      foreach ($enumerators as $i => $e) {
        invariant(
          $e instanceof Enumerator,
          'Children of EnumDeclaration has to be Enumerator',
        );
        $e_name = $e->getName()->getText();
        $e_value = $e->getValue()->getCode();
        $sep = ((int)$i) >= \count($enumerators) - 1 ? '' : ',';
        $code .= "\"$e_name\" => $e_value $sep\n";
      }
      $code .= ");\n";

      $code .= "use \HH\HACKLIB_ENUM_LIKE;\n";
      foreach ($enumerators as $e) {
        invariant(
          $e instanceof Enumerator,
          'Children of EnumDeclaration has to be Enumerator',
        );
        $e_name = $e->getName()->getText();
        $e_value = $e->getValue()->getCode();
        $code .= "const $e_name = $e_value;\n";
      }
      $code .= " }\n";
      $sub_ast = ast_from_code($code);
      $node = $node->replace($child, $sub_ast);
    }

    if ($child instanceof AliasDeclaration) {
      $node = $node->removeWhere(($n, $v) ==> $child === $n);
    }

    if ($child instanceof FunctionCallExpression) {

      $receiver = $child->getReceiver();

      if ($receiver instanceof NameToken) {
        $text = $receiver->getText();
        // echo "\nNameToken: |$text|\n";
        if ($text === 'invariant') {
          // $code = '\\HH\\'.$child->getCode();
          $args_list = $child->getArgumentList()?->getCode();
          $sub_ast = ast_from_code(
            "\\HH\\invariant($args_list)",
          ); //removes leadning and trailing arrays
          $node = $node->replace($child, $sub_ast);
        }
      }

      // if (
      //   $receiver instanceof QualifiedName || $receiver instanceof NameToken
      // ) {
      //   // $text = $receiver->getParts()->getCode();

      //   $fn = '';
      //   // $a = $receiver->getParts();
      //   $a = $receiver->getDescendantsOfType(EditableToken::class);
      //   // \var_dump($a);
      //   foreach ($a as $b) {

      //     $fn .= $b->getText();

      //   }

      //   if ($fn === 'invariant') {
      //     // $code = '\\HH\\'.$child->getCode();
      //     $args_list = $child->getArgumentList()?->getCode();
      //     $sub_ast = ast_from_code(
      //       "\\HH\\invariant($args_list)",
      //     ); //removes leadning and trailing arrays
      //     $node = $node->replace($child, $sub_ast);
      //   }

      //   // if ($fn === '\\__' || $fn === '__') {
      //   //   $args_list = $child->getArgumentList();
      //   //   if (!\is_null($args_list) && \count($args_list->getItems()) === 1) {
      //   //     $args_list_code = $args_list->getCode();
      //   //     $sub_ast = ast_from_code(
      //   //       "$fn($args_list_code, 'photography-management')",
      //   //     ); //removes leadning and trailing arrays
      //   //     $node = $node->replace($child, $sub_ast);
      //   //   }
      //   // }
      // }


    }

    if ($child instanceof LambdaExpression) {
      $closure_vars = \codeneric\util\get_closure_variables($child);
      $attributeSpec = $child->getSignature()->getCode();
      $body = $child->getBody()->getCode();
      $use = \count($closure_vars) > 0
        ? 'use('.\implode(',', $closure_vars).')'
        : '';
      $code = "function $attributeSpec $use $body";
      $sub_ast = ast_from_code($code);
      $node = $node->replace($child, $sub_ast);

    }


  }


  // if ($node instanceof MarkupSection) { //abstraction

  //   $php = sprinft($php, "<?php\n$P");
  //   return $php;
  // }

  if ($node instanceof NamespaceDeclaration) {
    $code = $node
      ->getName()
      ->getCode();
    $php = sprinft($php, "namespace $code$P");

    $parents[] = $node;
    $php = transpile($node->getBody(), $parents, $php);

    return $php;
  }

  if ($node instanceof NamespaceEmptyBody) {
    $php = sprinft($php, ";\n$P");

    return $php;
  }
  if ($node instanceof NamespaceUseDeclaration) { //abstraction
    $ns_cmd = $node->getClauses()->getCode();
    $ft = $node->getClauses()->getFirstToken();
    if (
      !\is_null($ft) && $ft->getTokenKind() !== "\\"
    ) { //TODO: mutate AST accordingly!
      $bs = new BackslashToken($ft->getLeading(), $ft);
      $node = $node->replace($ft, $bs);
      // $node = $node->insertBefore($ft, $bs);
      // $php = sprinft($php, "use \\$ns_cmd;\n$P");
    } else {
      // $php = sprinft($php, "use $ns_cmd;\n$P");
    }

    $php = interate_children($node, $parents, $php);
    return $php;
  }


  if ($node instanceof FunctionCallExpression) {
    $args = $node->getArgumentList();
    if (!\is_null($args)) {
      $last_token = $args->getLastToken();
      if ($last_token instanceof CommaToken) {
        $node = $node->removeWhere(($n, $v) ==> $last_token === $n);
      }
    }
    $php = interate_children($node, $parents, $php);
    return $php;
  }


  if ($node instanceof FunctionDeclarationHeader) {
    $type = $node->getType();
    $args = $node->getParameterList();

    if (!\is_null($args)) {
      $last_token = $args->getLastToken();
      if ($last_token instanceof CommaToken) {
        $node = $node->removeWhere(($n, $v) ==> $last_token === $n);
      }
    }

    $node = $node->removeWhere(
      ($n, $v) ==> $n instanceof SimpleTypeSpecifier ||
        $n instanceof ColonToken ||
        $n instanceof VectorArrayTypeSpecifier ||
        $n instanceof TypeParameters ||
        $n instanceof MapArrayTypeSpecifier ||
        $n instanceof ShapeTypeSpecifier ||
        $n instanceof ClosureTypeSpecifier ||
        $n instanceof NullableTypeSpecifier ||
        $n instanceof TupleTypeSpecifier ||
        $n instanceof GenericTypeSpecifier ||
        $n === $type,
    );
    $php = interate_children($node, $parents, $php);
    return $php;
  }

  if ($node instanceof AnonymousFunction) {
    $colon = $node->getColon();
    $type = $node->getType();
    $args = $node->getParameters();
    if (!\is_null($args)) {
      $last_token = $args->getLastToken();
      if ($last_token instanceof CommaToken) {
        $node = $node->removeWhere(($n, $v) ==> $last_token === $n);
      }
      invariant(
        $node instanceof AnonymousFunction,
        'node has to be of type AnonymousFunction.',
      );

      $args = $node->getParameters();

      if (!\is_null($args)) {
        $args_new = $args->removeWhere(
          ($n, $v) ==> $n instanceof SimpleTypeSpecifier ||
            $n instanceof ColonToken ||
            $n instanceof VectorArrayTypeSpecifier ||
            $n instanceof TypeParameters ||
            $n instanceof MapArrayTypeSpecifier ||
            $n instanceof ShapeTypeSpecifier ||
            $n instanceof ClosureTypeSpecifier ||
            $n instanceof NullableTypeSpecifier ||
            $n instanceof GenericTypeSpecifier ||
            $n instanceof TupleTypeSpecifier ||
            $n === $type,
        );
        $node = $node->replace($args, $args_new);


      }


    }
    $node = $node->removeWhere(($n, $v) ==> $n === $colon || $n === $type);

    $php = interate_children($node, $parents, $php);
    return $php;
  }


  if ($node instanceof AnonymousFunctionUseClause) {
    $args = $node->getVariables();
    if (!\is_null($args)) {
      $last_token = $args->getLastToken();
      if ($last_token instanceof CommaToken) {
        $node = $node->removeWhere(($n, $v) ==> $last_token === $n);
      }
      $args_new = $args->removeWhere(
        ($n, $v) ==> $n instanceof SimpleTypeSpecifier ||
          $n instanceof ColonToken ||
          $n instanceof VectorArrayTypeSpecifier ||
          $n instanceof TypeParameters ||
          $n instanceof MapArrayTypeSpecifier ||
          $n instanceof ShapeTypeSpecifier ||
          $n instanceof ClosureTypeSpecifier ||
          $n instanceof NullableTypeSpecifier ||
          $n instanceof GenericTypeSpecifier ||
          $n instanceof TupleTypeSpecifier,
      );
      $node = $node->replace($args, $args_new);
    }
    $php = interate_children($node, $parents, $php);
    return $php;
  }


  if (
    $node instanceof PropertyDeclaration || $node instanceof ConstDeclaration
  ) {
    $node = $node->removeWhere(
      ($n, $v) ==> $n instanceof SimpleTypeSpecifier ||
        $n instanceof ColonToken ||
        $n instanceof VectorArrayTypeSpecifier ||
        $n instanceof TypeParameters ||
        $n instanceof MapArrayTypeSpecifier ||
        $n instanceof ShapeTypeSpecifier ||
        $n instanceof ClosureTypeSpecifier ||
        $n instanceof NullableTypeSpecifier ||
        $n instanceof GenericTypeSpecifier ||
        $n instanceof TupleTypeSpecifier,
    );
    $php = interate_children($node, $parents, $php);
    return $php;
  }


  if ($node instanceof ConstructorCall) {

    $args = $node->getArgumentList();
    if (!\is_null($args)) {
      $last_token = $args->getLastToken();
      if ($last_token instanceof CommaToken) {
        $node = $node->removeWhere(($n, $v) ==> $last_token === $n);
      }
    }

    $php = interate_children($node, $parents, $php);
    return $php;
  }


  if ($node instanceof TupleExpression) {

    $token = $node->getKeyword();
    $array_token = new ArrayToken($token->getLeading(), $token->getTrailing());
    $node = $node->replace($token, $array_token);
    $php = interate_children($node, $parents, $php);
    return $php;
  }


  if ($node instanceof ShapeExpression) {

    $shape_token = $node->getKeyword();
    $array_token =
      new ArrayToken($shape_token->getLeading(), $shape_token->getTrailing());
    $node = $node->replace($shape_token, $array_token);
    $php = interate_children($node, $parents, $php);

    return $php;
  }

  //Token stuff:


  if (
    $node instanceof StaticDeclarator ||
    $node instanceof InclusionExpression ||
    $node instanceof InclusionDirective ||
    $node instanceof LiteralExpression ||
    $node instanceof CompoundStatement ||
    $node instanceof BinaryExpression ||
    $node instanceof VariableExpression ||
    $node instanceof ScopeResolutionExpression ||
    $node instanceof ClassishDeclaration ||
    $node instanceof ClassishBody ||
    $node instanceof PropertyDeclarator ||
    $node instanceof SimpleInitializer ||
    $node instanceof ConstDeclaration ||
    $node instanceof ConstantDeclarator ||
    $node instanceof FunctionStaticStatement ||
    $node instanceof InstanceofExpression ||
    $node instanceof QualifiedName ||
    $node instanceof MemberSelectionExpression ||
    $node instanceof EmbeddedMemberSelectionExpression ||
    $node instanceof ArrayIntrinsicExpression ||
    $node instanceof ForeachStatement ||
    $node instanceof CastExpression ||
    $node instanceof EmbeddedBracedExpression ||
    $node instanceof SwitchStatement ||
    $node instanceof SwitchSection ||
    $node instanceof CaseLabel ||
    $node instanceof BreakStatement ||
    $node instanceof DefaultLabel ||
    $node instanceof EchoStatement ||
    $node instanceof EmptyExpression ||
    $node instanceof TryStatement ||
    $node instanceof CatchClause ||
    $node instanceof IssetExpression ||
    $node instanceof UnsetStatement ||
    $node instanceof TraitUse ||
    $node instanceof DefineExpression ||
    $node instanceof ListExpression ||
    $node instanceof ThrowStatement ||
    $node instanceof MarkupSection ||
    $node instanceof MarkupSuffix ||
    $node instanceof WhileStatement ||
    $node instanceof ElseifClause ||
    $node instanceof ForStatement ||
    $node instanceof PostfixUnaryExpression ||
    $node instanceof ElementInitializer ||
    $node instanceof FieldInitializer ||
    $node instanceof ParenthesizedExpression ||
    $node instanceof SubscriptExpression ||
    $node instanceof ConditionalExpression ||
    $node instanceof SimpleTypeSpecifier ||
    $node instanceof ArrayCreationExpression ||
    $node instanceof ListItem ||
    $node instanceof ParameterDeclaration ||
    $node instanceof IfStatement ||
    $node instanceof PrefixUnaryExpression ||
    $node instanceof ReturnStatement ||
    $node instanceof ElseClause ||
    $node instanceof ObjectCreationExpression ||
    $node instanceof ClassishDeclaration ||
    $node instanceof NamespaceBody ||
    $node instanceof ExpressionStatement ||
    $node instanceof FunctionDeclaration ||
    $node instanceof MethodishDeclaration ||
    $node instanceof Missing ||
    $node instanceof ContinueStatement ||
    $node instanceof BracedExpression ||
    $node instanceof DecoratedExpression ||
    $node instanceof EvalExpression ||
    $node instanceof DoStatement ||
    $node instanceof GlobalStatement ||
    $node instanceof FinallyClause ||
    $node instanceof NamespaceUseClause ||
    $node instanceof YieldExpression ||
    $node instanceof AnonymousClass ||
    $node instanceof CollectionLiteralExpression ||
    $node instanceof EmbeddedSubscriptExpression ||
    $node instanceof AliasDeclaration
  ) {
    $php = interate_children($node, $parents, $php);
    return $php;
  }


  if ($node instanceof EndOfFile) {
    $php = sprinft($php, "");
    return $php;
  }


  //HERE BEGINS VERY GENERAL STUFF

  if ($node instanceof EditableList) {
    $php = interate_children($node, $parents, $php);
    return $php;
  }

  // if ($node instanceof TypeToken) {
  //   return $php;
  // }

  if ($node->isToken()) { //abstraction    
    $token = $node->getCode();

    $php = sprinft($php, "$token$P");

    return $php;
  }

  // $php = interate_children($node, $parents, $php);
  // return $php;


  throw new \Error(
    "Unknown node (".
    $node->getSyntaxKind().
    "): ".
    $node->getCode().
    " \nCurrent PHP: \n$php",
  );
}

type hooks = shape(
  "ast_after_transpile" => ?(function(EditableNode): EditableNode),
);


function run(string $filename): string {
  $en = \Facebook\HHAST\from_file($filename);
  return transpile($en, vec[], placeholder());
}
