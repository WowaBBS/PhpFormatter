<?
NameSpace Reformat\Filter;

Use Reformat\Token\TComment As TTokenComment;

Use Function Reformat\Log;
Use Function Reformat\Utils\Str\Starts_With_List;

/**
 * Functions:
 *  * Group inline comments
 *  * Detect code comments
 */
Class TComment Extends TBase
{
  Static Function GetName() { Return 'Comment'; }
  
  Var $Test=False;
  
  Function ProcessText($Token)//:Void|String|Token
  {
    If($Token->Id!==T_COMMENT && $Token->Id!==T_DOC_COMMENT) Return;
    $To=$Token;
    Switch($With=Starts_With_List($Token->Text, ['//', '#', '/*']))
    {
    Case '/*': Break;
    Case '#':
    Case '//':
      $Item=$To->Next;
      For(; $Item?->Is(T_WHITESPACE, T_COMMENT); $Item=$Item->Next)
        If($Item->Id===T_COMMENT)
        {
          If($Item->Tab!==$Token->Tab) Break;
          If(Str_Starts_With($Item->Text, $With)) Break;
          $To=$Item;
        }
      Break;
    Default:
      Log('Fatal', 'Unknown token')->Debug($Token);
      Return False;
    }
    Return New TTokenComment($Token, $To);
  }

  Function Option_Do($Op)
  {
    Parent::Option_Do($Op);
    $this->Test=$Op->GetSet('Test', $this->Test, 'Bool');
  }
}
