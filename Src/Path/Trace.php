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
  
  Function MakeBracesPath($Token)
  {
    $Lines=[];
    For($Item=$Token; $Item; $Item=BraceFind::Left($Item))
      $Lines[]=New TLine($Item);
    $Lines=Array_Reverse($Lines);
    
    $Res=[];
    $Detect=New TDetect();
    ForEach($Lines As $k=>$Line)
    {
      $OldState=$Detect->State;
      $r=$Detect->Line($Line);
      If(!$r) Continue;
      [$Detected, $Ok]=$r;
      If($Ok)
      {
        $Res[]=$Detected;
        Continue;
      }
      
      If($Line->IsEmpty())
      {
        $Res[]='\Empty:'.$Token->GetFilePos()->ToString();
        Continue;
      }
      
      $Res[]='{'.$Line->DebugPos().':'.$OldState.'->'.$Detect->State.':'.$Detected.'}';
    }
    Return $Res;
  }
}