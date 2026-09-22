<?
namespace Reformat\Token;
use function Reformat\Log;
Include_Once 'Linked/Node.php';

Class TBase //Extends \PhpToken 
{
  Use \Reformat\Linked\TNode;
  
  Function ToString()
  {
    $Result=[];
    $this->_ToString($Result);
    Return Implode($Result);
  }

  Function _ToString(Array &$Result)
  {
  }
  
//****************************************************************
// Change
  
  Var Bool $Changed=False;
  
  Function IsChanged():Bool { Return $this->Changed; }
  
  Function SetChanged()
  {
    If($this->Changed) Return;
  //Log('Debug', 'Changed?!?!');
    $this->Changed=True;
    $this->Parent?->SetChanged();
  //If($this->Parent) Log('Debug', 'Parent/Changed?!?!');
  }
  
  Function ResetChanged()
  {
    If(!$this->Changed) Return;
    $this->Changed=False;
    $this->DoResetChanged();
  }

  Protected Function DoResetChanged()
  {
  }
  
  //Function SetText ($v) { Return Self::_Assign($this->Text ,$v); }
  Function _Assign(&$To, $From)
  {
    If($To===$From) Return False;
    $To=$From;
    $this->SetChanged();
    Return True;
  }
  
  //Set=>Self::_CheckRet($this->Text ,$value);
  Function _CheckRet($To, $From)
  {
    If($To===$From) Return $To;
    $this->SetChanged();
    Return $From;
  }
//****************************************************************
}