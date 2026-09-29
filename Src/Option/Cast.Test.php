<?
NameSpace Reformat\Option;

Use Function Reformat\Log;

Include_Once '../_All.php';
Include_Once 'CastValues.php';

$Values=Cast_Values();
ForEach(Cast_GetList() As $Type=>$Cast)
{
  Log('Log', $Type, ':');
  ForEach($Values As $Value)
    Log('Log', '  ', [$Value], '=>', $Cast($Value));
  Log('Log', '-----------------');
}

$Loader->Done();
