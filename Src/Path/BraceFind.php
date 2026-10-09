<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

Class BraceFind
{
  Static Function IsIgnorable(?TToken $Token) { Return BraceSkip::IsIgnorable($Token); } //TODO: Move into Token
  
  Static Function Left(?TToken $Token):?TToken
  {
  //Log('Debug', 'Brace::FindLeft for ', $Token)->File($Token->GetFilePos()->ToArgs());
    For($Item=$Token?->Prev; $Item; $Item=$Item->Prev)
    {
      If(Self::IsIgnorable($Item)) Continue;
  //Log('Debug', 'Brace::FindLeft_Next ', [$Item->Text])->File($Item->GetFilePos()->ToArgs());
      $Type=EBraceType::Detect($Item->Text);
      If( $Type->IsLeft  ()) Return $Item;
      If(!$Type->IsRight ()) Continue;
     
      $Next=Self::Left($Item);
      If(!$Next) Break;
      $Need=EBraceType::Pair($Item->Text);
      If($Need!==$Next->Text)
        Log('Error', 'Need brace ', $Need, ' ', $Item->GetFilePos(), ' actual is ', $Next->Text, ' ', $Next->GetFilePos());
      $Item=$Next;
    }
    Return Null;
  }

  Static Function Right(?TToken $Token):?TToken
  {
  //Log('Debug', 'Brace_FindRight for ', $Token)->File($Token->GetFilePos()->ToArgs());
    For($Item=$Token?->Next; $Item; $Item=$Item->Next)
    {
      If(Self::IsIgnorable($Item)) Continue;
    //Log('Debug', 'Brace_FindRight_Next ', [$Item->Text])->File($Item->GetFilePos()->ToArgs());
      $Type=EBraceType::Detect($Item->Text);
      If( $Type->IsRight ()) Return $Item;
      If(!$Type->IsLeft  ()) Continue;
     
      $Next=Self::Right($Item);
      If(!$Next) Break;
      $Need=EBraceType::Pair($Item->Text);
      If($Need!==$Next->Text)
        Log('Error', 'Need brace ', $Need, ' ', $Item->GetFilePos(), ' actual is ', $Next->Text, ' ', $Next->GetFilePos());
      $Item=$Next;
    }
    Return Null;
  }
}