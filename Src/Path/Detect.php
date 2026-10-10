<?
NameSpace Reformat\Path;

Use Reformat\Token\TBase As TToken;
Use Function Reformat\Log;

Class TDetect
{
  Var $State='Code';

  Function Line(TLine $Line)
  {
    $Res=New TItem(Line: $Line);
    $Err=[];
    ForEach($Line As $i=>$Token)
    {
      $Line->Offset=$i;
      $r=$this->_ById($Line, $i);
      $Line->Offset=0;
      If(Is_Int($r))
        $Err[$i]=$r;
      ElseIf($r)
        Return $Res->_Set($r[0], $r[1], $r[2]);
    }
    
    $Info=[];
    ForEach($Line As $i=>$Token)
      $Info[]=(Is_Int($r=$Errors[$i]?? Null)? '/?'.$r.':':'/!:')
        .$Token->Text.'('.($Token->Line+1).','.($Token->Pos+1).')';
    Return $Res->_UnKnown(Join($Info));
  }

  Function EndPoint($Token)
  {
    $Res=New TItem(Line: New TLine($Token, True));
    Switch($Token->Id)
    {
    Case T_DOC_COMMENT              : $Res->_Set('Doc'     , '//' ,'Doc'     ); Break;
    Case T_COMMENT                  : $Res->_Set('Comment' , '//' ,'Comment' ); Break;
    Case T_INLINE_HTML              : $Res->_Set('Data'    , '//' ,'Data'    ); Break;
    Case T_CONSTANT_ENCAPSED_STRING : $Res->_Set('String'  , '//' ,'String'  ); Break;
    Case T_ENCAPSED_AND_WHITESPACE  : $Res->_Set(...$this->_String($Token)); Break;
    Default                         : $Res->_Set('Comment' , '//', '#'.($GLOBALS['TokenNameById'][$Token->Id]?? $Token->Id));
    }
    Return $Res;
  }
  
  Function _String($Token)
  {
    For($Item=$Token; $Item; $Item=$Item->Prev)
    {
      If(!Valuable::Is($Item)) Continue;
      If($Item->Id===T_START_HEREDOC) Return ['HereDoc' , '//' ,'HereDoc' ];
      If($Item->Text==='"') Return ['String' , '//' ,'String' ];
    //If($Item->Text==="'") Return ['String' , '//' ,'String' ];
    }
    Log('Error', 'Unknown string: ',$Token)->Debug(New TLine($Token));
    Return ['String' , '//' ,'String' ];
  }
  
//****************************************************************
  Protected Const Map=[ //Type    , Required   State {     ,  [        (
    T_NAMESPACE    =>['NameSpace' ,['Code'       ],'Code'  , Null   , Null   ],
    T_CLASS        =>['Class'     ,['Code'       ],'Class' , Null   , Null   ],
    T_INTERFACE    =>['Class'     ,['Code'       ],'Class' , False  , False  ],
    T_TRAIT        =>['Class'     ,['Code'       ],'Class' , False  , False  ],
    T_ENUM         =>['Class'     ,['Code'       ],'Class' , False  , False  ],
    T_FUNCTION     =>['Function'  ,  Null         ,'Code'  , False  ,'Arg'   ], //[Code, Class]
    T_FN           =>['Function'  ,['Code'       ],'Code'  , False  ,'Arg'   ],
    T_VARIABLE     =>['Var'       ,['Class','Arg'],'Hook'  ,'Array' , Null   ], //
    T_CONST        =>['Const'     ,  Null         , Null   ,'Array' , Null   ], //[Code, Class]
    T_DOUBLE_ARROW =>['Key'       ,['Array'      ], Null   , Null   , Null   ],
    T_STRING       =>['Token'     ,  Null         , Null   , Null   , Null   ],
  ];

  Function _ById($Line):False|Int|Array
  {
    $Detected=Self::Map[$Line[0]->Id]?? Null;
    
    If(!$Detected) Return False;
    
    $Type     =$Detected[0];
    $Required =$Detected[1];
    $State    =$Detected[Match($Line->Token->Text){'{'=>2,'['=>3,'('=>4,Default=>5}]?? Null;
    If($Required && !In_Array($this->State, $Required, True)) Return 1;

    $Res=$this->_Match($Type, $Line);
    If(!$Res) Return 2;

    $this->State=$Res['State']?? $State?? $this->State;
    Return [$Res['Type']?? $Type?? 'Error', $Res[0], $Res[1]];
  }
  
//****************************************************************
  Function _Match($Type, $Line)
  {
    Return Match($Type) {
      'NameSpace'  =>$this->_NameSpace ($Line),
      'Class'      =>$this->_Class     ($Line),
      'Function'   =>$this->_Function  ($Line),
      'Var'        =>$this->_Var       ($Line),
      'Const'      =>$this->_Const     ($Line),
      'Key'        =>$this->_Key       ($Line),
      'Token'      =>$this->_Token     ($Line),
       Default     =>$this->_Error     ($Line, $Type),
    };
  }

  Function _NameSpace($Line)
  {
    If(IsSet($Line[1]))
      Return ['\\', $Line[1]->Text];
  }
  
  Function _Function($Line)
  {
    $Next=$Line[1]?? Null;
    If(!$Next) Return Log('Error', 'Wrong function name', $Next)->Ret();
    
    If($Next->Id===T_STRING)
      Return [
        $this->State==='Class'? '::':'\\', 
        $Line[1]->Text.'()'
      ];
    If(In_Array($Next->Text, ['(', '{']))
      Return ['=>', 'fn()'];
  }
  
  Function _Class($Line)
  {
    $Next=$Line[1]?? Null;
    If(!$Next) Return;
    
    If($Next->Id===T_STRING) Return ['\\', $Line[1]->Text];
    If(In_Array($Next->Text, ['(', '{']))
      Return ['\\', 'Class'];
  }
  
  Function _Const($Line)
  {
    For($i=0; $i<Count($Line); $i++)
      If($Line[$i]?->Text==='=')
        Return ['::', $Line[$i-1]->Text,
        //'State'=>$Line[$i+1]?->Text==='['?'Array':Null
        ];
    
    Log('Error', 'Unknown const: ', $Line);
  }
  
  Function _Var($Line)
  {
    Switch($Line[1]?->Text)
    {
    Case '=': //Assign
    Case '{': //ToHook
    Case ';': //Only class
    Case ',': //Only arg
    Case '' : //Function
      Return [$this->State==='Arg'? '@Arg:':'::', $Line[0]->Text];
    }
      
    Log('Error', 'Unknown Var: ', $Line);
  }
  
  Function _Key($Line)
  {
    If(IsSet($Line[-1]))
      Return ['', '['.$Line[-1]->Text.']'];
  }
  
  Function _Token($Line)
  {
    If($this->State==='Hook')
      Return $this->_Hook($Line);
  }
  
  Function _Hook($Line)
  {
    Return ['::', $Line[0]->Text.'()', 'State'=>'Code', 'Type'=>'Hook'];
  }
  
  Function _Error($Line, $Type)
  {
    Log('Error', 'Wrong token type: ', $Type);
  }
  
//****************************************************************
}