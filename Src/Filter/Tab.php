<?
namespace Reformat\Filter;

class TTab Extends TBase
{
  //TODO: Make tab space

  Static Function GetName() { Return 'Tab'; }
  
  Function Process($Token)
  {
    Return Str_Replace("\t", '  ', $Token->text);
  }
}
