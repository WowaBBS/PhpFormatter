<?
namespace Reformat;
include 'All.php';

$checkInPhp  =False ;
$dontWrite   =true  ;
$debugTokens =True  ;

$Process=New TProcess();
$Process->Init();

if($Process->File(__DIR__.'\Sample.php', __DIR__)<0)
  exit(-1);
