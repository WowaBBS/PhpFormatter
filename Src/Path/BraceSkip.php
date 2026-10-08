<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

Class BraceSkip
{
  Static Function IsIgnorable($Token)
  {
    Return $Token->Is( //Ignore Ignorable
      T_COMMENT     , 
      T_DOC_COMMENT , 
      T_WHITESPACE  ,
      T_OPEN_TAG    , //TODO: Why?
    );
  }

  Static Function Prev(TToken $Token):?TToken 
  {
  //Log('Debug', 'BraceSkip::Prev.Begin=',$Token->Text);
    For($Item=$Token->Prev; $Item; $Item=$Item->Prev)
    {
      If($Item->GetTypeHandler()!=='Text') Continue;
      If(Self::IsIgnorable($Item)) Continue;
    //Log('Debug', 'BraceSkip::Prev.Next=',$Item->Text);
      If($Item->Text==='}') Return Null;
      If($Item->Text===';') Return Null;
      If($Item->Text===',') Return Null;
      $Type=EBrace::Detect($Item->Text);
      If($Type->IsRight ()) Return BraceFind::Left($Item);
      If($Type->IsLeft  ()) Return Null;
      Return $Item;
    }
  }
  
  Static Function Next(TToken $Token):?TToken 
  {
    $Type=EBrace::Detect($Token->Text);
    If($Type->IsLeft())
    { 
      $Token=BraceFind::Right($Token);
      If($Token?->Text==='}') Return Null;
    }
    For($Item=$Token?->Next; $Item; $Item=$Item->Next)
    {
      If($Item->GetTypeHandler()!=='Text') Continue;
      If(Self::IsIgnorable($Item)) Continue;
      If($Item->Text==='{') Return $Item;
      If($Item->Text===';') Return Null;
      If($Item->Text===',') Return Null;
      $Type=EBrace::Detect($Item->Text);
      If($Type->IsLeft  ()) Return $Item;
      If($Type->IsRight ()) Return Null;
      Return $Item;
    }
    Return Null;
  }
  
}