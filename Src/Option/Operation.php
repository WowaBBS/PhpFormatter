<?
NameSpace Reformat\Option;

Use Function Reformat\Log;

Class TOperation
{
  Var $Vars;
  Var $Op='None'; //'Get', 'Set', 'Check'
  
  Function __Construct($Vars, $Op=Null)
  {
    $this->Vars =$Vars;
    $this->Op   =$Op?? $this->Op;
  }

  Function GetSet($Key, $Value, Null|String|Callable $Type=Null, $Comment='')
  {
    $Vars=$this->Vars[$Key];
    $Valid=$Type && $Vars->Type->HasValue() && $Vars->CheckType($Type, $this->Op==='Check');
    
    Switch($this->Op)
    {
    Case 'None'  : Break;
    Case 'Get'   : Return $Valid? $Vars->GetValue(): $Value;
    Case 'Set'   : $Vars->SetValue($Value); Break;
    Case 'Check' : Break;
    }
    Return $Value;
  }
  
  Function State_Save()
  {
    Return [
      $this->Vars       ,
      $this->BaseCalled ,
    ];
  }
  
  Function State_Restore($v)
  {
    [
      $this->Vars       ,
      $this->BaseCalled ,
    ]=$v;
  }
  
  Function Sub($Key, $Value, $Comment='')
  {
    $Save=$this->State_Save();
    $Vars=$this->Vars[$Key];
    $this->Vars=$Vars;
    If(Is_Array($Value))
    {
      ForEach($Value As $k=>$v)
        $this->Sub($k, $v);
    }
    ElseIf(Is_Object($Value) && $Value InstanceOf IReceiver)
    {
      $this->Receive($Value);
    }
    Else
      Log('Error', 'OptionReceiver: Unknown option receiver:')->Debug($Value);
    
    $this->State_Restore($Save);
  }
  
  Function Receive(IReceiver $Object)
  {
    Try
    {
      $this->BaseCall_Begin();
      $Object->Option_Do($this);
      $this->BaseCall_Check($Object);
    }
    Catch(\Throwable $e)
    {
      Log('Error', 'Option_Do: have an exception at ', $this->Vars->GetPath())->Exception($e);
    }
  }
  
  Var $BaseCalled=False;
  
  Function BaseCall_Begin() { $this->BaseCalled=False; }
  Function BaseCall_Check($Object)
  {
    If($this->BaseCalled) Return;
    Log('Error', 'Option_Do: Hasn\'t called in ', Get_Class($Object));
  }
  
  Function BaseCalled()
  {
    If(!$this->BaseCalled)
      $this->BaseCalled=True;
    Else
      Log('Error', 'Option_Do: Base method has already called')->BackTrace();
  }
}
 