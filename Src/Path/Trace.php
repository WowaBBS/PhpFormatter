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
  
  Function MakeBracesLine(TToken $Token):Array
  {
    $Res=[];
    For($Item=$Token; $Item; $Item=BraceSkip::Prev($Item))
      $Res[]=$Item;
    $Res=Array_Reverse($Res);
    $Pos=Count($Res)-1;
    If($Res[$Pos]->Is( //Ignore Ignorable
      T_COMMENT     , 
      T_DOC_COMMENT , 
      T_WHITESPACE  ,
      T_OPEN_TAG    , //TODO: Why?
    )) Array_Pop($Res);
    For($Item=BraceSkip::Next($Token->Next); $Item; $Item=BraceSkip::Next($Item))
      $Res[]=$Item;
    Return [$Pos, $Res];
  }
  
  Function MakeBracesPath($Token)
  {
    $Res=[];
    For($Item=$Token; $Item; $Item=BraceFind::Left($Item))
      $Res[]=$this->MakeBracesLine($Item);
    $List=Array_Reverse($Res);
    $State='Code';
    $Res=[];
    ForEach($List As $k=>[$Pos, $Path])
    {
      $OldState=$State;
      $r=Detect::Line($State, $Pos, $Path);
      If(!$r) Continue;
      [$State, $Detected, $Ok]=$r;
      If($Ok)
      {
        $Res[]=$Detected;
        Continue;
      }
      
      If(!$Path)
      {
        $Res[]='\Empty:'.$Token->GetFilePos()->ToString();
        Continue;
      }
      
      $Pos1=$Path[0              ]->GetFilePos();
      $Pos2=$Path[$Pos           ]->GetFilePos();
      $Pos3=$Path[Count($Path)-1 ]->GetFilePos();
      
      $Pos3=$Pos3->ToString($Pos2);
      $Pos2=$Pos2->ToString($Pos1);
      $Pos1=$Pos1->ToString();
      
      $Pos='';
      If($Pos1===$Pos2 && $Pos1===$Pos3)
        $Pos=$Pos1;
      ElseIf($Pos1===$Pos2)
        $Pos='!'.$Pos1.'-'.$Pos3;
      ElseIf($Pos3===$Pos2)
        $Pos=$Pos1.'-!'.$Pos3;
      Else
        $Pos=$Pos1.'-'.$Pos2.'-'.$Pos3;
      $Res[]='{'.$Pos.':'.$OldState.'->'.$State.':'.$Detected.'}';
    }
    Return $Res;
  }
}