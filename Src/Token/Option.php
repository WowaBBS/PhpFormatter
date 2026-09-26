<?
namespace Reformat\Token;

Use Function Reformat\Log;
Use Reformat\Utils\TOption As TOptionParser;

Class TOption Extends TText
{
  Var Mixed $Vars  =Null ;
  Var       $Token ;//{ Get=>$this->Token?->Get(); Set=>$value?->ToWeak(); }
  Var       $Valid =False;
  Var       $Used  =False;
  
  Function __Construct($Text, $Token, $Source)
  {
    Parent::__Construct($Token->Id, $Token->Text, $Token->Line, $Token->Pos);
    $this->Token=$Token;
    If($this->Parse($Text, $Token))
      $this->Validate($Source);
  //Log('Debug', 'Option')->Debug($this);
  }

  Function GetId() { Return 'Option'; }

  Function Parse($Text, $Token)
  {
    $FilePos=$Token->GetFilePos();
    $Parser=New TOptionParser($FilePos);
    $this->Vars=$Parser->Parse($Text);
    
    Return True;
  }
  
  Function Validate($Source)
  {
    $Valid=$Source->Option_Validate($this->Vars, $this);
    If($Valid===True)
      $this->Valid=True;
    Else
      Log('Error', 'Option ', $Valid,': ', $Res);
  }
  
//****************************************************************
// Debug

  Function GetDebug() { Return ['Option ', $this->Token]; }
  
//****************************************************************
}
