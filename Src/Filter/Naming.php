<?
namespace Reformat\Filter;

class TNaming Extends TBase
{
  Static Function GetName() { Return 'Naming'; }

  Function ProcessText($Token)//:Void|String
  {
    global $keywordTokens;
    global $stringKeywords;
    
    // TODO: Add checking functions and classes names

    $Id   = $Token->Id   ;
    $Text = StrToLower($Token->Text );
  
    //Обычный символ: {};=+ etc.
    If($Id<128) Return;

    /*
     * Обычные ключевые слова PHP.
     *
     * Меняем только само содержимое токена.
     * Пробелы, комментарии, строки и т.д. сюда не попадут.
     */
    If (IsSet($keywordTokens[$Id])) {}

    /*
     * TRUE/FALSE/NULL и некоторые типы могут быть T_STRING.
     *
     * Важно: сравниваем именно T_STRING, чтобы не менять
     * произвольные токены.
     */
    ElseIf ($Id === T_STRING && IsSet($stringKeywords[$Text])) {}
    Else Return;
    
    Return $Text;
  }
}
