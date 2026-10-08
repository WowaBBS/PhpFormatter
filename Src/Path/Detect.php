<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

Class TDetect
{
  Var $State='Code';

  Protected Const Map=[ //Type    , State  , Need  
    T_NAMESPACE    =>['NameSpace' ,'Code'  ,'Code'  ],
    T_CLASS        =>['Class'     ,'Class' ,'Code'  ],
    T_INTERFACE    =>['Class'     ,'Class' ,'Code'  ],
    T_TRAIT        =>['Class'     ,'Class' ,'Code'  ],
    T_ENUM         =>['Class'     ,'Class' ,'Code'  ],
    T_FUNCTION     =>['Function'  ,'Code'  , Null   ], //[Code, Class]
    T_FN           =>['Function'  ,'Code'  ,'Code'  ],
    T_VARIABLE     =>['Var'       ,'Hook'  ,'Class' ],
    T_CONST        =>['Const'     , Null   , Null   ], //[Code, Class]
    T_DOUBLE_ARROW =>['Key'       , Null   ,'Array' ],
    T_STRING       =>['Token'     , Null   , Null   ],
  ];
  
//****************************************************************
  Function _Match($Type, $Line, $i)
  {
    Return Match($Type) {
      'NameSpace'  =>$this->_NameSpace ($Line, $i),
      'Class'      =>$this->_Class     ($Line, $i),
      'Function'   =>$this->_Function  ($Line, $i),
      'Var'        =>$this->_Var       ($Line, $i),
      'Const'      =>$this->_Const     ($Line, $i),
      'Key'        =>$this->_Key       ($Line, $i),
      'Token'      =>$this->_Token     ($Line, $i),
       Default     =>$this->_Error     ($Line, $i, $Type),
    };
  }

  Function _NameSpace($Line, $i)
  {
    Return ['/', $Line[$i+1]->Text];
  }
  
  Function _Function($Line, $i)
  {
    $Next=$Line[$i+1]?? Null;
    If(!$Next) Return Log('Error', 'Wrong function name', $Next)->Ret();
    
    If($Next->Id===T_STRING)
      Return [
        $this->State==='Class'? '::':'\\', 
        $Line[$i+1]->Text.'()'
      ];
    If(In_Array($Next->Text, ['(', '{']))
      Return ['=>', 'fn()'];
  }
  
  Function _Class($Line, $i)
  {
    $Next=$Line[$i+1]?? Null;
    If(!$Next) Return;
    
    If($Next->Id===T_STRING) Return ['/', $Line[$i+1]->Text];
    If(In_Array($Next->Text, ['(', '{']))
      Return ['/', 'Class'];
  }
  
  Function _Const($Line, $i)
  {
    For($j=$i; $j<Count($Line); $j++)
      If($Line[$j]?->Text==='=')
        Return ['::', $Line[$j-1]->Text,
          'State'=>$Line[$j+1]?->Text==='['?'Array':Null
        ];
    
    Log('Error', 'Unknown const: ', $Line);
  }
  
  Function _Var($Line, $i)
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
  
  Function _Key($Line, $i)
  {
    Return ['', '['.$Line[$i-1]->Text.']'];
  }
  
  Function _Token($Line, $i)
  {
    If($this->State==='Hook')
      Return $this->_Hook($Line, $i);
  }
  
  Function _Hook($Line, $i)
  {
    Return ['::', $Line[$i]->Text.'()', 'State'=>'Code', 'Type'=>'Hook'];
  }
  
  Function _Error     ($Line, $i, $Type)
  {
    Log('Error', 'Wrong token type: ', $Type);
  }
  
//****************************************************************

  Function _ById($Line, $i):False|Int|Array
  {
    $Detected=Self::Map[$Line[$i]->Id]?? Null;
    
    If(!$Detected) Return False;
    
    $Type  =$Detected[0];
    $State =$Detected[1];
    $Need  =$Detected[2];
    If($Need && $Need!==$this->State) Return 1;

    $Res=$this->_Match($Type, $Line, $i);
    If(!$Res) Return 2;

    $this->State=$Res['State']?? $State?? $this->State;
    Return [$Res['Type']?? $Type?? 'Error', $Res[0], $Res[1]];
  }

  Function Line(TLine $Line)
  {
    $Err=[];
    ForEach($Line As $i=>$Token)
    {
      $r=$this->_ById($Line, $i);
      If(Is_Int($r))
        $Err[$i]=$r;
      ElseIf($r)
        Return [$r[0].' '.$r[1].$r[2], True];
    }
    
    $Info=[];
    ForEach($Line As $i=>$Token)
      $Info[]=(Is_Int($r=$Errors[$i]?? Null)? '/?'.$r.':':'/!:')
        .$Token->Text.'('.($Token->Line+1).','.($Token->Pos+1).')';
    Return [Implode($Info), False];
  }
  
}