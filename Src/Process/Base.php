<?
NameSpace Reformat\Process;

Use Reformat\Option\IReceiver  As IOptionReceiver  ;
Use Reformat\Option\TManager   As TOptionManager   ;
Use Reformat\Debug\TController As TDebugController ;
Use Function Reformat\Log;
Use Function Reformat\Filter\CreateList;

Abstract Class TBase Implements IOptionReceiver
{
  Var $Filter    ;
  Var $ShortPath ='Source';
  Var $FilePath  ='Source';
  Var $Options   ;
  Var $Debug     ;
  
  Function Init()
  {
    $this->Filter  =CreateList($this); //TODO: $Config
    $this->Debug   =New TDebugController($this);
    $this->Options =New TOptionManager($this);
    $this->Options->SaveDefault();
  }
  
  //****************************************************************
  // Option
  
  Function Option_Do($Op)
  {
    $Op->Sub('Filter' ,$this->Filter );
    $Op->Sub('Debug'  ,$this->Debug  );
    $Op->BaseCalled();
  }
  
  //****************************************************************
}