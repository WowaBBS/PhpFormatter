<?
NameSpace Reformat\Using;

Use Function Reformat\Log;

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
  
  Function Add(String $Key, Mixed $Value, Bool $IfNotExists=False):TPoint
  {
    If(Is_Object($Value) && $Value InstanceOf TPoint)
      Return Log('Error', 'TUsingPoints: Unknown object')
        ->BackTrace()->Debug($Value)->Ret();
    
    If(!Array_Key_Exists($Key, $this->Map))
    {
      $Entity=New TPoint($Key, $Value);
      $this->Map[$Key]=$Entity;
      If($Last=$this->Last_Used?? Null)
        $Last->List_Insert($Entity);
      Else
        $this->List_Add($Entity);
      $this->Last_Used=$Entity;
      Return $Entity;
    }
    $Current=$this->Map[$Key];
    $this->Last_Used=$Current;
    If($IfNotExists) Return $Current;
    If($this->Conflict===False) Return $Current;
    If($Current->Value===$Value) Return $Current;
    Log('Error', 'Value for ', $Key, ' wos changed:')->Debug([
      'Old' =>$Current ->Value,
      'New' =>$Value,
    ]);
    If($this->Conflict===False) Return $Current;
    
    $Current->Value=$Value;
    Return $Current;
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