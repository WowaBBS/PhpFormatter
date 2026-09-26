<?
namespace Reformat\Token;
Use Function Reformat\Utils\Str\IsWord;

Class TText Extends TBase
{
  Var        $Id   = 0  ;
  Var String $Text = '' ; //{ Set($v){ Return Self::_Assign($this->Text ,$v); }}
  Var Int    $Line = 0  ;
  Var Int    $Pos  = 0  ;
  Var Int    $Tab  = 0  ;
  
  Function __Construct($Id, String $Text, Int $Line, Int $Pos)
  {
    $this->Id   =$Id   ;
    $this->Text =$Text ;
    $this->Line =$Line ;
    $this->Pos  =$Pos  ;
  }
  
  Function GetId() { Return $this->Id; }
  Function SetId   ($v) { Return Self::_Assign($this->Id   ,$v); }
  Function SetText ($v) { Return Self::_Assign($this->Text ,$v); }
  Function GetInnerText():String { Return $this->Text; }
  
  Function GetTypeHandler() { Return 'Text'; }
  
  Function IsWord() { Return IsWord($this->Text); }
  
  Function GetFilePos()
  {
    $SourceInfo=$this->GetDocument()?->GetSourceInfo()?? ['Source'];
    Return [
      $SourceInfo[0], //File
      $this->Line +($SourceInfo[1]?? 0),
      $this->Pos  +($SourceInfo[$this->Line<=1? 3:2]?? $SourceInfo[2]?? 0),
    ];
  }  
  
//****************************************************************
// String
  
  Function _ToString(Array &$Result)
  {
    $Result[]=$this->Text;
  }
  
//****************************************************************
// Debug

  Function GetDebug()
  {
    If($this->Id<128) Return [$this->Text];
    Return [$this->GetTokenName(), ': ', $this->Text];
  }
  
//****************************************************************

  Function GetLowerText()
  {
    Return StrToLower($this->Text);
  }
}