<?
NameSpace Reformat\Utils\Token;

Use Reformat\FilePos\TInfo As TFilePos;
Use Function Reformat\Log;
Use Function Reformat\Utils\Str\LinePos;

Function Tokenize(
  String    $Source ,
  ?TFilePos $FilePos=Null,
)
{
  $Document=New \Reformat\Token\TDocument($FilePos?? New TFilePos('PHPCode'));
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

Function TokenizeCode(
  String    $Source    ,
  ?TFilePos $FilePos=Null,
)
{
  $StartWith='<?php ';
  $Document=Tokenize($StartWith.$Source, $FilePos);
  $First=&$Document->First;
  If($First->Text===$StartWith)
    $First->Remove();
  Else
  {
    $First->Text =SubStr($First->Text, StrLen($StartWith));
    $First->Id   =T_WHITESPACE;
  }
  LinePosReIndex($Document);
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

Function LinePosReIndex($Tokens, $Tab=0)
{
  $Line     =0;
  $FirstPos =0;
  $Pos      =0;

  ForEach($Tokens As $Token)
  {
    If($Token->GetTypeHandler()!=='Text') Continue;
    $Token->Line =$Line ;
    $Token->Pos  =$Pos  ;
    $Token->Tab  =$Tab  ;
    $Text=$Token->Text;
    
    If(LinePos($Text, $Line, $Pos))
    {
      $LastLineSize=$Pos;
      $Pos+=$FirstPos;
      Switch($Token->Id)
      { //TODO: Heredoc, Yield From, Tag, Html
      Case \T_WHITESPACE    : $Tab=$LastLineSize; Break; //Ok
      CAse \T_CONSTANT_ENCAPSED_STRING : // "TheString"
      Case \T_ENCAPSED_AND_WHITESPACE  :
      Case \T_START_HEREDOC :
      Case \T_OPEN_TAG      :
      Case \T_COMMENT       :
      Case \T_DOC_COMMENT   :
      Case \T_YIELD_FROM    :
      Case \T_INLINE_HTML   :
      Case \T_CLOSE_TAG     :
        Break;
      Default: Log('Warning', '\n is in ', $Token->GetTokenName())->Debug($Token->Text);
      }
    }
  }
}
