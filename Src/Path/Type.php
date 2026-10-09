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
  // EndPoint:
  Case Comment   ='Comment'   ; //Comment
  Case Doc       ='Doc'       ; //Comment
  Case Code      ='Code'      ; //Comment
  Case Data      ='Data'      ;
  Case String    ='String'    ;
  Case HereDoc   ='HereDoc'   ;
  
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
  Function IsComment   ():Bool { Return $this===Self::Comment   ; } //Comment
  Function IsDoc       ():Bool { Return $this===Self::Doc       ; } //Comment
  Function IsCode      ():Bool { Return $this===Self::Code      ; } //Comment
  Function IsData      ():Bool { Return $this===Self::Data      ; }         
  Function IsString    ():Bool { Return $this===Self::String    ; }
  Function IsHereDoc   ():Bool { Return $this===Self::HereDoc   ; }
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
    'Comment'   =>Self::Comment   , //Comment
    'Doc'       =>Self::Doc       , //Comment
    'Code'      =>Self::Code      , //Comment
    'Data'      =>Self::Data      ,
    'String'    =>Self::String    ,
    'HereDoc'   =>Self::HereDoc   ,
    'Error'     =>Self::Error     ,
  ];
  
  Static Function Cast(Self|String $v):Self { Return Is_String($v)? Self::Cast[$v]:$v; }
  
//****************************************************************
  Function ToString():String { Return $this->value; }
//****************************************************************
}
