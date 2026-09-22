<?
namespace Reformat\Filter;
use function Reformat\Log;

class TBase
{
  Var $Source;
  
  Function GetSource() { Return $this->Source->Get(); }
  
  Static Function GetName() { Return 'Base'; }
  
  Function Init($Source)
  {
    $this->Source=\WeakReference::Create($Source);
  }
  
  Static Function IsApplicable($Process) { return True; }

  Function FileStart() {}
  
  Function ProcessAll($Document):?Bool
  {
    $this->CodeStart();
    $First=$Document->First;
    For($Token=$Document->First; $Token; $Token=$Token->Next)
    {
      $r=$this->ProcessText($Token);
      If(Is_String($r))
      {
        $Token->SetText($r);
        Continue;
      }
      If($r===False) Continue; //TODO: Remove?
      If($r===Null ) Continue; //Not changed
      If(Is_Object($r)) { $Token=$r; Continue; }
      Log('Error', 'Unknown token process:')->Debug($r);
      Return False;
    }
    
    $this->CodeFinish();
    
    $Changed=$Document->IsChanged();
    
    If(!$Changed) Return Null;
  //Log('Debug', 'Chenged!');
    $Document->ResetChanged();
      
  //TODO: $this->ReIndexLinePos($Res, $First);
    
    Return True;
  }
  
  Function ReIndexLinePos($Tokens, $First)
  { //TODO: Add Unicode support?
    If(!$Tokens) Return;
    $Line     = $First->Line ;
    $FirstPos = $First->Pos  ;
    $Pos      = $FirstPos    ;
    ForEach($Tokens As $Token)
    {
      $Token->Line =$Line ;
      $Token->Pos  =$Pos  ;
      $Text=$Token->Text;
      
      $i=StrRPos($Text, "\n");
      If($i!==False)
      {
        $Line+=SubStr_Count($Text, "\n");
        $Pos=$FirstPos-$i-1;
      }
      $Pos+=StrLen($Text);
    }
  }
  
  Function CodeStart()
  {
  }
  
  Function CodeFinish()
  {
  }
  
  Function ProcessText($Token)//:Void|String //:Null|Object|Array|String
  {
  }
}