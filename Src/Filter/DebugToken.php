<?
namespace Reformat\Filter;
use function Reformat\Log;

$debugTokens??=False;

Class TDebugToken Extends TBase
{
  Static Function GetName() { Return 'DebugToken'; }
  
  Static Function IsApplicable($Process)
  {
    Global $debugTokens;
    Return $debugTokens;
  }
  
  Function Process($Token)
  {
    Log('Debug', 'Token: ', ...$Token->GetDebug());
  }
}
