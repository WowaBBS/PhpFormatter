<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

/**
 * Contains all tokens related to a single entity
 */
Class TLine Implements \ArrayAccess, \Countable, \IteratorAggregate
{
  Var TToken $Token  ;
  Var Array  $List   ;
  Var Int    $Goal   ;
  Var Int    $Offset =0;
  
  Function __Construct(TToken $Token, Bool $OnlyOne=False)
  {
    If($OnlyOne)
    {
      $Res=[$Token];
      $Goal=0;
    }
    Else
    {
      $Res=[];
      For($Item=$Token; $Item; $Item=BraceSkip::Prev($Item))
        $Res[]=$Item;
      $Res=Array_Reverse($Res);
      $Goal=Count($Res)-1;
      If(BraceSkip::IsIgnorable($Token)) Array_Pop($Res);
      For($Item=BraceSkip::Next($Token->Next); $Item; $Item=BraceSkip::Next($Item))
        $Res[]=$Item;
    }
      
    $this->Token =$Token ;
    $this->List  =$Res   ;
    $this->Goal  =$Goal  ;
  }
  
  Function IsEmpty():Bool { Return !$this->List; }
  
  Function IsSame(?TLine $v)
  {
    If(!$v) Return False;
    Return 
      Array_First ($this->List)===Array_First ($v->List) &&
      Array_Last  ($this->List)===Array_Last  ($v->List);
  }

//****************************************************************
// ArrayAccess interface

  Function OffsetExists ($k    ):Bool  { $k+=$this->Offset; Return $k>=0 && $k<Count($this->List); }
  Function OffsetGet    ($k    ):Mixed { Return $this->List[$k+$this->Offset]?? Null;     }
  Function OffsetSet    ($k ,$v):Void  { Log('Fatal', 'Unsupported'); }
  Function OffsetUnset  ($k    ):Void  { Log('Fatal', 'Unsupported'); }
  
//****************************************************************
// Countable interface

  Function Count():Int { Return Count($this->List)-$this->Offset; }
  
//****************************************************************
// IteratorAggregate interface
  
  Function GetIterator():\Traversable { Return !$this->Offset? New \ArrayIterator($this->List):Log('Fatal', 'Iterator does not support Offset')->Ret(); }
  
//****************************************************************
// Debug

  Function DebugPos()
  {
    $Path  =$this->List  ;
    $Token =$this->Token ;
    $Len   =Count($Path);
    $Pos2=$this->Token  ->GetFilePos();
    If(!$Len) Return $Pos2->ToString();
  
    $Pos1=$Path[0      ]->GetFilePos();
    $Pos3=$Path[$Len-1 ]->GetFilePos();
    
    $Pos3=$Pos3->ToString($Pos2);
    $Pos2=$Pos2->ToString($Pos1);
    $Pos1=$Pos1->ToString();
    
    If($Pos1===$Pos2) Return $Pos1===$Pos3? $Pos1:'!'.$Pos1.'-'.$Pos3;
    If($Pos3===$Pos2) Return $Pos1.'-!'.$Pos3;
    Return $Pos1.'-'.$Pos2.'-'.$Pos3;
  }
  
//****************************************************************
  
}