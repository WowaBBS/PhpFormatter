<?
namespace Reformat\Token;

Use Function Reformat\Log;
Use Reformat\Option\TParser As TOptionParser;

Class TOption Extends TText
{
  Var Mixed $Vars  =Null ;
  Var       $Token ;//{ Get=>$this->Token?->Get(); Set=>$value?->ToWeak(); }
  Var       $Valid =False;
  
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
    $Parser=New TOptionParser($Text, $FilePos);
    $this->Vars=$Parser->Parse();
    Return True;
  }
  
  Function Validate($Source)
  {
    $Source->Option_Validate($this->Vars, $this);
  }
  
  Function CheckUnUsed()
  {
    $this->Vars->CheckUnUsed();
  }
  
//****************************************************************
// Debug

  Function GetDebug() { Return ['Option ', $this->Token]; }
  
//****************************************************************
}
