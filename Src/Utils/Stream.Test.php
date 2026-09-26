<?
 NameSpace Reformat\Utils\Stream;
 
 Include_Once '../All.php';
 Include_Once 'Stream.php';
 
 Use Function Reformat\Log;

 
 $Data=['Skip', 'Str1', 1, 'Str2', 'Str3'];
 $Res=$Data
   |> Skip(1)
   |> Filter(Is_String(...))
   |> Map(StrToLower(...))
   |> MapKey(fn($v)=>SubStr($v,3))
   |> MapPair(Function(&$k,$v){ $t=$k; $k=$v; Return $t; }) //Swap key and value
   |> ToArray(...)
   |> ForEachVal(fn($v)=>Log('Log', $v))
 ;

 $Res=$Data
   |> Skip(1)
   |> Filter(Is_String(...));
 
 Echo 'v1=', Next($Res), "\n";
 Echo 'v2=', Next($Res), "\n";

 $Desired=['str1'=>1, 'str2'=>2, 'str3'=>3];
 
//Print_R($Res);
