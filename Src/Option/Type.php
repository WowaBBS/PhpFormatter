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
  
  Const Default=Self::Void;
  
  Function IsVoid   () { Return $this===Self::Void   ; }
  Function IsNull   () { Return $this===Self::Null   ; }
  Function IsBool   () { Return $this===Self::Bool   ; }
  Function IsInt    () { Return $this===Self::Int    ; }
  Function IsFloat  () { Return $this===Self::Float  ; }
  Function IsString () { Return $this===Self::String ; }
  Function IsList   () { Return $this===Self::List   ; }
  Function IsMap    () { Return $this===Self::Map    ; }

  Function Is(Self $Type) { Return $this===$Type; }
  Function CanSet(Self $Type) { Return $this===Self::Void && $this===$Type; }
  
  Function CanSetNull   () { Return $this===Self::Void && $this===Self::Null   ; }
  Function CanSetBool   () { Return $this===Self::Void && $this===Self::Bool   ; }
  Function CanSetInt    () { Return $this===Self::Void && $this===Self::Int    ; }
  Function CanSetFloat  () { Return $this===Self::Void && $this===Self::Float  ; }
  Function CanSetString () { Return $this===Self::Void && $this===Self::String ; }
  Function CanSetList   () { Return $this===Self::Void && $this===Self::List   ; }
  Function CanSetMap    () { Return $this===Self::Void && $this===Self::Map    ; }
  
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
