<?
namespace Reformat\Filter;
use function Reformat\Log;

$debugTokens??=False;

class TDebugToken Extends TBase
{
  Static Function GetName() { Return 'DebugToken'; }
  
  Static Function IsApplicable($Info, $Config)
  {
    Global $debugTokens;
    Return $debugTokens;
  }
  
  Function Process($Token)
  {
    Log('Debug', 'Token: ', ...$Token->GetDebug());
  }
  
}
