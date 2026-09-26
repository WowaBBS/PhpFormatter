<?
NameSpace Reformat\Utils\StrList;
Include_Once 'Str.php'; Use Function Reformat\Utils\Str\{LenFirstSpaces};

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
  $Min=LenFirstSpaces($l[0]);
  ForEach($l As $v)
    $Min=Min($Min, LenFirstSpaces($v));
  RemoveFirstLen($l, $Min);
  Return $Min;
}
