<?
NameSpace Reformat\Utils\Str;

Function LenFirstSpaces(String $Str, String $Space=' ')
{
  Return StrLen($Str)-StrLen(LTrim($Str, $Space));
}

Function LenLastSpaces(String $Str, String $Space=' ')
{
  Return StrLen($Str)-StrLen(RTrim($Str, $Space));
}

Function Starts_With_List($Str, $List, $Default='')
{
  ForEach($List As $k=>$Needle)
    If(Str_Starts_With($Str, $Needle))
      Return $Needle;
  Return $Default;
}

Function Starts_With_List_Key($Str, $List, $Default=0)
{
  ForEach($List As $k=>$Needle)
    If(Str_Starts_With($Str, $Needle))
      Return $k;
  Return $Default;
}

Function IsWord($Str)
{
  Return Preg_Match('/^[a-zA-Z_][a-zA-Z0-9_]*$/Ss', $Str);
}

Function LinePos($Text, &$Line, &$Pos)
{
  //TODO: Add Unicode support?
  $i=StrRPos($Text, "\n");
  If($i!==False)
  {
    $Line+=SubStr_Count($Text, "\n");
    $LastLineSize=StrLen($Text)-$i-1;
    $Pos=$LastLineSize;
    Return True;
  }

  $Pos+=StrLen($Text);
  Return False;
}
