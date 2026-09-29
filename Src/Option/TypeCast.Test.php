<?
namespace Reformat\Option;

Use Function Reformat\Log;

Include_Once '../_All.php';
Include_Once 'CastValues.php';

$Values=Cast_Values();
ForEach(EType::Cases() As $Type)
{
  If($Type->IsEmpty()) Continue;
  Log('Log', $Type, ':');
  ForEach($Values As $Value)
    Log('Log', '  ', [$Value], '=>', $Type->Cast($Value));
  Log('Log', '-----------------');
}

$Loader->Done();
