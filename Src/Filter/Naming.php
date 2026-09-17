<?
namespace Reformat\Filter;

class TNaming Extends TBase
{
  Static Function GetName() { Return 'KeyWord'; }

  Function Process($token)
  {
    global $keywordTokens;
    global $stringKeywords;
    
    // TODO: Add checking functions and classes names

    $id   = $token->id   ;
    $text = strtolower($token->text );
  
    //Обычный символ: {};=+ etc.
    If($id<128) Return;

    /*
     * Обычные ключевые слова PHP.
     *
     * Меняем только само содержимое токена.
     * Пробелы, комментарии, строки и т.д. сюда не попадут.
     */
    if (IsSet($keywordTokens[$id])) {}

    /*
     * TRUE/FALSE/NULL и некоторые типы могут быть T_STRING.
     *
     * Важно: сравниваем именно T_STRING, чтобы не менять
     * произвольные токены.
     */
    elseif ($id === T_STRING && isset($stringKeywords[$text])) {}
    else return;
    
    Return $text;
  }
}
