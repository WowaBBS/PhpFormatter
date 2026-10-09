<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

Class TextLine
{
  Static Function IsIgnorable(?TToken $Token) { Return BraceSkip::IsIgnorable($Token); } //TODO: Move into Token

  Static Function Find(TToken $Token):?TToken 
  {
    Return Self::Next($Token)?? Self::Prev($Token);
  }
  
  Static Function Prev(?TToken $Token):?TToken 
  {
    If(!Self::IsIgnorable($Token)) Return $Token;
  
    For($Item=$Token?->Prev; $Item; $Item=$Item->Prev)
    {
      If(Self::IsIgnorable($Item)) Continue;

      If($Item->Line===$Token->Line) Return $Item;
      
      Break;
    }
    Return Null;
  }
  
  Static Function Next(?TToken $Token):?TToken 
  {
    If(!Self::IsIgnorable($Token)) Return $Token;
    
    For($Item=$Token?->Next; $Item; $Item=$Item->Next)
    {
      If(Self::IsIgnorable($Item)) Continue;

      If($Item->Line===$Token->Line) Return $Item;
      
      Break;
    }
    Return Null;
  }
}