<?
namespace Reformat\Token;
Include_Once 'Linked/List.php';

Class TList Extends TBase
{
  Use \Reformat\Linked\TList;

  Function AddText($Id, $Text, $Line, $Pos)
  {
    $this->Add(New TText($Id, $Text, $Line, $Pos));
  }

  Function IsText() { Return False; }

//****************************************************************
// String
  
  Function _ToString(Array &$Result)
  {
    For($Item=$this->First; $Item; $Item=$Item->Next)
      $Item->_ToString($Result);
  }

//****************************************************************
// Change

  Protected Function DoResetChanged()
  {
    For($Item=$this->First; $Item; $Item=$Item->Next)
      $Item->ResetChanged();
  }

//****************************************************************
}