<?
namespace Reformat\Filter;

Class TList
{
  Var $List=[];
  
  Function Add($Filter)
  {
    $Key=$Filter->GetName();
    If(IsSet($this->List[$Key]))
      Log('Error', 'Filter ', $Key, ' has already exists');
    $this->List[$Key]=$Filter;
  }
  
  Function FileStart($Info)
  {
    ForEach($this->List As $Filter)
      $Filter->FileStart($Info);
  }

  Function ProcessAll($Tokens)
  {
    $Changed=False;
    ForEach($this->List As $Filter)
    {
      $Result=$Filter->ProcessAll($Tokens);
      If($Result===False) Return False; //Error happend
      If(Is_Array($Result))
      {
        $Changed=True;
        $Tokens=$Result;
      }
    }
    Return $Changed? $Tokens:Null;
  }
}
