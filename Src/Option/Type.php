<?
NameSpace Reformat\Option;

Use Function Reformat\Log;

Enum EType Implements \WLib\Debug\ICustom
{
  Case Void   ;
  Case Null   ;
  Case Bool   ;
  Case Int    ;
  Case Float  ;
  Case String ;
  Case List   ;
  Case Map    ;
  Case Error  ; //Used only for detect
  
  Const Default=Self::Void;

  Protected Const TypeInfo = [      //Type 0      , Kind 1   ,Default 2  ,FastCast 3
    Self::Void   ->name =>[Self::Void   ,'Void'    ,Null       ,Self::_Cast_Null   (...)],
    Self::Null   ->name =>[Self::Null   ,'Void'    ,Null       ,Self::_Cast_Null   (...)],
    Self::Bool   ->name =>[Self::Bool   ,'Bool'    ,False      ,Self::_Cast_Bool   (...)],
    Self::Int    ->name =>[Self::Int    ,'Numeric' ,0          ,Self::_Cast_Int    (...)],
    Self::Float  ->name =>[Self::Float  ,'Numeric' ,0.         ,Self::_Cast_Float  (...)],
    Self::String ->name =>[Self::String ,'String'  ,''         ,Self::_Cast_String (...)],
    Self::List   ->name =>[Self::List   ,'Array'   ,[]         ,Self::_Cast_List   (...)],
    Self::Map    ->name =>[Self::Map    ,'Array'   ,[]         ,Self::_Cast_Array  (...)],
    Self::Error  ->name =>[Self::Error  ,'Void'    ,Self::Error,Self::_Cast_Null   (...)],
  ];
  
  Function IsVoid   () { Return $this===Self::Void   ; }
  Function IsNull   () { Return $this===Self::Null   ; }
  Function IsBool   () { Return $this===Self::Bool   ; }
  Function IsInt    () { Return $this===Self::Int    ; }
  Function IsFloat  () { Return $this===Self::Float  ; }
  Function IsString () { Return $this===Self::String ; }
  Function IsList   () { Return $this===Self::List   ; }
  Function IsMap    () { Return $this===Self::Map    ; }
  Function IsError  () { Return $this===Self::Error  ; }
  
  Function IsNumeric () { Return $this->IsInt  () || $this->IsFloat (); }
  Function IsArray   () { Return $this->IsList () || $this->IsMap   (); }
  Function IsEmpty   () { Return $this->IsVoid () || $this->IsNull  () || $this->IsError(); }
  Function HasValue  () { Return !$this->IsVoid (); }

  Function Is(Self $Type) { Return $this===$Type; }
//Function CanSet(Self $Type) { Return $this===Self::Void && $this===$Type; }
  
  Function GetDefaultValue() { Return Self::TypeInfo[$this->name][2]; }
  
//****************************************************************
// Cast

  Const TypeDetect=[
    'NULL'    =>Self::Null   ,
    'integer' =>Self::Int    ,
    'boolean' =>Self::Bool   ,
    'double'  =>Self::Float  ,
    'string'  =>Self::String ,
    'array'   =>Self::Map    ,
    'object'  =>Self::Error  , //TODO: Iterable and __ToString()
    //TODO: Another types
  ];
  
  Static Function Detect($Value)
  {
    $Type=GetType($Value);
    $Res=Self::TypeDetect[$Type]?? Self::Error;
    If($Res===Self::Map && Array_Is_List($Value))
      $Res=Self::List;
    Return $Res;
  }
  
  Function GetKind() { Return Self::TypeInfo[$this->name][1]; }
  
  Function IsCompatible(Self $Type)
  {
    Return $this->GetKind()==$Type->GetKind();
  }
  
  Function CanCast($Value)
  {
    $Detected=Self::Detect($Value);
    If(!$this->IsCompatible($Detected)) Return False;
    Return $this->_CanCast($Value);
  }
  
  Function _CanCast($Value)
  {
    $Casted=$this->FastCast($Value);
    $Detected=Self::Detect($Value);
    $Restored=$Detected->FastCast($Casted);
    Return $Value===$Restored;
  }
  
  Static Function _Cast_Null   ($v) { Return Null; }
  Static Function _Cast_Bool   ($v) { Return @(Bool   )$v; }
  Static Function _Cast_Int    ($v) { Return @(Int    )$v; }
  Static Function _Cast_Float  ($v) { Return @(Float  )$v; }
  Static Function _Cast_String ($v) { Return @(String )$v; }
  Static Function _Cast_Array  ($v) { Return @(Array  )$v; }
  Static Function _Cast_List   ($v) { Return Array_Values(@(Array  )$v); }
  
  Function FastCast($Value) { Return Self::TypeInfo[$this->name][3]($Value); }
  
//****************************************************************

  Const CheckTypes=[
    'Null'    =>[Self::Null               ],
    
    'Bool'    =>[Self::Bool               ],
    'Int'     =>[Self::Int                ],
    'Float'   =>[Self::Float  ,Self::Int  ],
    'String'  =>[Self::String             ],
    'List'    =>[Self::List   ,Self::Map  ],
    'Map'     =>[Self::Map    ,Self::List ],
    
    '?Bool'   =>[Self::Bool               ,Self::Null],
    '?Int'    =>[Self::Int                ,Self::Null],
    '?Float'  =>[Self::Float  ,Self::Int  ,Self::Null],
    '?String' =>[Self::String             ,Self::Null],
    '?List'   =>[Self::List   ,Self::Map  ,Self::Null],
    '?Map'    =>[Self::Map    ,Self::List ,Self::Null],
  ];

  Function IsType(String $Type)
  {
    $Types=Self::CheckTypes[$Type]?? Log('Error', 'Type ', $Type, ' is not found')->Ret([]);
    Return In_Array($this, $Types, True)? $Types[0]:False;
  }

//****************************************************************
// Debug

  //WLib\Debug\ICustom
  Function Debug_Write(\WLib\Log\CFormat $To)
  { //TODO: Workaround
    $To->Write($this->name);
  }

//****************************************************************
}
