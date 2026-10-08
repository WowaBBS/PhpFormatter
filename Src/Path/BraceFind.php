<?
NameSpace Reformat\Path;

Use Function Reformat\Log;

Class BraceFind
{
  Static Function Left($Token)
  {
  //Log('Debug', 'Brace::FindLeft for ', $Token)->File($Token->GetFilePos()->ToArgs());
    For($Item=$Token->Prev; $Item; $Item=$Item->Prev)
    {
      If($Item->GetTypeHandler()!=='Text') Continue;
    //Log('Debug', 'Brace::FindLeft_Next ', [$Item->Text])->File($Item->GetFilePos()->ToArgs());
      $Type=EBrace::Detect($Item->Text);
      If( $Type->IsLeft  ()) Return $Item;
      If(!$Type->IsRight ()) Continue;
     
      $Next=Self::Left($Item);
      If(!$Next) Break;
      $Need=EBrace::Pair($Item->Text);
      If($Need!==$Next->Text)
        Log('Error', 'Need brace ', $Need, ' ', $Item->GetFilePos(), ' actual is ', $Next->Text, ' ', $Next->GetFilePos());
      $Item=$Next;
    }
    Return Null;
  }

  Static Function Right($Token)
  {
  //Log('Debug', 'Brace_FindRight for ', $Token)->File($Token->GetFilePos()->ToArgs());
    For($Item=$Token->Next; $Item; $Item=$Item->Next)
    {
      If($Item->GetTypeHandler()!=='Text') Continue;
    //Log('Debug', 'Brace_FindRight_Next ', [$Item->Text])->File($Item->GetFilePos()->ToArgs());
      $Type=EBrace::Detect($Item->Text);
      If( $Type->IsRight ()) Return $Item;
      If(!$Type->IsLeft  ()) Continue;
     
      $Next=Self::Right($Item);
      If(!$Next) Break;
      $Need=EBrace::Pair($Item->Text);
      If($Need!==$Next->Text)
        Log('Error', 'Need brace ', $Need, ' ', $Item->GetFilePos(), ' actual is ', $Next->Text, ' ', $Next->GetFilePos());
      $Item=$Next;
    }
    Return Null;
  }
}