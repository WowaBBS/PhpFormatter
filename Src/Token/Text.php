<?
namespace Reformat\Token;

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
  
  Function SetId   ($v) { Return Self::_Assign($this->Id   ,$v); }
  Function SetText ($v) { Return Self::_Assign($this->Text ,$v); }
  
  Function IsText() { Return True; }
//****************************************************************
// String
  
  Function _ToString(Array &$Result)
  {
    $Result[]=$this->Text;
  }
  
//****************************************************************

  Function GetTokenName() { Return $this->Id<128? $this->Text:Token_Name($this->Id); }

  Function GetDebug()
  {
    If($this->Id<128) Return [$this->Text];
    Return [Token_Name($this->Id), ': ', $this->Text];
  }
  
  Function GetLowerText()
  {
    Return StrToLower($this->Text);
  }
}