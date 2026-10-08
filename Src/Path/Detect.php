<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

Class Detect
{
  Const Map=[//             Detect func     , State  , Need  //TODO: Remove State
    T_NAMESPACE    =>[Self::NameSpace (...) ,'Code'  ,'Code'  ],
    T_CLASS        =>[Self::Class     (...) ,'Class' ,'Code'  ],
    T_INTERFACE    =>[Self::Class     (...) ,'Class' ,'Code'  ],
    T_TRAIT        =>[Self::Class     (...) ,'Class' ,'Code'  ],
    T_ENUM         =>[Self::Class     (...) ,'Class' ,'Code'  ],
    T_FUNCTION     =>[Self::Function  (...) ,'Code'           ], //[Code, Class]
    T_FN           =>[Self::Function  (...) ,'Code'  ,'Code'  ],
    T_VARIABLE     =>[Self::Var       (...) ,'Hook'  ,'Class' ],
    T_CONST        =>[Self::Const     (...)                   ], //[Code, Class]
    T_DOUBLE_ARROW =>[Self::Key       (...) , Null   ,'Array' ],
  ];

  Static Function NameSpace($State, $Pos, $Path, $i, $Token)
  {
    Return ['Code', 'NameSpace', '/', $Path[$i+1]->Text];
  }
  
  Static Function Function($State, $Pos, $Path, $i, $Token)
  {
    If($Next=$Path[$i+1]?? Null)
    {
      If($Next->Id===T_STRING)
        Return ['Code', 'Function', 
          $State==='Class'? '::':'\\', 
          $Path[$i+1]->Text.'()'
        ];
      If(In_Array($Next->Text, ['(', '{']))
        Return ['Code', 'Function', '=>', 'fn()'];
    }
    Log('Error', 'Wrong function name', $Next);
  }
  
  Static Function Class($State, $Pos, $Path, $i, $Token)
  {
    If($Next=$Path[$i+1]?? Null)
    {
      If($Next->Id===T_STRING)
        Return ['Class', 'Class', '/', $Path[$i+1]->Text];
      If(In_Array($Next->Text, ['(', '{']))
        Return ['Class', 'Class', '/', 'Class'];
    }
  }
  
  Static Function Const($State, $Pos, $Path, $i, $Token)
  {
    For($j=$i; $j<Count($Path); $j++)
      If($Path[$j]?->Text==='=')
      {
        Return [$Path[$j+1]?->Text==='['?'Array':Null, 'Const', '::', $Path[$j-1]->Text];
      }
    Log('Error', 'Unknown const: ', $Path);
    Return;
  }
  
  Static Function Var($State, $Pos, $Path, $i, $Token)
  {
    If($Path[$i+1]?->Text==='=')
      Return [$Path[$i+2]?->Text==='['?'Array':'Hook', 'Var', '::', $Token->Text];
    If($Path[$i+1]?->Text==='{')
      Return ['Hook', 'Var', '::', $Token->Text];
    Log('Error', 'Unknown Var: ', $Path);
  //Return ['Hook', '::'.$Token->Text];
  }
  
  Static Function Key($State, $Pos, $Path, $i, $Token)
  {
    Return [Null, 'Key', '', '['.$Path[$i-1]->Text.']'];
  }
  
//****************************************************************
  Static Function Hook($State, $Pos, $Path, $i, $Token)
  {
    Return ['Code', 'Hook', '::', $Token->Text.'()'];
  }

  Static Function ById($State, $Pos, $Path, $i, $Token):False|Int|Array
  {
    $Detected=Self::Map[$Token->Id]?? Null;
    
    If(!$Detected) Return False;
    
  //$NewState =$Detected[1]?? $State;
    $Need     =$Detected[2]?? Null;
    If($Need && $Need!==$State) Return 1;

    $r=$Detected[0]($State, $Pos, $Path, $i, $Token);
    If(!$r) Return 2;

    $r[0]??=$State; /*$NewState?? */
    Return $r;
  }

  Static Function Line(String $State, Int $Pos, Array $Path)
  {
    Global $TokenNameById;
    If(Count($Path)===1 && $Path[0]->Id===T_COMMENT) Return;
  //Static $Debug=Log('Debug', 'Detect::Map: ')->Debug(Self::Map)->Ret();
    $Res=[];
    $l=Count($Path);
    For($i=0; $i<$l; $i++)
    {
      $Token=$Path[$i];
      $r=Self::ById($State, $Pos, $Path, $i, $Token);
      If(Is_Int($r))
        $r=$r;
      ElseIf($r)
        Return [$r[0], $r[1].' '.$r[2].$r[3], True];
      ElseIf($Token->Id!==T_STRING)
        $r=3;
      ElseIf($State==='Hook')
      {
        $r=Self::Hook($State, $Pos, $Path, $i, $Token);
        If($r)
          Return [/*$NewState*/$r[0]?? $State, $r[1].' '.$r[2].$r[3], True];
        $r=4;
      }
      $Res[]=Is_Int($r)? '/?'.$r.':':'/!:'.$Token->Text.'('.($Token->Line+1).','.($Token->Pos+1).')';
    }
    Return [$State, Implode($Res), False];
  }
  
}