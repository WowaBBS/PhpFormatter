<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

Class Valuable
{
  Static Function Is(?TToken $Token) //TODO: Move into Token
  {
    If(!$Token) Return False;
    If($Token->GetTypeHandler()!=='Text') Return False;
    Return !$Token->Is( //Ignore Ignorable
      T_COMMENT     , 
      T_DOC_COMMENT , 
      T_WHITESPACE  ,
      T_OPEN_TAG    , //TODO: Why?
    );
  }

  Static Function First(?TToken $Token):?TToken
  {
    Return Self::Is($Token)? $Token:Self::Next($Token);
  }
  
  Static Function Last(?TToken $Token):?TToken
  {
    Return Self::Is($Token)? $Token:Self::Prev($Token);
  }
  
  Static Function Next(?TToken $Token):?TToken
  {
    For($Item=$Token?->Next; $Item; $Item=$Item->Next)
    {
      If(!Self::Is($Item)) Continue;
      Return $Item;
    }
    Return Null;
  }

  Static Function Prev(?TToken $Token):?TToken
  {
    For($Item=$Token?->Prev; $Item; $Item=$Item->Prev)
    {
      If(!Self::Is($Item)) Continue;
      Return $Item;
    }
    Return Null;
  }
}