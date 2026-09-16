<?
namespace Reformat\Filter;
use function Reformat\Log;

class TTranslate Extends TBase
{
  Static Function GetName() { Return 'Translate'; }

  Function Process($Token)
  {
    if(!preg_match('/[\x80-\xFF]/', $Token->text)) Return;
    Log('Debug', 'Found: ', $Token->text);
  }
}
