<?
NameSpace Reformat\Process;

Use Reformat\Option\IReceiver As IOptionReceiver ;
Use Reformat\Option\TManager  As TOptionManager  ;
Use Function Reformat\Log;
Use Function Reformat\Filter\CreateList;

Abstract Class TBase Implements IOptionReceiver
{
  Var $Filter;
  Var $ShortPath ='Source';
  Var $FilePath  ='Source';
  Var $Options   ;
  
  Function Init()
  {
    $this->Filter=CreateList($this); //TODO: $Config
    $this->Options=New TOptionManager($this);
    $this->Options->SaveDefault();
  }
  
  //****************************************************************
  // Option
  
  Function Option_Do($Op)
  {
    $Op->Sub('Filter', $this->Filter);
    $Op->BaseCalled();
  }
  
  //****************************************************************
}