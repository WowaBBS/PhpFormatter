<?
namespace Reformat\Token;

Use Function Reformat\Log;
Use Reformat\Option\TParser   As TOptionParser   ;
Use Reformat\Option\IProvider As IOptionProvider ;
Use Reformat\Option\TValue    As TOptionValue    ;

Class TOption Extends TText Implements IOptionProvider
{
  Var Mixed $Vars  =Null ;
  Var       $Token ;//{ Get=>$this->Token?->Get(); Set=>$value?->ToWeak(); }
  Var       $Valid =False;
  
  Function __Construct($Text, $Token, $Source)
  {
    Parent::__Construct($Token->Id, $Token->Text, $Token->Line, $Token->Pos);
    $this->Token=$Token;
    If($this->Parse($Text, $Token))
      $Source->Options->Register($this);
  //Log('Debug', 'Option')->Debug($this);
  }

  Function GetId() { Return 'Option'; }
  
  Var $Parser=Null;

  Function Parse($Text, $Token)
  {
    $FilePos=$Token->GetFilePos();
    $Parser=New TOptionParser($Text, $FilePos);
    $this->Vars=$Parser->Parse();
    $this->Parser=$Parser; // For saving document
    Return True;
  }
  
  Function GetVars():TOptionValue { Return $this->Vars; }
  Function CheckUnUsed() { $this->Vars->CheckUnUsed(); }
  
//****************************************************************
// Debug

  Function GetDebug() { Return ['Option ', $this->Token]; }
  
//****************************************************************
}
