<?
namespace Reformat\Process;
use function Reformat\Log;

Abstract Class TFolder Extends TFile
{
  Function Folder($Root)
  {
    If(!Is_Dir($Root)) Return Log('Error', 'Directory does not exist: ', $Root)->Ret(-1);
    
    $FilesProcessed = 0;
    $FilesChanged   = 0;
    $Errors         = 0;
    
    $Iterator = New \RecursiveIteratorIterator(
      New \RecursiveDirectoryIterator(
        $Root,
        \FilesystemIterator::SKIP_DOTS
      ),
      \RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    ForEach ($Iterator As $File)
    {
      if (!$File->isFile()) Continue;
      $Ext=StrToLower($File->GetExtension());
    //If ($Ext !== 'php') Continue;
      If (!Str_Starts_With($Ext, 'php')) Continue;
    
      $FileName = $File->GetPathName();
    
      $Result=$this->File($FileName, $Root);
      switch($Result)
      {
      case -2: ++$Errors; break;
      case -1: ++$Errors; ++$FilesProcessed; break;
      case  0: ++$FilesProcessed; break;
      case  1: ++$FilesProcessed; ++$FilesChanged; break;
      default: Log('Error', 'Folder: Unknown result code ', $Result); break;
      }
    }
    
    echo "\n";
    echo "Files processed: {$FilesProcessed}\n";
    echo "Files changed:   {$FilesChanged}\n";
    echo "Errors:          {$Errors}\n";
  }
}