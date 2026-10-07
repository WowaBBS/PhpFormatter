<?
namespace Reformat\Filter;

class TTab Extends TBase
{
  Var $Tab='  ';

  Static Function GetName() { Return 'Tab'; }
  
  Function ProcessText($Token):Void
  {
    $Token->SetText(Str_Replace("\t", $this->Tab, $Token->Text));
  }
}
