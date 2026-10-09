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

    $EndPoint=New TItem(Line: New TLine($Token, True));
    Switch($Token->Id)
    {
    Case T_DOC_COMMENT              : $EndPoint->_Set('Doc'     , '//' ,'Doc'     ); Break;
    Case T_COMMENT                  : $EndPoint->_Set('Comment' , '//' ,'Comment' ); Break;
    Case T_INLINE_HTML              : $EndPoint->_Set('Data'    , '//' ,'Data'    ); Break;
    Case T_CONSTANT_ENCAPSED_STRING : $EndPoint->_Set('String'  , '//' ,'String'  ); Break;
  //Case T_ENCAPSED_AND_WHITESPACE  : $EndPoint->_Set('HereDoc' , '//' ,'HereDoc' ); Break;
    Default                         : $EndPoint->_Set('Comment' , '//', '#'.($GLOBALS['TokenNameById'][$Token->Id]?? $Token->Id));
    }
    $Res[]=$EndPoint;
  
    Return $Res;
  }
}