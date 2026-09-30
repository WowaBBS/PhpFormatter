<?
namespace Reformat\Process;
use function Reformat\Log;
use function Reformat\Filter\CreateList;

Abstract Class TBase
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
    $this->Filters->Option_Validate($Vars['Filter'], $Option);
  }
  
  Function Option_CheckUnused()
  {
    $Options=$this->Options; $this->Options=[];
    
    ForEach($Options As $Option)
      $Option->CheckUnUsed();
  }

  //****************************************************************
}