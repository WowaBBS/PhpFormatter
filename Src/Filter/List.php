<?
namespace Reformat\Filter;
use function Reformat\Log;

Class TList Extends TBase Implements \IteratorAggregate
{
  Use \Reformat\Linked\TList{
    Add As Private List_Add;
  }
  Var $Map=[];
  
  Function Has($Name) { Return IsSet($this->Map[$Name]); }
  Function Get($Name) { Return $this->Map[$Name]?? Log('Error', 'Filter ', $Name, ' not found')->BackTrace()->Ret(); }
  
  Function Add($Filter)
  {
    $Key=$Filter->GetName();
    If($OldFilter=$this->Map[$Key]?? Null)
    {
      Log('Error', 'Filter ', $Key, ' has already exists');
      $OldFilter->Remove();
      UnSet($this->Map[$Key]);
    }
    $Filter->Init($this->Source);
    $this->Map[$Key]=$Filter;
    $this->List_Add($Filter);
    Return $Filter;
  }
  
  Function FileStart()
  {
    ForEach($this As $Filter)
      $Filter->FileStart();
  }

  Function ProcessAll($Document):?Bool
  {
    $Changed=False;
    ForEach($this As $Filter)
    {
    //Log('Debug', 'Process.Filter.',$Filter->GetName(),'.Enable=',$Filter->Enable);
      $Result=$Filter->ProcessAll($Document);
      If($Result===False) Return False; //Error happend
      If($Result===True) $Changed=True;
    }
    Return $Changed? True:Null;
  }

  Function Option_Do($Op)
  {
    Parent::Option_Do($Op);
    ForEach($this As $Filter)
      $Op->Sub($Filter->GetName(), $Filter);
  }
}
