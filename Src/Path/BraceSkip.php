<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

Class BraceSkip
{
  Static Function IsIgnorable(TToken $Token) //TODO: Move into Token
  {
    If($Token->GetTypeHandler()!=='Text') Return False;
    Return $Token->Is( //Ignore Ignorable
      T_COMMENT     , 
      T_DOC_COMMENT , 
      T_WHITESPACE  ,
      T_OPEN_TAG    , //TODO: Why?
    );
  }

  Static Function Prev(TToken $Token):?TToken 
  {
    For($Item=$Token->Prev; $Item; $Item=$Item->Prev)
    {
      If(Self::IsIgnorable($Item)) Continue;

      If($Item->Text==='}') Return Null;
      If($Item->Text===';') Return Null;
      If($Item->Text===',') Return Null;
      
      $Type=EBraceType::Detect($Item->Text);
      If($Type->IsRight ()) Return BraceFind::Left($Item);
      If($Type->IsLeft  ()) Return Null;
      
      Return $Item;
    }
    Return Null;
  }
  
  Static Function Next(TToken $Token):?TToken 
  {
    $Type=EBraceType::Detect($Token->Text);
    If($Type->IsLeft())
    { 
      $Token=BraceFind::Right($Token);
      If($Token?->Text==='}') Return Null;
    }
    For($Item=$Token?->Next; $Item; $Item=$Item->Next)
    {
      If(Self::IsIgnorable($Item)) Continue;
      
      If($Item->Text==='{') Return $Item;
      If($Item->Text===';') Return Null;
      If($Item->Text===',') Return Null;
      
      $Type=EBraceType::Detect($Item->Text);
      If($Type->IsLeft  ()) Return $Item;
      If($Type->IsRight ()) Return Null;
      
      Return $Item;
    }
    Return Null;
  }
}