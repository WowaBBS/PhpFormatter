<?
NameSpace Reformat\Option;

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
    //TODO: Another
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
  
  Function CanCast($Value)
  {
    $Casted=$this->Cast($Value);
    $Restored=Self::Detect($Value)->Cast($Casted);
    Return $Value===$Restored;
  }
  
  Function Cast($Value)
  {
    Return Match($this) {
      Self::Void    => Null ,
      Self::Null    => Null ,
      Self::Bool    => Self::Cast_Bool   ($Value ),
      Self::Int     => Self::Cast_Int    ($Value ),
      Self::Float   => Self::Cast_Float  ($Value ),
      Self::String  => Self::Cast_String ($Value ),
      Self::List    => Self::Cast_List   ($Value ),
      Self::Map     => Self::Cast_Map    ($Value ),
      Self::Error   => Null ,
    };
  }
  
  Static Function Cast_Bool   ($Value ) { Return @(Bool   )$Value; }
  Static Function Cast_Int    ($Value ) { Return Cast_Int    ($Value ); }
  Static Function Cast_Float  ($Value ) { Return Cast_Float  ($Value ); }
  Static Function Cast_String ($Value ) { Return Cast_String ($Value ); }
  Static Function Cast_List   ($Value )
  {
    $Value=Cast_Array($Value );
    Return Array_Is_List($Value)? $Value: Array_Values($Value);
  }
  
  Static Function Cast_Map    ($Value )
  { 
    $Value=Cast_Array($Value );
    Return $Value; 
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
