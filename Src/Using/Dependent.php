<?
NameSpace Reformat\Using;

Use Reformat\Token\TBase   As TToken      ;
Use Reformat\Path\TResult  As TEntityPath ;
Use Reformat\Path\TTrace   As TPathTrace  ;
Use Reformat\FilePos\TInfo As TFilePos    ;

Use Function Reformat\Log;

/*
 * Dependant entity contain who is used this entity point
 */
Class TDependent
{
  Var TFilePos $FilePos ;
  Var String   $Path    ;
  
  Function __Construct(
    TFilePos $FilePos ,
    String   $Path    ,
  )
  {
    $this->FilePos =$FilePos ;
    $this->Path    =$Path    ;
  //Log('Debug', 'Path: ', $this->Path)->File($this->FilePos->ToArgs());
  }
  
  Static Function FromToken(TToken $Token)
  {
    $FilePos    =$Token->GetFilePos();
    $PathTrace  =New TPathTrace();
    $PathResult =$PathTrace->MakeBracesPath($Token);
    $Path       =$PathResult->ToString();
    
    Return New Self($FilePos, $Path);
  }
  
//Function GetKey() { Return $this->Path; }
}
