<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

/**
 * Contains full path
 */
Class TResult Implements \ArrayAccess, \Countable, \IteratorAggregate
{
  Var Array $List=[];
  
  Function IsEmpty():Bool { Return !$this->List; }

//****************************************************************
// ArrayAccess interface

  Function OffsetExists ($k    ):Bool  { Return Is_Int($k) && $k>=0 &&$k<Count($this->List); }
  Function OffsetGet    ($k    ):Mixed { Return $this->List[$k];     }
  Function OffsetSet    ($k ,$v):Void  { If($k===Null) $this->List[]=$v; Else Log('Fatal', 'Unsupported'); }
  Function OffsetUnset  ($k    ):Void  { Log('Fatal', 'Unsupported'); }
  
//****************************************************************
// Countable interface

  Function Count():Int { Return Is_Array($this->List)? Count($this->List):0; }
  
//****************************************************************
// IteratorAggregate interface
  
  Function GetIterator():\Traversable { Return New \ArrayIterator($this->List); }
  
//****************************************************************
  Function ToString()
  {
    $Res=[];
    ForEach($this->List As $Item)
      If($Item->IsSignificant())
        $Item->_ToString($Res);
    Return Join($Res);
  }

  Function ToDebug()
  {
    $Res=[];
    ForEach($this->List As $Item)
      $Res[]=$Item->ToDebug();
    Return $Res;
  }
//****************************************************************
  
}