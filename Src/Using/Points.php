<?
NameSpace Reformat\Using;

/*
 * List of using point
 */
Class TPoints Implements \ArrayAccess, \Countable, \IteratorAggregate
{
  Use \Reformat\Linked\TList;
  
  Var ?Bool  $Conflict=True; //True -- Check, False -- Ignore, Null -- Override
  Var  Array $Map=[];
  Var        $Last_Used;
  
  Function __Construct(?Bool $Conflict=True)
  {
    $this->Conflict=$Conflict;
  }
  
  Function Add(String $Key, Mixed $Value):TPoint
  {
    If(Is_Object($Value))
      Log('Error', 'TUsingPoints: Unknown object')->Debug($Value);
    Else
      $Value=New TPoint($Key, $Value);
    If(!Array_Key_Exists($Key, $this->Map))
    {
      $this->Map[$Key]=$Value;
      $Last=$this->Last_Used?? $this->List_Last;
      If($Last)
        $Last->List_Insert($Value);
      Else
        $this->List_Add($Value);
      Return $Value;
    }
    $Old=$this->Map[$Key];
    If($this->Conflict===False) Return $Old;
    If($Old->Value===$Value->Value) Return $Old;
    Log('Error', 'Value for ', $Key, ' wos changed:')->Debug([
      'Old' =>$Old   ->Value,
      'New' =>$Value ->Value,
    ]);
    If($this->Conflict===False) Return $Old;
    
    $this->Map[$Key]->Value=$Value->Value;
    Return $Old;
  }
  
  Function IsEmpty():Bool { Return Count($this->Map)===0; }
  Function ToCompare()
  {
    $Res=[];
    ForEach($this As $v)
      $Res[]=$v->Value;
    Return $Res;
  }

//****************************************************************
// ArrayAccess interface

  Function OffsetExists ($k    ):Bool  { Return Array_Key_Exists($k, $this->Map); }
  Function OffsetGet    ($k    ):Mixed { Return $this->Map[$k]; }
  Function OffsetSet    ($k ,$v):Void  { $this->Add($k, $v); }
  Function OffsetUnset  ($k    ):Void  { $this->Map[$k]?->List_Remove(); UnSet($this->Map[$k]); } //TODO: Move UnSet into List_Remove
  
//****************************************************************
// Countable interface

  Function Count():Int { Return Count($this->Map); }
  
//****************************************************************
// IteratorAggregate interface
  
//Function GetIterator():\Traversable { Return New \ArrayIterator($this->List); }
  
//****************************************************************
}