<?
NameSpace Reformat\Filter;

Use Reformat\Option\IReceiver As IOptionReceiver;
Use Function Reformat\Log;

Class TBase Implements IOptionReceiver
{
  Var $Source { Get=>$this->Source?->Get(); Set=>$value? \WeakReference::Create($value):Null; }
  Var Bool $InProcess=False;
  
  Static Function GetName() { Return 'Base'; }
  
  Function Init($Source)
  {
    $this->Source=$Source;
  }
  
  Static Function IsApplicable($Process) { return True; }

  Function FileStart() {}
  
  Function ProcessAll($Document):?Bool
  {
    If($this->InProcess)
      Return Log('Error', 'Filter has alredy started')->BackTrace()->Ret(False);
    $this->Source->Options->LoadDefault();
    $this->CodeStart();
    $First=$Document->First;
    $this->InProcess=True;
    $r=$this->ProcessNode($Document);
    If(!$this->InProcess)
      Return Log('Error', 'Filter has wrong status InProcess')->BackTrace()->Ret(False);
    $this->InProcess=False;
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
    $this->Source->Options->Apply($Token);
  }
  
  Function ProcessNode($Node)
  {
    ForEach($Node As $Token)
    {
      If($Token->GetId()==='Option')
        $this->ProcessOption($Token);
      Switch($TypeHandler=$Token->GetTypeHandler())
      {
      Case 'Text': $r=$this->ProcessText($Token); Break;
      Case 'Node': $r=$this->ProcessMode($Token); Break;
    //Case 'Opt' : $r=$this->ProcessOption($Token); Break;
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
  
  Function Option_Do($Op) { $Op->BaseCalled(); }
}