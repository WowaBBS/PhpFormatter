<?
NameSpace Reformat\Using;

Use Reformat\Token\TBase   As TToken      ;
Use Reformat\Path\TResult  As TEntityPath ;
Use Reformat\Path\TTrace   As TPathTrace  ;
Use Reformat\FilePos\TInfo As TFilePos    ;

/*
 * Dependant entity contain who is used this entity point
 */
Class TDependent
{
  Var TFilePos $FilePos ;
  Var String   $Path    ;
  
  Function __Construct(TToken $Token)
  {
    $this->FilePos =$Token->GetFilePos();
    $PathTrace     =New TPathTrace();
    $PathResult    =$PathTrace->MakeBracesPath($Token);
    $this->Path    =$PathResult->ToString();
  }
  
//Function GetKey() { Return $this->Path; }
}
