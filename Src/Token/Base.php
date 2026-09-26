<?
namespace Reformat\Token;
use function Reformat\Log;
Include_Once 'Linked/Node.php';
$Loader->Load_Interface('/Debug/Custom');

Abstract Class TBase Implements \WLib\Debug\ICustom
{
  Use \Reformat\Linked\TNode;
  
  Abstract Function GetTypeHandler();
  Abstract Function GetInnerText():String;
  Function SetInnerText($v)
  {
    Log('Fatal', 'Unsupported SetInnerText for ', ...$this->GetDebug())
      ->Debug(['NewInnerText'=>$v, 'OldInnerText'=>$this->GetInnerText()]);
  }
  
  Function GetId() { Return 0; }
  
  Function Is(Int|String ...$Args) { Return In_Array($Id=$this->GetId(), $Args, True); } // Old style: || Is_Array($Args[0]?? Null) && In_Array($Id, $Args[0], True)
  Function GetTokenName() { $Id=$this->GetId(); Return Is_Int($Id)? ($Id<128? Ord($Id):Token_Name($Id)):$Id; }
  
//****************************************************************
// String
  
  Function ToString()
  {
    $Result=[];
    $this->_ToString($Result);
    Return Implode($Result);
  }

  Abstract Function _ToString(Array &$Result);
  
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
// Debug

  //WLib\Debug\ICustom
  Function Debug_Write(\WLib\Log\CFormat $To)
  {
    $To->Write(...$this->GetDebug());
  }

  Function GetDebug()
  {
    Return [Self::class, 'TODO: More information?'];
  }
  
  Function _Debug_Serialize(&$Res)
  {
    UnSet($Res['Next']);
    UnSet($Res['Prev']);
    $Res['TokenName']??=$this->GetTokenName();
  }
  
//****************************************************************
}