<?
namespace Reformat\Process;
use function Reformat\Filter\CreateList;

Abstract Class TBase
{
  Var $Filters;
  Var $ShortPath ='Source';
  Var $FilePath  ='Source';
  
  Function Init()
  {
    $this->Filters=CreateList($this); //TODO: $Config
  }
}