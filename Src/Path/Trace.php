<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

Class TTrace
{
  //TODO: Alternative syntax is not supported now:
  //  if(...): ... elseif(...): ... else: ... endif;
  //  switch(...): ...  case ...: ... endswitch;
  //  while(...) ... endwhile;
  //  do: while???
  //  for(...) ... endfor;
  //  foreach(...) ... endforeach;
  //  declare(...) ... enddeclare;
  
  Function __Construct()
  {
  }
  
  Function FindNameSpace($Token)
  {
    //TODO: Cache
    $Doc=$Token->GetDocument();
    $NameSpace=Null;
    For($Item=$Doc->First; $Item; $Item=$Item->Next)
      If(!BraceSkip::IsIgnorable($Item))
      {
        If($Item->Id===T_NAMESPACE) { $NameSpace=$Item; Continue; }
        If($Item->Id===T_STRING) Continue;
        If($Item->Text==='{') Return;
        If($Item->Text===';') Break;
        Return;
      }
    If(!$NameSpace) Return;
    $Res=New TLine($NameSpace);
    Return $Res;
  }
  
  Function MakeBracesPath($Token)
  {
    $Lines=[];
    $NameSpace=$this->FindNameSpace($Token);
    For($Item=TextLine::Find($Token)?? $Token; $Item; $Item=BraceFind::Left($Item))
      $Lines[]=New TLine($Item);
    If($NameSpace && !$NameSpace->IsSame(Array_Last($Lines)))
      $Lines[]=$NameSpace;
    $Lines=Array_Reverse($Lines);
    
    $Res=New TResult();
    $Detect=New TDetect();
    ForEach($Lines As $k=>$Line)
    {
      $OldState=$Detect->State;
      $Item=$Detect->Line($Line);
      $Res[]=$Item;
      If($Item->IsSignificant())
        Continue;
      
      If($Line->IsEmpty())
        $Item->Value.='\Empty:'.$Token->GetFilePos()->ToString();
      Else
        $Item->Value='{'.$Line->DebugPos().':'.$OldState.'->'.$Detect->State.':'.$Item->Value.'}';
    }

    $Res[]=$Detect->EndPoint($Token);

    Return $Res;
  }
}