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
  
  Var $ReqiredNameSpace=False;
  
  Function FindNameSpace($Token)
  {
    //TODO: Cache
    $Doc=$Token->GetDocument();
    $NameSpace=Null;
    For($Item=Valuable::First($Doc->First); $Item; $Item=Valuable::Next($Item))
    {
      If($Item->Id===T_NAMESPACE) //Parse namespace
      {
        $NameSpace = $Item;
        $Name      = $Item=Valuable::Next($Item);
        $End       = $Item=Valuable::Next($Item);
        
        If($Name ?->Is(T_NAME_QUALIFIED, T_STRING) &&
           $End  ?->Text ===';')
          Break;
        
        $NameSpace=Null;
        Break;
      }
      If($Item->Id===T_DECLARE) //Skip deline
      {
        $Decline   = $Item;
        $Directive = $Item=Valuable::Next($Item); $Item=BraceFind::Right($Item);
        $End       = $Item=Valuable::Next($Item);
        
        If($Directive ?->Text ==='(' &&
           $End       ?->Text ===';')
          Continue;
        
        Break;
      }
      Break; //Others are not allowed
    }
    If($NameSpace)
       Return New TLine($NameSpace);
       
    If(!$this->ReqiredNameSpace) Return;

    $Log=Log('Error', 'Reqired NameSpace:')->File($Doc->GetFilePos()->ToArgs());
    For($Item=Valuable::First($Doc->First), $Count=10; $Item && $Count>0; $Item=Valuable::Next($Item), $Count--)
      $Log('  ', $Item);
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