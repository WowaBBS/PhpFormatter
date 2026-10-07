<?
NameSpace Reformat\Option;

Use Function Reformat\Log;

Class TManager
{
  Function __Construct(IReceiver $Object) { $this->Object=$Object; }
  
//****************************************************************
// 
  Var Array $Options=[];
  
  Function Register(IProvider $Option)
  {
    $this->Options[]=$Option;
    $this->Process($Option->GetVars(), 'Check');
  }
  
  Function CheckUnused()
  {
    $Options=$this->Options; $this->Options=[];
    
    ForEach($Options As $Option)
      $Option->CheckUnUsed();
  }
  
  Function Apply(IProvider $Option)
  {
    $this->Process($Option->GetVars(), 'Set');
  }

//****************************************************************
// Receiver operations
  
  Var $Object { Get=>$this->Object?->Get(); Set=>$value? \WeakReference::Create($value):Null; }
  Var TValue $Default;
  
  Function Process($Value, $OpType='Get', Array $For=['Unknown'])
  {
    $Object=$this->Object;
    If(!$Object) Return Log('Error', 'OptionManager: There is no object')->BackTrace()->Ret();
    If(!$Value) Return Log('Error', 'OptionManager: There is no value')->BackTrace()->Ret();
    $Op=New TOperation($Value, $OpType, $For);
    $Op->Receive($Object);
  }
  
  Function LoadDefault(Array $For=['UnKnown'])
  {
    $this->Process($this->Default, 'Set', [...$For, '.LoadDefault']);
  }
  
  Function SaveDefault(Array $For=['UnKnown'])
  {
    $this->Default=$this->ReadCurrent([...$For,'.SaveDeafult']);
    Log('Debug', 'Default: ', $this->Default); //->Debug($this->Default);
  }
  
  Function ReadCurrent(Array $For=['UnKnown'])
  {
    $Res=$this->NewValue();
    $this->Process($Res, 'Get', $For);
    Return $Res;
  }
  
//****************************************************************

  Function NewValue()
  {
    $Res=New TValue();
    $Res->Owner=$this;
    Return $Res;
  }
  
  Function GetLogger() { Return Null; }
}