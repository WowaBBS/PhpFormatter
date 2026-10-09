<?
NameSpace Reformat\Filter;

Use Reformat\Option\IReceiver As IOptionReceiver;
Use Function Reformat\Log;

Class TBase Implements IOptionReceiver
{
  Use \Reformat\Linked\TNode;
  
  Var $Source { Get=>$this->Source?->Get(); Set=>$value? \WeakReference::Create($value):Null; }
  Var Bool $InProcess =False;
  Var Bool $Enable    =True;
  
  Static Function GetName() { Return 'Base'; }
  
  Function Init($Source)
  {
    $this->Source=$Source;
  }
  
  Function Dispose()
  {
    $this->Remove();
  }
  
  Static Function IsApplicable($Process) { return True; }

  Function FileStart() {}
  
  Function ProcessAll($Document):?Bool
  {
    If($this->InProcess)
      Return Log('Error', 'Filter has alredy started')->BackTrace()->Ret(False);
    $this->Source->Options->LoadDefault(['Filter.',$this->GetName()]);
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
  //Log('Debug', 'Filter.',$this->GetName(),'.', 'ProcessOption=', $Token->Text);
    $this->Source->Options->Apply($Token, ['Filter.',$this->GetName(),'.Option']);
  }
  
  Function ProcessNode($Node)
  {
    ForEach($Node As $Token)
    {
      If($Token->GetId()==='Option')
        $this->ProcessOption($Token);
      Switch($TypeHandler=$Token->GetTypeHandler())
      {
      Case 'Text': $this->DoProcessText($Token); Break;
      Case 'Node': $this->ProcessMode($Token); Break;
    //Case 'Opt' : $this->ProcessOption($Token); Break;
      Default: Return Log('Fatal', 'Unknown TypeHandler=',$TypeHandler)->Ret(False);
      }
      
      //TODO: Catch error
    }
  //If(!$Node->Debug_IntegrityTest())
  //  Log('Error', 'IntegrityTest: Filter.',$this->GetName()); //TODO: Return False
  }
  
  Function DoProcessText($Token):Void
  {
    If($this->Enable)
      $this->ProcessText($Token);
  }
  
  Function ProcessText($Token):Void
  {
  }
  
  Function CodeStart()
  {
  }
  
  Function CodeFinish()
  {
  }
  
  Function Option_Do($Op)
  {
    $this->Enable=$Op->GetSet('Enable', $this->Enable);
    $Op->BaseCalled();
  }
}