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
  Case Error  ;
  
  Const Default=Self::Void;
  
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

  Function Is(Self $Type) { Return $this===$Type; }
//Function CanSet(Self $Type) { Return $this===Self::Void && $this===$Type; }
  
  Function GetDefaultValue()
  {
    Return Match($this) {
      Self::Void    => Null  ,
      Self::Null    => Null  ,
      Self::Bool    => False ,
      Self::Int     => 0     ,
      Self::Float   => 0.    ,
      Self::String  => ''    ,
      Self::List    => []    ,
      Self::Map     => []    ,
      Self::Error   => Self::Error,
    };
  }
  
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
    $Res=Self::$TypeDetect[$Type]?? Self::Error;
    Switch($Res)
    {
    Case Self::Map:
      If(Array_Is_List($Value))
        $Res=Self::List;
      Break;
    }
    Return $Res;
  }
  
  Const KindByType=[
    Self::Void   ->name => 'Void'    ,
    Self::Null   ->name => 'Void'    ,
    Self::Bool   ->name => 'Bool'    ,
    Self::Int    ->name => 'Numeric' ,
    Self::Float  ->name => 'Numeric' ,
    Self::String ->name => 'String'  ,
    Self::List   ->name => 'Array'   ,
    Self::Map    ->name => 'Array'   ,
    Self::Error  ->name => 'Void'    ,
  ];
  
  Function GetKind() { Return Self::KindByType[$this->name]; }
  
  Function IsCompatible(Self $Type)
  {
    Return $this->GetKind()==$Type->GetKind();
  }
  
  Function CanCast($Value)
  {
    $Detected=Self::Detect($Value);
    If(!$this->IsCompatible($Detected)) Return False;
    Return $this->CanCastFull($Value);
  }
  
  Function _CanCast($Value)
  {
    $Casted=$this->FastCast($Value);
    $Detected=Self::Detect($Value);
    $Restored=$Detected->FastCast($Casted);
    Return $Value===$Restored;
  }
  
  Function FastCast($Value)
  {
    Return Match($this) {
      Self::Void    => Null ,
      Self::Null    => Null ,
      Self::Bool    => @(Bool   )($Value),
      Self::Int     => @(Int    )($Value),
      Self::Float   => @(Float  )($Value),
      Self::String  => @(String )($Value),
      Self::List    => Array_Values(@(Array)($Value)),
      Self::Map     => @(Array  )($Value),
      Self::Error   => Null ,
    };
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
