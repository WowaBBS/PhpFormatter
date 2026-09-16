<?

$TokenTypes=[
// KeyWords:
  'T_AS'             => 'as'            , //Operator?
  'T_CASE'           => 'case'          ,
  'T_CATCH'          => 'catch'         ,
  'T_DECLARE'        => 'declare'       ,
  'T_EXTENDS'        => 'extends'       ,
  'T_IMPLEMENTS'     => 'implements'    ,
  'T_INSTEADOF'      => 'insteadof'     ,
// Control:
  'T_BREAK'          => 'break'         ,
  'T_CLASS'          => 'class'         ,
  'T_CONTINUE'       => 'continue'      ,
  'T_DEFAULT'        => 'default'       , //?
  'T_DO'             => 'do'            ,
  'T_ELSE'           => 'else'          ,
  'T_ELSEIF'         => 'elseif'        ,
  'T_ENDDECLARE'     => 'enddeclare'    ,
  'T_ENDFOR'         => 'endfor'        ,
  'T_ENDFOREACH'     => 'endforeach'    ,
  'T_ENDIF'          => 'endif'         ,
  'T_ENDSWITCH'      => 'endswitch'     ,
  'T_ENDWHILE'       => 'endwhile'      ,

  'T_GOTO'           => 'goto'          ,
  'T_IF'             => 'if'            ,
  'T_MATCH'          => 'match'         ,
  'T_SWITCH'         => 'switch'        ,
  'T_TRY'            => 'try'           ,
  'T_WHILE'          => 'while'         ,
  'T_FOR'            => 'for'           ,
  'T_FOREACH'        => 'foreach'       ,
  'T_FINALLY'        => 'finally'       ,
// Declaration:
  'T_CONST'          => 'const'         ,
  'T_ENUM'           => 'enum'          ,
  'T_INTERFACE'      => 'interface'     ,
  'T_NAMESPACE'      => 'namespace'     ,
  'T_TRAIT'          => 'trait'         ,
  'T_VAR'            => 'var'           ,
  'T_USE'            => 'use'           ,
  'T_FUNCTION'       => 'function'      ,
  'T_FN'             => 'fn'            ,
  'T_GLOBAL'         => 'global'        ,
// Prefixes:
  'T_ABSTRACT'       => 'abstract'      ,
  'T_FINAL'          => 'final'         ,
  'T_PRIVATE'        => 'private'       ,
  'T_PRIVATE_SET'    => 'private(set)'  , //TODO: Extra spaces?
  'T_PROTECTED'      => 'protected'     ,
  'T_PROTECTED_SET'  => 'protected(set)', //TODO: Extra spaces?
  'T_PUBLIC'         => 'public'        ,
  'T_PUBLIC_SET'     => 'public(set)'   , //TODO: Extra spaces?
  'T_READONLY'       => 'readonly'      ,
  'T_STATIC'         => 'static'        ,

// Functional operator:
  'T_ECHO'           => 'echo'          ,
  'T_EMPTY'          => 'empty'         ,
  'T_INCLUDE'        => 'include'       ,
  'T_INCLUDE_ONCE'   => 'include_once'  ,
  'T_PRINT'          => 'print'         ,
  'T_REQUIRE'        => 'require'       ,
  'T_REQUIRE_ONCE'   => 'require_once'  ,
  
  'T_RETURN'         => 'return'        ,
  'T_YIELD'          => 'yield'         ,
  'T_YIELD_FROM'     => 'yield from'    , //TODO: Spaces between yield and from
  
  'T_CLONE'          => 'clone'         ,
  'T_THROW'          => 'throw'         ,
  'T_NEW'            => 'new'           ,
// Functions:
  'T_EVAL'           => 'eval()'        ,
  'T_EXIT'           => 'exit or die'   ,
  'T_HALT_COMPILER'  => '__halt_compiler()', // Stop parsing -> T_INLINE_HTML:
  'T_ISSET'          => 'isset()'       ,
  'T_LIST'           => 'list()'        ,
  'T_UNSET'          => 'unset()'       ,
// Casts:
  'T_ARRAY_CAST'     => '(array)'       ,
  'T_BOOL_CAST'      => '(bool) or (boolean)',
  'T_DOUBLE_CAST'    => '(real), (double) or (float)',
  'T_INT_CAST'       => '(int) or (integer)',
  'T_OBJECT_CAST'    => '(object)'      ,
  'T_STRING_CAST'    => '(string)'      ,
  'T_UNSET_CAST'     => '(unset)'       ,
  'T_VOID_CAST'      => '(void)'        ,
// Operators:
 //Assign
  'T_AND_EQUAL'      => '&=' ,
  'T_COALESCE_EQUAL' => '??=',
  'T_CONCAT_EQUAL'   => '.=' ,
  'T_DIV_EQUAL'      => '/=' ,
  'T_MINUS_EQUAL'    => '-=' ,
  'T_MOD_EQUAL'      => '%=' ,
  'T_MUL_EQUAL'      => '*=' ,
  'T_OR_EQUAL'       => '|=' ,
  'T_PLUS_EQUAL'     => '+=' ,
  'T_POW_EQUAL'      => '**=',
  'T_SL_EQUAL'       => '<<=',
  'T_SR_EQUAL'       => '>>=',
  'T_XOR_EQUAL'      => '^=' ,
 //Binary:
  'T_INSTANCEOF'     => 'instanceof'    ,
  'T_LOGICAL_AND'    => 'and',
  'T_LOGICAL_OR'     => 'or' ,
  'T_LOGICAL_XOR'    => 'xor',
  'T_BOOLEAN_AND'    => '&&' ,
  'T_BOOLEAN_OR'     => '||' ,
  'T_COALESCE'       => '??' ,
  'T_PIPE'           => '|>' ,
  'T_POW'            => '**' ,
  'T_SL'             => '<<' ,
  'T_SR'             => '>>' ,
  'T_START_HEREDOC'  => '<<<', //String?
 //Compare:
  'T_IS_EQUAL'                 => '==' ,
  'T_IS_GREATER_OR_EQUAL'      => '>=' ,
  'T_IS_IDENTICAL'             => '===',
  'T_IS_NOT_EQUAL'             => '!= or <>',
  'T_IS_NOT_IDENTICAL'         => '!==',
  'T_IS_SMALLER_OR_EQUAL'      => '<=' ,
  'T_SPACESHIP'                => '<=>',
 //Unary:
  'T_DEC'                      => '--'  ,
  'T_INC'                      => '++'  ,
 //Others:
  'T_DOUBLE_ARROW'             => '=>'  ,
  'T_DOUBLE_COLON'             => '::'  ,
  'T_ELLIPSIS'                 => '...' ,
  'T_NS_SEPARATOR'             => '\\'  , //???
  'T_PAAMAYIM_NEKUDOTAYIM'     => '::'  ,
  'T_OBJECT_OPERATOR'          => '->'  ,
  'T_NULLSAFE_OBJECT_OPERATOR' => '?->' ,
// Types:
  'T_CALLABLE'                 => 'callable',
// Contants:
  'T_CLASS_C'                  => '__CLASS__'     ,
  'T_DIR'                      => '__DIR__'       ,
  'T_FILE'                     => '__FILE__'      ,
  'T_FUNC_C'                   => '__FUNCTION__'  ,
  'T_LINE'                     => '__LINE__'      ,
  'T_METHOD_C'                 => '__METHOD__'    ,
  'T_NS_C'                     => '__NAMESPACE__' ,
  'T_PROPERTY_C'               => '__PROPERTY__'  ,
  'T_TRAIT_C'                  => '__TRAIT__'     ,
// Strings
  'T_CONSTANT_ENCAPSED_STRING' => '"foo" or \'bar\'',
  'T_CURLY_OPEN'               => '{$',
  'T_DOLLAR_OPEN_CURLY_BRACES' => '${',
  'T_ENCAPSED_AND_WHITESPACE'  => '" $a"',
  'T_NUM_STRING'               => '"$a[0]"',
  'T_STRING_VARNAME'           => '"${a',
  'T_END_HEREDOC'              => '',
// Dynamic
  'T_STRING'         => 'parent, self, etc.',
  'T_WHITESPACE'     => '\t \r\n',
  'T_VARIABLE'       => '$foo',
  'T_DNUMBER'        => '0.12, etc.',
  'T_LNUMBER'        => '123, 012, 0x1ac, etc.',
  'T_COMMENT'        => '// or #, and /* */',
  'T_DOC_COMMENT'    => '/** */',
// Tags
  'T_CLOSE_TAG'            => '?> or %>',
  'T_OPEN_TAG'             => '<?php, <? or <%',
  'T_OPEN_TAG_WITH_ECHO'   => '<?= or <%=',
// Unsort
  'T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG' => '&',
  'T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG' => '&',
  'T_ARRAY'                    => 'array()',
  'T_ATTRIBUTE'                => '#[',
  'T_BAD_CHARACTER'            => '',
  'T_INLINE_HTML'              => '',
  'T_NAME_FULLY_QUALIFIED'     => '\App\Namespace',
  'T_NAME_QUALIFIED'           => 'App\Namespace',
  'T_NAME_RELATIVE'            => 'namespace\Namespace',
];

$CharTokens=[
  ';' =>'',
  '=' =>'',
];
$TokenNames=[];

$CheckOldTokens=Array_Flip(Array_Keys($TokenTypes));
$CheckNewTokens=[];

$TokenConsts=[];

ForEach(Get_Defined_Constants(true)['tokenizer'] As $k=>$v)
  If(Str_Starts_With($k, 'T_'))
  {
    If(IsSet($CheckOldTokens[$k]))
      UnSet($CheckOldTokens[$k]);
    Else
    {
      Log('Warning', 'Need to add a new token', $k, '=', $v);
      $CheckNewTokens[$k]=True;
    }
    
    $TokenNames[$v]='!'.$k;
    $TokenConsts[$k]=$v;
  }
  
ForEach($CheckOldTokens As $k=>$v)
  Log('Warning', 'Need to remove an aold token', $k);
