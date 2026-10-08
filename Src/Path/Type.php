<?
NameSpace Reformat\Path;

Use Function Reformat\Log;

Enum EType:String
{
  Case UnKnown   ='UnKnown'   ;
  Case NameSpace ='NameSpace' ;
  Case Function  ='Function'  ;
  Case Struct    ='Class'     ; // const Class is not allowed
  Case Var       ='Var'       ;
  Case Const     ='Const'     ;
  Case Key       ='Key'       ;
  Case Hook      ='Hook'      ;
  Case Error     ='Error'     ;
  
  Function Class():Self { Return Self::Struct; }
  
  Function IsUnKnown   ():Bool { Return $this===Self::UnKnown   ; }
  Function IsNameSpace ():Bool { Return $this===Self::NameSpace ; }
  Function IsFunction  ():Bool { Return $this===Self::Function  ; }
  Function IsClass     ():Bool { Return $this===Self::Struct    ; }
  Function IsVar       ():Bool { Return $this===Self::Var       ; }
  Function IsConst     ():Bool { Return $this===Self::Const     ; }
  Function IsKey       ():Bool { Return $this===Self::Key       ; }
  Function IsHook      ():Bool { Return $this===Self::Hook      ; }
  Function IsError     ():Bool { Return $this===Self::Error     ; }
  
  Function IsSignificant():Bool { Return $this!==Self::UnKnown && $this!==Self::Error; }

//****************************************************************
// Cast
  Protected Const Cast=[
    'UnKnown'   =>Self::UnKnown   ,
    'NameSpace' =>Self::NameSpace ,
    'Function'  =>Self::Function  ,
    'Class'     =>Self::Struct    ,
    'Var'       =>Self::Var       ,
    'Const'     =>Self::Const     ,
    'Key'       =>Self::Key       ,
    'Hook'      =>Self::Hook      ,
    'Error'     =>Self::Error     ,
  ];
  
  Static Function Cast(Self|String $v):Self { Return Is_String($v)? Self::Cast[$v]:$v; }
  
//****************************************************************
  Function ToString():String { Return $this->value; }
//****************************************************************
}
