<?
namespace Reformat\Filter;

class TTab Extends TBase
{
  //TODO: Make tab space

  Static Function GetName() { Return 'Tab'; }
  
  Function ProcessText($Token)//:Void|String
  {
    Return Str_Replace("\t", '  ', $Token->Text);
  }
}
