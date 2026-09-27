<?
namespace Reformat\Token;

Class TDocument Extends TList
{
  Var String $FileName  ='Source';
  Var Int    $StartLine =0;
  Var Int    $StartPos  =0;
  Var Int    $FirstPos  =0;
  
  Function __Construct(
    Array $FilePos=[],
  )
  {
    $this->FileName  =$FilePos[0]?? $this->FileName  ;
    $this->StartLine =$FilePos[1]?? $this->StartLine ;
    $this->StartPos  =$FilePos[2]?? $this->StartPos  ;
    $this->FirstPos  =$FilePos[3]?? $this->StartPos  ;
  }
  
  Function GetFilePos()
  {
    Return [
      $this->FileName  ,
      $this->StartLine ,
      $this->StartPos  ,
      $this->FirstPos  ,
    ];
  }  
}