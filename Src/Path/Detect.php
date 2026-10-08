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

  Static Function NameSpace($State, $Line, $i, $Token)
  {
    Return ['Code', 'NameSpace', '/', $Line[$i+1]->Text];
  }
  
  Static Function Function($State, $Line, $i, $Token)
  {
    If($Next=$Line[$i+1]?? Null)
    {
      If($Next->Id===T_STRING)
        Return ['Code', 'Function', 
          $State==='Class'? '::':'\\', 
          $Line[$i+1]->Text.'()'
        ];
      If(In_Array($Next->Text, ['(', '{']))
        Return ['Code', 'Function', '=>', 'fn()'];
    }
    Log('Error', 'Wrong function name', $Next);
  }
  
  Static Function Class($State, $Line, $i, $Token)
  {
    If($Next=$Line[$i+1]?? Null)
    {
      If($Next->Id===T_STRING)
        Return ['Class', 'Class', '/', $Line[$i+1]->Text];
      If(In_Array($Next->Text, ['(', '{']))
        Return ['Class', 'Class', '/', 'Class'];
    }
  }
  
  Static Function Const($State, $Line, $i, $Token)
  {
    For($j=$i; $j<Count($Line); $j++)
      If($Line[$j]?->Text==='=')
      {
        Return [$Line[$j+1]?->Text==='['?'Array':Null, 'Const', '::', $Line[$j-1]->Text];
      }
    Log('Error', 'Unknown const: ', $Line);
    Return;
  }
  
  Static Function Var($State, $Line, $i, $Token)
  {
    If($Line[$i+1]?->Text==='=')
      Return [$Line[$i+2]?->Text==='['?'Array':'Hook', 'Var', '::', $Token->Text];
    If($Line[$i+1]?->Text==='{')
      Return ['Hook', 'Var', '::', $Token->Text];
    Log('Error', 'Unknown Var: ', $Line);
  //Return ['Hook', '::'.$Token->Text];
  }
  
  Static Function Key($State, $Line, $i, $Token)
  {
    Return [Null, 'Key', '', '['.$Line[$i-1]->Text.']'];
  }
  
//****************************************************************
  Static Function Hook($State, $Line, $i, $Token)
  {
    Return ['Code', 'Hook', '::', $Token->Text.'()'];
  }

  Static Function ById($State, $Line, $i, $Token):False|Int|Array
  {
    $Detected=Self::Map[$Token->Id]?? Null;
    
    If(!$Detected) Return False;
    
  //$NewState =$Detected[1]?? $State;
    $Need     =$Detected[2]?? Null;
    If($Need && $Need!==$State) Return 1;

    $r=$Detected[0]($State, $Line, $i, $Token);
    If(!$r) Return 2;

    $r[0]??=$State; /*$NewState?? */
    Return $r;
  }

  Static Function Line(String $State, TLine $Line)
  {
    $Res=[];
    ForEach($Line As $i=>$Token)
    {
      $r=Self::ById($State, $Line, $i, $Token);
      If(Is_Int($r))
        $r=$r;
      ElseIf($r)
        Return [$r[0], $r[1].' '.$r[2].$r[3], True];
      ElseIf($Token->Id!==T_STRING)
        $r=3;
      ElseIf($State==='Hook')
      {
        $r=Self::Hook($State, $Line, $i, $Token);
        If($r)
          Return [/*$NewState*/$r[0]?? $State, $r[1].' '.$r[2].$r[3], True];
        $r=4;
      }
      $Res[]=Is_Int($r)? '/?'.$r.':':'/!:'.$Token->Text.'('.($Token->Line+1).','.($Token->Pos+1).')';
    }
    Return [$State, Implode($Res), False];
  }
  
}