<?
namespace Reformat\Filter;

class TCrLf Extends TBase
{
  Static Function GetName() { Return 'CrLf'; }
  
  Function Process($Token)
  {
    Return Str_Replace(["\r\n", "\n\r", "\r"], "\n", $Token->text);
  }
}
