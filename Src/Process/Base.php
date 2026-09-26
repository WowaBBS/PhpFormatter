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
  
  Function Option_Validate($Vars, $Option):True|String
  {
    $this->Options[]=$Option;
    $Res=False;
    If($FilterVars=$Vars['Filter']?? Null)
      Return $this->Filters->Option_Validate($FilterVars, $Option);
    Return 'Unknown Option';
  }
  
  Function Option_CheckUnused()
  {
    $Res=[];
    ForEach($this->Options As $Option)
      If($Option->Valid && !$Option->Used)
        $Res[]=$Option;
    If(!$Res) Return;
    $Log=Log('Error', 'Unused Option:');
    ForEach($Res As $Option)
      $Log('  ', $Option);
    $this->Options=[];
  }

  //****************************************************************
}