<?
NameSpace Reformat\Utils\Token;
Use Function Reformat\Log;
Use Function Reformat\Utils\Str\LinePos;

Function Tokenize(
  String $Source    ,
  Array  $FilePos=[],
)
{
  $Document=New \Reformat\Token\TDocument($FilePos);
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
  String $Source    ,
  Array  $FilePos=[],
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

Function LinePosReIndex($Tokens, $Tab=0, $FilePos=Null)
{
  $FilePos??=$Tokens->GetFilePos();
  $Line     =$FilePos[1]?? 0;
  $FirstPos =$FilePos[2]?? 0;
  $Pos      =$FilePos[3]?? $FirstPos;

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
      Case \T_START_HEREDOC :
      Case \T_ENCAPSED_AND_WHITESPACE:
      Case \T_OPEN_TAG      :
      Case \T_COMMENT       :
      Case \T_DOC_COMMENT   :
      Case \T_YIELD_FROM    :
      Case \T_INLINE_HTML   :
        Break;
      Default: Log('Warning', '\n is in ', $Token->GetTokenName())->Debug($Token->Text);
      }
    }
  }
}
