<?
NameSpace Reformat\Option;

Use Function Reformat\Log;
Use Function Reformat\Utils\Str\LinePos;

Trait TraitLog
{
  Var       $FilePos   ; //Token|Array
//Var Bool  $HasError =False;

  Function Log_FilePos($FilePos)
  {
    If($FilePos===Null && Is_Object($this->FilePos)) // End of token
    {
      $Token=$this->FilePos;
      $FilePos=$Token->GetFilePos();
      LinePos($Token->Text, $FilePos[1], $FilePos[2]);
    }
    If(Is_Array($FilePos)) { $this->FilePos=$FilePos; Return; }
    If(!Is_Object($FilePos)) Return Log('Error', 'Wrong FilePos, ', $FilePos)->Ret();
    $this->FilePos=$FilePos;
  }
  
  Function GetFilePos()
  {
    $Res=$this->FilePos;
    If(Is_Object($Res))
      $Res=$Res->GetFilePos();
    Return $Res;
  }
  
  Function Warning (...$Args) { Return $this->Log('Warning' ,...$Args); }
  Function Error   (...$Args) { Return $this->Log('Error'   ,...$Args); }
  Function Debug   (...$Args) { Return $this->Log('Debug'   ,...$Args); }
  Function Log(String $LogLevel, ...$Args)
  {
  //If($LogLevel==='Error') $this->HasError=True;
    Return Log($LogLevel, ...$Args)->File(...$this->GetFilePos());
  }
}
