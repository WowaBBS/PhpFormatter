<?
namespace Reformat\Token;

Class TText Extends TBase
{
  Var Int    $Id  =0;
  Var String $Text =''; //{ Set($v){ Return Self::_Assign($this->Text ,$v); }}
  
  Function __Construct($Id, $Text)
  {
    $this->Id   =$Id   ;
    $this->Text =$Text ;
  }
  
  Function SetId   ($v) { Return Self::_Assign($this->Id   ,$v); }
  Function SetText ($v) { Return Self::_Assign($this->Text ,$v); }
  
  Function _ToString(Array &$Result)
  {
    $Result[]=$this->Text;
  }
  
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