<?
namespace Reformat\Token;
Include_Once 'Linked/List.php';

Class TList Extends TBase
{
  Use \Reformat\Linked\TList;

  Function AddText($Id, $Text)
  {
    $this->Add(New TText($Id, $Text));
  }

  Function _ToString(Array &$Result)
  {
    For($Item=$this->First; $Item; $Item=$Item->Next)
      $Item->_ToString($Result);
  }

  Protected Function DoResetChanged()
  {
    For($Item=$this->First; $Item; $Item=$Item->Next)
      $Item->ResetChanged();
  }
}