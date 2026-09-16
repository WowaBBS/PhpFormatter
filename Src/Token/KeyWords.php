<?
namespace Reformat;

/*
 * Ключевые слова PHP, которые token_get_all() представляет
 * отдельными токенами.
 *
 * Список рассчитан на современные версии PHP 8.x.
 */
$keywordTokens = [
  'abstract'     ,
  'array'        ,
  'as'           ,
  'break'        ,
  'callable'     ,
  'case'         ,
  'catch'        ,
  'class'        ,
  'clone'        ,
  'const'        ,
  'continue'     ,
  'declare'      ,
  'default'      ,
  'do'           ,
  'echo'         ,
  'else'         ,
  'elseIf'       ,
  'empty'        ,
  'endDeclare'   ,
  'endFor'       ,
  'endForEach'   ,
  'endIf'        ,
  'endSwitch'    ,
  'endWhile'     ,
  'eval'         ,
  'exit'         ,
  'extends'      ,
  'final'        ,
  'finally'      ,
  'for'          ,
  'foreach'      ,
  'function'     ,
  'global'       ,
  'goto'         ,
  'if'           ,
  'implements'   ,
  'include'      ,
  'include_once' ,
  'instanceOf'   ,
  'insteadOf'    ,
  'interface'    ,
  'isset'        ,
  'list'         ,
  'nameSpace'    ,
  'new'          ,
  'print'        ,
  'private'      ,
  'protected'    ,
  'public'       ,
  'require'      ,
  'require_once' ,
  'return'       ,
  'static'       ,
  'switch'       ,
  'throw'        ,
  'trait'        ,
  'try'          ,
  'unSet'        ,
  'use'          ,
  'var'          ,
  'while'        ,
  'yield'        ,
  'yield_from'   ,
  
  // PHP 7+
  'finally'      ,

  // PHP 8+
  'match'        ,
  'enum'         ,
  'readOnly'     ,

  // PHP 8.1+
  'attribute'    ,
];
  
/*
 * Некоторые ключевые слова/зарезервированные значения PHP
 * token_get_all() возвращает как T_STRING.
 *
 * Например:
 *
 *   TRUE  -> T_STRING
 *   FALSE -> T_STRING
 *   NULL  -> T_STRING
 *
 * Поэтому их обрабатываем отдельно.
 */
$stringKeywords = [
  'true'     ,
  'false'    ,
  'null'     ,

  // Типы, являющиеся специальными типами PHP.
  'int'      ,
  'float'    ,
  'string'   ,
  'bool'     ,
  'array'    ,
  'object'   ,
  'callable' ,
  'iterable' ,
  'void'     ,
  'mixed'    ,
  'never'    ,

  // Специальные имена типов.
  'self'     ,
  'parent'   ,
];

/*
 * Некоторые T_* могут отсутствовать в старых версиях PHP.
 * Поэтому добавляем только реально существующие константы.
 */
$Res=[];
ForEach($keywordTokens As $Token)
{
  $Const='T_'.StrToUpper($Token);
  If($v=$TokenConsts[$Const]?? Null)
    $Res[$v]=True;
  Else
    $stringKeywords[]=$Token;
}
$keywordTokens = $Res; //array_values(array_unique($keywordTokens));

$Res=[];
ForEach($stringKeywords As $Token)
  $Res[StrToLower($Token)]=$Token;
$stringKeywords = $Res;
