<?
NameSpace Reformat\Using;

Use Function Reformat\Log;

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
    Global $Using_Translate; //TODO: Workaround

    $Map=[];
    ForEach($this->UsedBy As $Item)
    {
      $Path     =$Item->Path;
      $FileName =$Item->FilePos->FileName ;
      $Line     =$Item->FilePos->Line     ;
      $FileName =StrTr($FileName, '\\', '/');
      $FileName =LTrim($FileName, '/');
      
      If($Using_Translate)
        $Using_Translate($this, $FileName, $Line, $Path);

      $Rec=&$Map[$Path];
      $Rec??=[];
      $RecFile=&$Rec[StrToLower($FileName)];
      $RecFile??=[];
      $RecFile[$Line]=$FileName;
    }
    UnSet($Rec);
    UnSet($RecFile);
    $Res=[];
    ForEach($Map As $Path=>$List)
    {
      KSort($List, SORT_NATURAL);
      $R=[];
      ForEach($List As $Lines)
      {
        $FileName =Array_First ($Lines);
        $Lines    =Array_Keys  ($Lines);
        Sort($Lines);
        $R[]=$FileName.':'.Join(':', $Lines);
      }
      $Res[$Path]=$Path.' ('.Join(', ', $R).')';
    }    
    Return $Res;
  }
  
  Function Add(TDependent $Who) { $this->UsedBy[]=$Who; }
}