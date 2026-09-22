<?
namespace Reformat;

for($F=__FILE__; $F;) if(@include($F=DirName($F)).'/Using.php') break;

$Loader->GetLogger()->ScriptLog($LogFile); 
$log=$Loader->GetLogger()->Log(...);

Function Log(...$Args)
{
  Global $log;
  Return $log(...$Args);
}
