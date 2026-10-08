<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

Class Detect
{
  Const Map=[//             Detect func     ,Type        , State  , Need  
    T_NAMESPACE    =>[Self::NameSpace (...) ,'NameSpace' ,'Code'  ,'Code'  ],
    T_CLASS        =>[Self::Class     (...) ,'Class'     ,'Class' ,'Code'  ],
    T_INTERFACE    =>[Self::Class     (...) ,'Class'     ,'Class' ,'Code'  ],
    T_TRAIT        =>[Self::Class     (...) ,'Class'     ,'Class' ,'Code'  ],
    T_ENUM         =>[Self::Class     (...) ,'Class'     ,'Class' ,'Code'  ],
    T_FUNCTION     =>[Self::Function  (...) ,'Function'  ,'Code'           ], //[Code, Class]
    T_FN           =>[Self::Function  (...) ,'Function'  ,'Code'  ,'Code'  ],
    T_VARIABLE     =>[Self::Var       (...) ,'Var'       ,'Hook'  ,'Class' ],
    T_CONST        =>[Self::Const     (...) ,'Const'                       ], //[Code, Class]
    T_DOUBLE_ARROW =>[Self::Key       (...) ,'Key'       , Null   ,'Array' ],
  ];

  Static Function NameSpace($State, $Line, $i)
  {
    Return ['/', $Line[$i+1]->Text];
  }
  
  Static Function Function($State, $Line, $i)
  {
    If($Next=$Line[$i+1]?? Null)
    {
      If($Next->Id===T_STRING)
        Return [
          $State==='Class'? '::':'\\', 
          $Line[$i+1]->Text.'()'
        ];
      If(In_Array($Next->Text, ['(', '{']))
        Return ['=>', 'fn()'];
    }
    
    Log('Error', 'Wrong function name', $Next);
  }
  
  Static Function Class($State, $Line, $i)
  {
    If($Next=$Line[$i+1]?? Null)
    {
      If($Next->Id===T_STRING)
        Return ['/', $Line[$i+1]->Text];
      If(In_Array($Next->Text, ['(', '{']))
        Return ['/', 'Class'];
    }
  }
  
  Static Function Const($State, $Line, $i)
  {
    For($j=$i; $j<Count($Line); $j++)
      If($Line[$j]?->Text==='=')
        Return ['::', $Line[$j-1]->Text,
          'State'=>$Line[$j+1]?->Text==='['?'Array':Null
        ];
    
    Log('Error', 'Unknown const: ', $Line);
    Return;
  }
  
  Static Function Var($State, $Line, $i)
  {
    If($Line[$i+1]?->Text==='=')
      Return ['::', $Line[$i]->Text,
        'State'=>$Line[$i+2]?->Text==='['?'Array':'Hook',
      ];
    If($Line[$i+1]?->Text==='{')
      Return ['::', $Line[$i]->Text];
    Log('Error', 'Unknown Var: ', $Line);
  //Return ['Hook', '::'.$Line[$i]->Text];
  }
  
  Static Function Key($State, $Line, $i)
  {
    Return ['', '['.$Line[$i-1]->Text.']'];
  }
  
//****************************************************************
  Static Function Hook($State, $Line, $i)
  {
    Return ['::', $Line[$i]->Text.'()', 'State'=>'Code'];
  }

  Static Function ById($State, $Line, $i):False|Int|Array
  {
    $Detected=Self::Map[$Line[$i]->Id]?? Null;
    
    If(!$Detected) Return False;
    
    $Type     =$Detected[1];
    $NewState =$Detected[2]?? $State;
    $Need     =$Detected[3]?? Null;
    If($Need && $Need!==$State) Return 1;

    $r=$Detected[0]($State, $Line, $i);
    If(!$r) Return 2;

    $State=$r['State']?? $NewState;
    Return [$State, $Type, $r[0], $r[1]];
  }

  Static Function Line(String $State, TLine $Line)
  {
    $Res=[];
    ForEach($Line As $i=>$Token)
    {
      $r=Self::ById($State, $Line, $i);
      If(Is_Int($r))
        $r=$r;
      ElseIf($r)
        Return [$r[0], $r[1].' '.$r[2].$r[3], True];
      ElseIf($Token->Id!==T_STRING)
        $r=3;
      ElseIf($State==='Hook')
      {
        $r=Self::Hook($State, $Line, $i);
        If($r)
          Return [$r['State']?? $State, 'Hook'.' '.$r[0].$r[1], True];
        $r=4;
      }
      $Res[]=Is_Int($r)? '/?'.$r.':':'/!:'.$Token->Text.'('.($Token->Line+1).','.($Token->Pos+1).')';
    }
    Return [$State, Implode($Res), False];
  }
  
}