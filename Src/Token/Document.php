<?
namespace Reformat\Token;

Class TDocument Extends TList
{
  Var String $FileName  ='Source';
  Var Int    $StartLine =0;
  Var Int    $StartPos  =0;
  Var Int    $FirstPos  =0;
  
  Function __Construct(
    Array $SourceInfo=[],
  )
  {
    $this->FileName  =$SourceInfo[0]?? $this->FileName  ;
    $this->StartLine =$SourceInfo[1]?? $this->StartLine ;
    $this->StartPos  =$SourceInfo[2]?? $this->StartPos  ;
    $this->FirstPos  =$SourceInfo[3]?? $this->StartPos  ;
  }
  
  Function GetSourceInfo()
  {
    Return [
      $this->FileName  ,
      $this->StartLine ,
      $this->StartPos  ,
      $this->FirstPos  ,
    ];
  }  
}