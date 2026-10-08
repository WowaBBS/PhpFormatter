<?
NameSpace Reformat\Path;

Use Function Reformat\Log;

Enum EType:String
{
  Case UnKnown   ='UnKnown'   ;
  Case NameSpace ='NameSpace' ;
  Case Function  ='Function'  ;
  Case Struct    ='Class'     ;
  Case Var       ='Var'       ;
  Case Const     ='Const'     ;
  Case Key       ='Key'       ;
  Case Hook      ='Hook'      ;
  
  Const Cast=[
    'UnKnown'   =>Self::UnKnown   ,
    'NameSpace' =>Self::NameSpace ,
    'Function'  =>Self::Function  ,
    'Class'     =>Self::Struct    ,
    'Var'       =>Self::Var       ,
    'Const'     =>Self::Const     ,
    'Key'       =>Self::Key       ,
    'Hook'      =>Self::Hook      ,
  ];
  
  Function Class():Self { Return Self::Struct; }
  
  Function Cast(Self|String $v):Self { Return Is_String($v)? Self::Cast[$v]:$v; }
  
  Function ToString():String { Return $this->value; }
}
