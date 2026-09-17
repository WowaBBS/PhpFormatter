<?
namespace Reformat;

for($F=__FILE__; $F;) if(@include($F=DirName($F)).'/Using.php') break;

//$Loader->GetLogger()->Add($argv[0].'.log'); 
$info=PathInfo($argv[0]); //$_SERVER['SCRIPT_FILENAME'] instead
$logFile??=$info['dirname'].'/'.$info['filename'].'.log';
$Loader->GetLogger()->Add($logFile);
$log=$Loader->GetLogger()->Log(...);

Function Log(...$Args)
{
  Global $log;
  Return $log(...$Args);
}
