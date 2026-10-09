<?
namespace Reformat;
include '_All.php';

$checkInPhp  =False ;
$dontWrite   =true  ;
$debugTokens =True  ;

$Process=New TProcess();
$Process->Init();

$SourceFile=__DIR__.'\Sample.php';
//$SourceFile=__DIR__.'\Test.php';
$Res=$Process->File($SourceFile, __DIR__);
if($Res<0)
  exit(-1);
  
$Loader->Dispose();