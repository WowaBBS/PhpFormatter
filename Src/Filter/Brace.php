<?
namespace Reformat\Filter;

/**
 * Functions:
 *  * Groups []{}()
 *  * Groups Others tokens {} inline spaces and comments
 *  * Groups Others tokens;  inline spaces and comments
 */
class TBrace Extends TBase
{
  Static Function GetName() { Return 'Brace'; }

  Function ProcessText($Token)//:Void|String
  {
    //TODO ()[]{}
  }
}
