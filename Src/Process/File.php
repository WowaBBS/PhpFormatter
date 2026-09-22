<?
namespace Reformat\Process;
use function Reformat\Log;

$dontWrite  ??=True;

Abstract Class TFile Extends TSource
{
  Function File($FilePath, $root)
  {
    $ShortPath=$FilePath;
    If(Str_Starts_With($FilePath, $root))
      $ShortPath=SubStr($FilePath, StrLen($root));
    
    $OldShortPath =$this->ShortPath ;
    $OldFilePath  =$this->FilePath  ;
    
    $this->ShortPath =$ShortPath;
    $this->FilePath  =$FilePath;
    
    $Result=$this->_File($FilePath);
   
    $this->ShortPath =$OldShortPath ;
    $this->FilePath  =$OldFilePath  ;
    
    Return $Result;
  }
  
  Function _File($FilePath)
  {
    Log('Progress', 'Processing: ', $this->ShortPath);
    
    $source = @file_get_contents($FilePath);
    
    if ($source === false) Return Log('Error', 'Cannot read file ', $FilePath)->Ret(-2);
    
    $this->Filters->FileStart();
    
    $Result = $this->Source($source);
    
    if($Result===-1) Return Log('Progress', 'Processing: ', $this->ShortPath, ' error')->Ret(-1);
    if($Result===0) Return Log('Progress', 'Processing: ', $this->ShortPath, ' unchanged')->Ret(0);
    if(!Is_String($Result)) Return Log('Error', 'File: Unknown result code ', $Result)->Ret(-1);
    
    //«аписываем только после успешной проверки.
  
    Global $dontWrite;
    If(!$dontWrite)
    If(@file_put_contents($FilePath, $Result) === false)
      Return Log('Error', 'Cannot write file')->Ret(-1);
  
    Log('Log', 'Processing: ', $this->ShortPath, ' changed');
    Return 1;
  }
}