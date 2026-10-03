<?
NameSpace Reformat\Process;

Use Reformat\Option\IReceiver As IOptionReceiver;
Use Function Reformat\Log;
Use Function Reformat\Filter\CreateList;

Abstract Class TBase Implements IOptionReceiver
{
  Var $Filters;
  Var $ShortPath ='Source';
  Var $FilePath  ='Source';
  Var $Options   =[];
  
  Function Init()
  {
    $this->Filters=CreateList($this); //TODO: $Config
  }
  
  //****************************************************************
  // Option
  
  Function Option_Validate($Vars, $Option)
  {
    $this->Options[]=$Option;
    $Op=New \Reformat\Option\TOperation($Vars, 'Check');
    $Op->Receive($this);
  }
  
  Function Option_Do($Op)
  {
    $Op->Sub('Filter', $this->Filters);
    $Op->BaseCalled();
  }
  
  Function Option_CheckUnused()
  {
    $Options=$this->Options; $this->Options=[];
    
    ForEach($Options As $Option)
      $Option->CheckUnUsed();
  }

  //****************************************************************
}