<?
NameSpace Reformat\Option;

Use Function Reformat\Log;

Use Reformat\FilePos\TInfo     As TFilePos;
Use Reformat\FilePos\IProvider As IFilePos;

Trait TraitLog
{
  Var IFilePos $FilePos;

  Function Log_FilePos(?IFilePos $FilePos)
  {
    If($FilePos===Null && $this->FilePos) // End of token
    {
      $Token=$this->FilePos;
      $FilePos=$Token->GetFilePosEnd();
    }
    $this->FilePos=$FilePos;
  }
  
  Function GetFilePos() { Return $this->FilePos?->GetFilePos()?? TFilePos::GetEmpty(); }
  
  Function Warning (...$Args) { Return $this->Log('Warning' ,...$Args); }
  Function Error   (...$Args) { Return $this->Log('Error'   ,...$Args); }
  Function Debug   (...$Args) { Return $this->Log('Debug'   ,...$Args); }
  Function Log(String $LogLevel, ...$Args)
  {
    Return Log($LogLevel, ...$Args)->File(...$this->GetFilePos()->ToArgs());
  }
}
