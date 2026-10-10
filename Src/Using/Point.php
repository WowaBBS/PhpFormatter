<?
NameSpace Reformat\Using;

/*
 * Using point contain who is used this entity point
 */
Class TPoint
{
  Use \Reformat\Linked\TNode;
  
  Var String $Key   ;
  Var Mixed  $Value ;
  Var Array  $UsedBy=[];
  
  Function __Construct(
    String $Key   ,
    Mixed  $Value ,
  )
  {
    $this->Key   =$Key   ;
    $this->Value =$Value ;
  }
  
  Function Make_UsedIn()
  {
    $Map=[];
    ForEach($this->UsedBy As $Item)
    {
      $Rec=&$Map[$Item->Path];
      $Rec??=[];
      $Rec[$Item->FilePos->ToString()]=$Item->FilePos;
    }
    UnSet($Rec);
    $Res=[];
    ForEach($Map As $Path=>$List)
    {
      KSort($List, SORT_NATURAL);
      $R=[];
      $Prev=Null;
      ForEach($List As $FilePos)
      {
        $R[]=$FilePos->ToString($Prev);
        $Prev=$FilePos;
      }
      $Res[$Path]=$Path.' '.Join($R);
    }    
    Return $Res;
  }
  
  Function Add(TDependent $Who) { $this->UsedBy[]=$Who; }
}