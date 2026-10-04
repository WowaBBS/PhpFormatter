<?
namespace Reformat\Filter;
use function Reformat\Log;

Class TList Extends TBase
{
  Var $List=[];
  
  Function Add($Filter)
  {
    $Filter->Init($this->Source);
    $Key=$Filter->GetName();
    If(IsSet($this->List[$Key]))
      Log('Error', 'Filter ', $Key, ' has already exists');
    $this->List[$Key]=$Filter;
  }
  
  Function FileStart()
  {
    ForEach($this->List As $Filter)
      $Filter->FileStart();
  }

  Function ProcessAll($Document):?Bool
  {
    $Changed=False;
    ForEach($this->List As $Filter)
    {
      $Result=$Filter->ProcessAll($Document);
      If($Result===False) Return False; //Error happend
      If($Result===True) $Changed=True;
    }
    Return $Changed? True:Null;
  }

  Function Option_Do($Op)
  {
    Parent::Option_Do($Op);
    ForEach($this->List As $FilterName=>$Filter)
      $Op->Sub($FilterName, $Filter);
  }
}
