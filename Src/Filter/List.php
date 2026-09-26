<?
namespace Reformat\Filter;
use function Reformat\Log;

Class TList Extends TBase
{
  Var $List=[];
  
  Function Add($Filter)
  {
    $Filter->Init($this->GetSource());
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

  Function Option_Validate($Vars, $Option):True|String
  {
    ForEach($Vars As $FilterName=>$FilterVars)
      If($Filter=$this->List[$FilterName]?? Null)
      {
        $R=$Filter->Option_Validate($FilterVars, $Option);
        If(Is_String($R))
          Return $R;
      }
      Else
        Return 'Unknown filter '.$FilterName;
    Return True;
  }
}
