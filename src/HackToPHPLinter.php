<?hh // strict 
/*
 *  Copyright (c) 2017-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Facebook\HHAST\Linters;

use type Facebook\HHAST\{
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

};
use function Facebook\HHAST\{Missing, find_position, find_offset};
use namespace Facebook\TypeAssert;
use namespace HH\Lib\{C, Vec};

final class HackToPHPLinter extends ASTLinter<EditableNode> {

  private string $placeholder =
    "kcnvjrhthkndfnkdfknknsdnks"; //make this smarter
  <<__Override>>
  protected static function getTargetType(): classname<Script> {
    return Script::class;
  }

  <<__Override>>
  public function getLintErrorForNode(
    EditableNode $node,
    vec<EditableNode> $parents,
  ): ?ASTLintError<EditableNode> {
    exit($this->transpile($node, $parents, $this->placeholder));
  }

  private function ast_from_code(string $code): EditableNode {
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

  private function get_php_markup(): MarkupSection {
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


  private function transpile(
    EditableNode $node,
    vec<EditableNode> $parents,
    string $php,
  ): string {
    $P = $this->placeholder;

    if ($node instanceof Script) {


      $next_nodes = $node
        ->getDeclarations();
      $php = $this->transpile($next_nodes, $parents, $php);
      return $php;
    }

    $childs = $node->getChildren();
    foreach ($childs as $key => $child) {
      if ($child instanceof SafeMemberSelectionExpression) {

        $safe_member_object = $child->getObject()->getCode();
        $safe_member_name = $child->getName()->getCode();

        $sub_ast = $this->ast_from_code(
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
          $php_markup = $this->get_php_markup();
          $node = $node->replace($old_suffix_name, $suffix_name);
        }
      }

      if ($child instanceof CollectionLiteralExpression) {
        $initializers = $child->getInitializers()?->getCode();
        $sub_ast =
          $this->ast_from_code("\\HH\\Map::hacklib_new(array($initializers))");
        $node = $node->replace($child, $sub_ast);
        // $node = $this->transform_ast($node);
      }

      if ($child instanceof EnumDeclaration) {
        // $enum_keyword = $node->getKeyword()->getCode();
        $enum_name = $child->getName()->getCode();
        $enumerators = $child->getEnumerators()?->getChildren();
        $enumerators = !\is_null($enumerators) ? $enumerators : [];
        $code = "final class $enum_name { private function __construct() {} \n";
        foreach ($enumerators as $e) {
          invariant(
            $e instanceof Enumerator,
            'Children of EnumDeclaration has to be Enumerator',
          );
          $e_name = $e->getName()->getCode();
          $e_value = $e->getValue()->getCode();
          $code .= "const $e_name = $e_value;\n";
        }
        $code .= " }\n";
        $sub_ast = $this->ast_from_code($code);
        $node = $node->replace($child, $sub_ast);
      }

      if ($child instanceof AliasDeclaration) {
        $node = $node->removeWhere(($n, $v) ==> $child === $n);
      }


    }


    // if ($node instanceof MarkupSection) { //abstraction

    //   $php = $this->sprinft($php, "<?php\n$P");
    //   return $php;
    // }

    if ($node instanceof NamespaceDeclaration) {
      $code = $node
        ->getName()
        ->getCode();
      $php = $this->sprinft($php, "namespace $code$P");

      $parents[] = $node;
      $php = $this->transpile($node->getBody(), $parents, $php);

      return $php;
    }

    if ($node instanceof NamespaceEmptyBody) {
      $php = $this->sprinft($php, ";\n$P");

      return $php;
    }
    if ($node instanceof NamespaceUseDeclaration) { //abstraction
      $ns_cmd = $node->getClauses()->getCode();
      $ft = $node->getClauses()->getFirstToken();
      if (
        !\is_null($ft) && $ft->getTokenKind() !== "\\"
      ) { //TODO: mutate AST accordingly!
        $php = $this->sprinft($php, "use \\$ns_cmd;\n$P");
      } else {
        $php = $this->sprinft($php, "use $ns_cmd;\n$P");
      }

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
      $php = $this->interate_children($node, $parents, $php);
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
      $php = $this->interate_children($node, $parents, $php);
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

      $node = $node->removeWhere(($n, $v) ==> $n === $colon || $n === $type);
      $php = $this->interate_children($node, $parents, $php);
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
      $php = $this->interate_children($node, $parents, $php);
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
      $php = $this->interate_children($node, $parents, $php);
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

      $php = $this->interate_children($node, $parents, $php);
      return $php;
    }


    if ($node instanceof TupleExpression) {

      $token = $node->getKeyword();
      $array_token =
        new ArrayToken($token->getLeading(), $token->getTrailing());
      $node = $node->replace($token, $array_token);
      $php = $this->interate_children($node, $parents, $php);
      return $php;
    }


    if ($node instanceof ShapeExpression) {

      $shape_token = $node->getKeyword();
      $array_token =
        new ArrayToken($shape_token->getLeading(), $shape_token->getTrailing());
      $node = $node->replace($shape_token, $array_token);
      $php = $this->interate_children($node, $parents, $php);

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
      $node instanceof AliasDeclaration
    ) {
      $php = $this->interate_children($node, $parents, $php);
      return $php;
    }


    if ($node instanceof EndOfFile) {
      $php = $this->sprinft($php, "");
      return $php;
    }


    //HERE BEGINS VERY GENERAL STUFF

    if ($node instanceof EditableList) {
      $php = $this->interate_children($node, $parents, $php);
      return $php;
    }

    // if ($node instanceof TypeToken) {
    //   return $php;
    // }

    if ($node->isToken()) { //abstraction    
      $token = $node->getCode();

      $php = $this->sprinft($php, "$token$P");

      return $php;
    }

    // $php = $this->interate_children($node, $parents, $php);
    // return $php;


    throw new \Error(
      "Unknown node (".
      $node->getSyntaxKind().
      "): ".
      $node->getCode().
      " \nCurrent PHP: \n$php",
    );
  }


  private static function isAsyncBoundary(Script $node): bool {
    return $node instanceof AnonymousFunction ||
      $node instanceof AwaitableCreationExpression ||
      $node instanceof LambdaExpression;
  }


  private function interate_children(
    EditableNode $node,
    vec<EditableNode> $parents,
    string $php,
  ): string {
    $next_nodes = $node
      ->getChildren();
    $parents[] = $node;
    foreach ($next_nodes as $next_node) {
      $php = $this->transpile($next_node, $parents, $php);
    }
    return $php;
  }

  private function sprinft(string $format, string $arg): string {
    // \var_dump($format);

    // return \sprintf($format, $arg);
    return \str_replace($this->placeholder, $arg, $format);
  }
}
