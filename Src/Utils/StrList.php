<?
NameSpace Reformat\Utils\StrList;
Use Function Reformat\Utils\Str\LenFirstSpaces;

Function RemoveFirstLen(Array &$l, $Len)
{
  ForEach($l As &$v)
    $v=SubStr($v, $Len);
}

Function IsAllStartsWith(Array $l, $Needle)
{
  ForEach($l As $v)
    If(!Str_Starts_With($v, $Needle))
      Return False;
  Return True;
}

Function HasStartsWith(Array $l, $Needle)
{
  ForEach($l As $v)
    If(Str_Starts_With($v, $Needle))
      Return True;
  Return True;
}

Function LTrimMinSpaces(Array &$l, $Def=0)
{
  If(!$l) Return $Def;
  $Min=-1;
  ForEach($l As $v)
  {
    $Len=LenFirstSpaces($v);
    If($Len===StrLen($v)) Continue; //Empty string
    $Min=$Min<0? $Len:Min($Min, $Len);
  }
  If($Min<0) Return $Def;
  RemoveFirstLen($l, $Min);
  Return $Min;
}
