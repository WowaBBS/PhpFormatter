<?
namespace Reformat\Process;
use function Reformat\Log;

$dontWrite  ??=True;

Class TFile Extends TSource
{
  Function File($FileName, $root)
  {
    $ShortName=$FileName;
    If(Str_Starts_With($FileName, $root))
      $ShortName=SubStr($FileName, StrLen($root));
    $Info=[
      'RootDir'   =>$root,
      'FileName'  =>$FileName,
      'ShortName' =>$ShortName,
    ];
  
    Log('Progress', 'Processing: ', $ShortName);
    
    $source = @file_get_contents($FileName);
    
    if ($source === false) Return Log('Error', 'Cannot read file ', $FileName)->Ret(-2);
    
    $this->Filters->FileStart($Info);
    
    $Result = $this->Source($source, $Info);
    
    if($Result===-1) Return -1;
    if($Result===0) Return Log('Progress', 'Processing: ', $ShortName, '  unchanged')->Ret(0);
    if(!Is_String($Result)) Return Log('Error', 'Unknown resul code ', $Result)->Ret(-1);
    
    //«аписываем только после успешной проверки.
  
    Global $dontWrite;
    If(!$dontWrite)
    If(@file_put_contents($FileName, $Result) === false)
      Return Log('Error', 'Cannot write file')->Ret(-1);
  
    Log('Log', 'Processing: ', $ShortName, ' changed');
    Return 1;
  }
}