<?
namespace Reformat\Filter;

/**
 * Replaces \r\n, \n\r, \r to \n
 */
class TCrLf Extends TBase
{
  Static Function GetName() { Return 'CrLf'; }
  
  Function Process($Token)
  { //TODO: Configure
    Return Str_Replace(["\r\n", "\n\r", "\r"], "\n", $Token->text);
  }
}
