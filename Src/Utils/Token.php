<?
NameSpace Reformat\Utils\Token;
//Use Function Reformat\Log;

Function Tokenize($Source)
{
  $Document=New \Reformat\Token\TList();
  $Tokens = \PhpToken::Tokenize($Source); //, TOKEN_PARSE); //Token_Get_All($Source);
  ForEach($Tokens As $Item)
    $Document->AddText(
      $Item->id   ,
      $Item->text ,
      $Item->line ,
      $Item->pos  ,
    );
  Return $Document;
}

Function TokenizeCode($Source)
{
  $Document=Tokenize('<?php '.$Source);
  $First=&$Document->First;
  $First->Remove();
  $Text=&$First->Text;
  If($Text===' ')
    $First->Remove();
  Else
    $Text=SubStr($Text, 1);
  Return $Document;
}

Function RemoveComments($Tokens)
{
  ForEach($Tokens As $Item)
    If($Item->Is(T_COMMENT))
      $Item->Remove();
}

Function RemoveWhiteSpaces($Tokens)
{
  ForEach($Tokens As $Item)
    If($Item->Is(T_WHITESPACE))
      $Item->Remove();
}

Function TrimWhiteSpaces($Tokens, $With=' ')
{
  ForEach($Tokens As $Item)
    If($Item->Is(T_WHITESPACE))
      $Item->Text=$With;
}
