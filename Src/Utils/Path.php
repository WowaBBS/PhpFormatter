<?
NameSpace Reformat\Utils;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

Class TPath
{
  const Brace_None  =0;
  const Brace_Open  =1;
  const Brace_Close =2;
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
  
  Const Array Braces=[
    '['=>[Self::Brace_Open  ,']'],
    '('=>[Self::Brace_Open  ,')'],
    '{'=>[Self::Brace_Open  ,'}'],
    ']'=>[Self::Brace_Close ,'['],
    ')'=>[Self::Brace_Close ,'('],
    '}'=>[Self::Brace_Close ,'{'],
  //';'=>[3,'' ],
  ];
  
  Function Prev_SkipBraces(TToken $Token):?TToken 
  {
  //Log('Debug', 'Prev_SkipBraces.Begin=',$Token->Text);
    For($Item=$Token->Prev; $Item; $Item=$Item->Prev)
    {
      If($Item->GetTypeHandler()!=='Text') Continue;
      If($Item->Is( //Ignore Ignorable
        T_COMMENT     , 
        T_DOC_COMMENT , 
        T_WHITESPACE  ,
        T_OPEN_TAG    , //TODO: Why?
      )) Continue;
    //Log('Debug', 'Prev_SkipBraces.Next=',$Item->Text);
      If($Item->Text==='}') Return Null;
      If($Item->Text===';') Return Null;
      $Type=Self::Braces[$Item->Text][0]?? Self::Brace_None;
      If($Type===Self::Brace_Close ) Return $this->Prev_Open($Item);
      If($Type===Self::Brace_Open  ) Return Null;
      Return $Item;
    }
  }
  
  Function Next_SkipBraces(TToken $Token):?TToken 
  {
    $Type=Self::Braces[$Token->Text][0]?? Self::Brace_None;
    If($Type===Self::Brace_Open)
    { 
      $Token=$this->Next_Close($Token);
      If($Token?->Text==='}') Return Null;
    }
    For($Item=$Token?->Next; $Item; $Item=$Item->Next)
    {
      If($Item->GetTypeHandler()!=='Text') Continue;
      If($Item->Is( //Ignore Ignorable
        T_COMMENT     , 
        T_DOC_COMMENT , 
        T_WHITESPACE  ,
        T_OPEN_TAG    , //TODO: Why?
      )) Continue;
      If($Item->Text==='{') Return $Item;
      If($Item->Text===';') Return Null;
      $Type=Self::Braces[$Item->Text][0]?? Self::Brace_None;
      If($Type===Self::Brace_Open  ) Return $Item;
      If($Type===Self::Brace_Close ) Return Null;
      Return $Item;
    }
    Return Null;
  }
  
  Function MakeBracesLine(TToken $Token):Array
  {
    $Res=[];
    For($Item=$Token; $Item; $Item=$this->Prev_SkipBraces($Item))
      $Res[]=$Item;
    $Res=Array_Reverse($Res);
    $Pos=Count($Res)-1;
  //For($Item=$Token; $Item; $Item=$this->Next_SkipBraces($Item))
  //For($Item=$Token->Next; $Item; $Item=$this->Next_SkipBraces($Item))
    For($Item=$this->Next_SkipBraces($Token->Next); $Item; $Item=$this->Next_SkipBraces($Item))
      $Res[]=$Item;
    Return [$Pos, $Res];
  }
  
  Static Function Detect_NS($Token, $Path, $i)
  {
    Return ['Code', '/'.$Path[$i+1]->Text];
  }
  
  Static Function Detect_Func($Token, $Path, $i)
  {
    If($Next=$Path[$i+1]?? Null)
    {
      If($Next->Id===T_STRING) Return ['Code', '::'.$Path[$i+1]->Text.'()'];
      If(In_Array($Next->text, ['(', '{'])) Return ['Code', '::fn()'];
    }
    //TODO: Error
  }
  
  Static Function Detect_Class($Token, $Path, $i)
  {
    Return ['Class', '/'.$Path[$i+1]->Text];
  }
  
  Static Function Detect_Const($Token, $Path, $i)
  {
    For($j=$i; $j<Count($Path); $j++)
      If($Path[$j]?->Text==='=')
      {
        Return [$Path[$j+1]?->Text==='['?'Array':Null, '::'.$Path[$j-1]->Text];
      }
    Log('Error', 'Unknown const: ', $Path);
    Return;
  //Return '::'.$Path[$i+1]->Text;
  }
  
  Static Function Detect_Var($Token, $Path, $i)
  {
    If($Path[$i+1]?->Text==='=')
      Return [$Path[$i+2]?->Text==='['?'Array':'Hook', '::'.$Token->Text];
    If($Path[$i+1]?->Text==='{')
      Return ['Hook', '::'.$Token->Text];
    Log('Error', 'Unknown Var: ', $Path);
  //Return ['Hook', '::'.$Token->Text];
  }
  
  Static Function Detect_GetSet($Token, $Path, $i)
  {
    Return ['Code', '::'.$Token->Text.'()'];
  }
  
  Static Function Detect_Key($Token, $Path, $i)
  {
    Return [Null, '['.$Path[$i-1]->Text.']'];
  }

  Static Function Detect_Array($Token, $Path, $i)
  {
    Return ['Array', 'Array'];
  }

  Const Detect_Map=[//Detect func               , State  , Need  //TODO: Remove State
    T_NAMESPACE    =>[Self::Detect_NS     (...) ,'Code'  ,'Code'  ],
    T_CLASS        =>[Self::Detect_Class  (...) ,'Class' ,'Code'  ],
    T_INTERFACE    =>[Self::Detect_Class  (...) ,'Class' ,'Code'  ],
    T_TRAIT        =>[Self::Detect_Class  (...) ,'Class' ,'Code'  ],
    T_FUNCTION     =>[Self::Detect_Func   (...) ,'Code'           ], //[Code, Class]
    T_FN           =>[Self::Detect_Func   (...) ,'Code'  ,'Code'  ],
    T_VARIABLE     =>[Self::Detect_Var    (...) ,'Hook'  ,'Class' ],
    T_CONST        =>[Self::Detect_Const  (...)                   ], //[Code, Class]
    'get'          =>[Self::Detect_GetSet (...) ,'Code'  ,'Hook'  ],
    'set'          =>[Self::Detect_GetSet (...) ,'Code'  ,'Hook'  ],
    T_DOUBLE_ARROW =>[Self::Detect_Key    (...) ,Null    ,'Array' ],
  //'['            =>[Self::Detect_Array  (...) ,'Array'          ],
  ];
  
  Var $Detect_Map=Self::Detect_Map; //TODO: Does not work
  
  Function Detect(String $State, Int $Pos, Array $Path)
  {
    Global $TokenNameById;
  //Static $Debug=Log('Debug', 'Detect_Map: ')->Debug(Self::Detect_Map)->Ret();
    $Res=[];
    $l=Count($Path);
    For($i=0; $i<$l; $i++)
    {
      $v=$Path[$i];
      If($Detected=Self::Detect_Map[$v->Id]?? Null)
      {
      //$NewState =$Detected[1]?? $State;
        $Need     =$Detected[2]?? Null;
        If($Need && $Need!==$State)
        {
          $Res[]='/?1:'.$v->Text.'('.($v->Line+1).','.($v->Pos+1).')';
          Continue;
        }
        $r=$Detected[0]($v, $Path, $i);
        If(!$r)
        {
          $Res[]='/?2:'.$v->Text.'('.($v->Line+1).','.($v->Pos+1).')';
          Continue;
        }
      //Return [$NewState, $r, True];
        Return [/*$NewState*/$r[0]?? $State, $r[1], True];
      }
      If($v->Id!==T_STRING)
      {
        $Res[]='/?3:'.$TokenNameById[$v->Id].':'.$v->Text.'('.($v->Line+1).','.($v->Pos+1).')';
        Continue;
      }
      $text=StrToLower($v->Text);
      If($Detected=Self::Detect_Map[$text]?? Null)
      {
      //$NewState =$Detected[1]?? $State;
        $Need     =$Detected[2]?? Null;
        If($Need && $Need!==$State)
        {
          $Res[]='/?4:'.$v->Text.'('.($v->Line+1).','.($v->Pos+1).')';
          Continue;
        }
        $r=$Detected[0]($v, $Path, $i);
        If(!$r)
        {
          $Res[]='/?5:'.$v->Text.'('.($v->Line+1).','.($v->Pos+1).')';
          Continue;
        }
        Return [/*$NewState*/$r[0]?? $State, $r[1], True];
      }
      $Res[]='/!:'.$v->Text.'('.($v->Line+1).','.($v->Pos+1).')';
    }
  //ForEach($Path As $v)
  //  $Res[]='/'.$v->Text.'('.($v->Line+1).','.($v->Pos+1).')';
    Return [$State, Implode($Res), False];
  }
  
  Function MakeBracesPath($Token)
  {
    $Res=[];
    For($Item=$Token; $Item; $Item=$this->Prev_Open($Item))
      $Res[]=$this->MakeBracesLine($Item);
    $Res=Array_Reverse($Res);
    $State='Code';
    ForEach($Res As $k=>[$Pos, $Path])
    {
      $OldState=$State;
      [$State, $Detected, $Ok]=$this->Detect($State, $Pos, $Path);
      If($Ok)
        $Res[$k]=$Detected;
      Else
      {
        $Pos1=$Path[0]->GetFilePos()->ToString();
        $Pos2=$Path[$Pos]->GetFilePos()->ToString();
        $Pos3=$Path[Count($Path)-1]->GetFilePos()->ToString();
        $Pos='';
        If($Pos1===$Pos2 && $Pos1===$Pos3)
          $Pos=$Pos1;
        ElseIf($Pos1===$Pos2)
          $Pos='!'.$Pos1.'-'.$Pos3;
        ElseIf($Pos3===$Pos2)
          $Pos=$Pos1.'-!'.$Pos3;
        Else
          $Pos=$Pos1.'-'.$Pos2.'-'.$Pos3;
        $Res[$k]='{'.$Pos.':'.$OldState.'->'.$State.':'.$Detected.'}';
      }
    }
    Return $Res;
  }
  
  Function Prev_Open($Token)
  {
  //Log('Debug', 'Prev_Open for ', $Token)->File($Token->GetFilePos()->ToArgs());
    For($Item=$Token->Prev; $Item; $Item=$Item->Prev)
    {
      If($Item->GetTypeHandler()!=='Text') Continue;
    //Log('Debug', 'Prev_Open_Next ', [$Item->Text])->File($Item->GetFilePos()->ToArgs());
      $Type=Self::Braces[$Item->Text][0]?? Self::Brace_None;
      If($Type===Self::Brace_Open  ) Return $Item;
      If($Type!==Self::Brace_Close ) Continue;
     
      $Next=$this->Prev_Open($Item);
      If(!$Next) Break;
      $Need=Self::Braces[$Item->Text][1];
      If($Need!==$Next->Text)
        Log('Error', 'Need brace ', $Need, ' ', $Item->GetFilePos(), ' actual is ', $Next->Text, ' ', $Next->GetFilePos());
      $Item=$Next;
    }
    Return Null;
  }

  Function Next_Close($Token)
  {
  //Log('Debug', 'Nect_Close for ', $Token)->File($Token->GetFilePos()->ToArgs());
    For($Item=$Token->Next; $Item; $Item=$Item->Next)
    {
      If($Item->GetTypeHandler()!=='Text') Continue;
    //Log('Debug', 'Nect_Close_Next ', [$Item->Text])->File($Item->GetFilePos()->ToArgs());
      $Type=Self::Braces[$Item->Text][0]?? Self::Brace_None;
      If($Type===Self::Brace_Close ) Return $Item;
      If($Type!==Self::Brace_Open  ) Continue;
     
      $Next=$this->Next_Close($Item);
      If(!$Next) Break;
      $Need=Self::Braces[$Item->Text][1];
      If($Need!==$Next->Text)
        Log('Error', 'Need brace ', $Need, ' ', $Item->GetFilePos(), ' actual is ', $Next->Text, ' ', $Next->GetFilePos());
      $Item=$Next;
    }
    Return Null;
  }
}