<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

/**
 * Contains all tokens related to a single entity
 */
Class TLine Implements \ArrayAccess, \Countable, \IteratorAggregate
{
  Var Array $List ;
  Var       $Pos  ;
  
  Function __Construct(TToken $Token)
  {
    $Res=[];
    For($Item=$Token; $Item; $Item=BraceSkip::Prev($Item))
      $Res[]=$Item;
    $Res=Array_Reverse($Res);
    $Pos=Count($Res)-1;
    If(BraceSkip::IsIgnorable($Res[$Pos])) Array_Pop($Res);
    For($Item=BraceSkip::Next($Token->Next); $Item; $Item=BraceSkip::Next($Item))
      $Res[]=$Item;
      
    $this->List =$Res;
    $this->Pos  =$Pos;
  }
  
  Function DebugPos()
  {
    $Path =$this->List ;
    $Pos  =$this->Pos  ;
    $Len  =Count($Path);
  
    $Pos1=$Path[0      ]->GetFilePos();
    $Pos2=$Path[$Pos   ]->GetFilePos();
    $Pos3=$Path[$Len-1 ]->GetFilePos();
    
    $Pos3=$Pos3->ToString($Pos2);
    $Pos2=$Pos2->ToString($Pos1);
    $Pos1=$Pos1->ToString();
    
    If($Pos1===$Pos2) Return $Pos1===$Pos3? $Pos1:'!'.$Pos1.'-'.$Pos3;
    If($Pos3===$Pos2) Return $Pos1.'-!'.$Pos3;
    Return $Pos1.'-'.$Pos2.'-'.$Pos3;
  }
  
  Function IsEmpty():Bool { Return !$this->List; }

//****************************************************************
// ArrayAccess interface

  Function OffsetExists ($k    ):Bool  { Return Is_Int($k) && $k>=0 &&$k<Count($this->List); }
  Function OffsetGet    ($k    ):Mixed { Return $this->List[$k];     }
  Function OffsetSet    ($k ,$v):Void  { Log('Fatal', 'Unsupported'); }
  Function OffsetUnset  ($k    ):Void  { Log('Fatal', 'Unsupported'); }
  
//****************************************************************
// Countable interface

  Function Count():Int { Return Is_Array($this->List)? Count($this->List):0; }
  
//****************************************************************
// IteratorAggregate interface
  
  Function GetIterator():\Traversable { Return New \ArrayIterator($this->List); }
  
//****************************************************************
  
}