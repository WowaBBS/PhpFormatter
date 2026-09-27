<?
NameSpace Reformat\Option;

Use Function Reformat\Log;
Use Function Reformat\Utils\Str\LinePos;

Trait TraitLog
{
  Var       $LastToken ;
  Var Bool  $HasToken  ;
  Var Array $FilePos   ; //TODO: Remove
//Var Bool  $HasError =False;

  Function Log_SetToken($Token)
  {
    $this->HasToken=(Bool)$Token;
    If($Token)
      $this->LastToken=$Token;
    Return $Token;
  }
  
  Function Warning (...$Args) { Return $this->Log('Warning' ,...$Args); }
  Function Error   (...$Args) { Return $this->Log('Error'   ,...$Args); }
  Function Debug   (...$Args) { Return $this->Log('Debug'   ,...$Args); }
  Function Log(String $LogLevel, ...$Args)
  {
  //If($LogLevel==='Error') $this->HasError=True;
    $FilePos=$this->FilePos?: [];
  
    $Line =$this->FilePos[1]?? 1;
    $Pos  =$this->FilePos[$Line<=1? 3:2]?? $this->FilePos[2]?? 1;
    If($Token=$this->LastToken)
    {
      $Line +=$Token->Line-1;
      $Pos  +=$Token->Pos   ;
      If(!$this->HasToken)
        LinePos($Token->Text, $Line, $Pos);
    }
    Return Log($LogLevel, ...$Args)->File(
      $this->FilePos[0]?? 'Source', 
      $Line, 
      $Pos
    );
  }
}
