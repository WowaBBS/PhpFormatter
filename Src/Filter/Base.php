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
    $r=$this->ProcessNode($Document);
    If($r===False) Return False;
    If($r!==Null) Return Log('Error', 'Unknown token process:')->Debug($r)->Ret(False);
    
    $this->CodeFinish();
    
    $Changed=$Document->IsChanged();
    
    If(!$Changed) Return Null;
    
    $Document->ResetChanged();
      
    Return True;
  }
  
  Function ProcessOption($Token)
  {
  }
  
  Function ProcessNode($Node)
  {
    ForEach($Node As $Token)
    {
      Switch($TypeHandler=$Token->GetTypeHandler())
      {
      Case 'Text': $r=$this->ProcessText($Token); Break;
      Case 'Node': $r=$this->ProcessMode($Token); Break;
      Case 'Opt' : $r=$this->ProcessOption($Token); Break;
      Default: Return Log('Fatal', 'Unknown TypeHandler=',$TypeHandler)->Ret(False);
      }
        
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
  }
  
  Function ProcessText($Token)//:Void|String //:Null|Object|Array|String
  {
  }
  
  Function CodeStart()
  {
  }
  
  Function CodeFinish()
  {
  }
  
  Function Option_Validate($Vars, $Option)
  {
  }
}