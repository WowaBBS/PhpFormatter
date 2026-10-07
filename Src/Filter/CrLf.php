<?
namespace Reformat\Filter;

/**
 * Replaces \r\n, \n\r, \r to \n
 */
class TCrLf Extends TBase
{
  Static Function GetName() { Return 'CrLf'; }
  
  Function ProcessText($Token):Void
  { //TODO: Configure
    $Token->SetText(Str_Replace(["\r\n", "\n\r", "\r"], "\n", $Token->Text));
  }
}
