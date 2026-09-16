<?
namespace Reformat;
include 'All.php';

$checkInPhp  =False ;
$dontWrite   =true  ;
$debugTokens =True  ;

if(ProcessFile(__DIR__.'\Sample.php', __DIR__)<0)
  exit(-1);

$a=['wonderful', 'fullfill',['helpful','pure']];
echo "Hello $a[0] world!\n";
echo "Hello $a[1][20] world!\n";
echo "Hello {$a[2][0]} world!\n";
echo "Hello {$a[2][0+1]} world!\n";
//echo "Hello ${a[2][20]} world!\n";
//sleep(1);