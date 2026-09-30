<?
NameSpace Reformat\Option;

Use Reformat\FilePos\TInfo     As TFilePos;
Use Reformat\FilePos\IProvider As IFilePos;
Use Function Reformat\Log;

Class TValue Implements IFilePos, \ArrayAccess, \Countable, \IteratorAggregate, \WLib\Debug\ICustom
{
  Var ?TValue $Key    =Null;
  Var         $Value  ;
  Var  EType  $Type   =EType::Default;
  Var         $Parsed =False;
  
  Function ToValue():Mixed
  {
    $Res=$this->Value;
    If(Is_Array($Res))
      ForEach($Res As $k=>$v)
        $Res[$k]=$v->ToValue();
    
    Return $Res;
  }
  
//****************************************************************
// Token info
  
  Var $Tokens=[];
  
  Function SetToken($v) { If($v) $this->Tokens  =[$v]; }
  Function AddToken($v) { If($v) $this->Tokens[]= $v ; }

  Function GetFirstToken () { Return Array_First ($this->Tokens); }
  Function GetLastToken  () { Return Array_Last  ($this->Tokens); }
  
  Function GetFilePos    ():TFilePos { Return $this->GetFirstToken ()?->GetFilePos()?? TFilePos::GetEmpty(); }
  Function GetFilePosEnd ():TFilePos { Return $this->GetLastToken  ()?->GetFilePos()?? TFilePos::GetEmpty(); }
  
//****************************************************************
// Parser interface
  
  Function Parser_SetType(EType $Type, $Token, $Value)
  {
    If($this->Type->Is($Type) && $Type->IsMap())
    {
      $this->AddToken($Token);
      Return;
    }
    
    If(!$this->Type->IsVoid())
    {
      $this->Warning($Type, '=',$Value,' has already exist: ', 
        $this->ToDebug(), ' was setted in ', $this->GetFilePos())
        ->File($Token->GetFilePos()->ToArgs());
      //TODO: Error
    }
    
    $this->Type    =$Type;
    $this->Value   =$Type->GetDefaultValue();
    $this->SetToken($Token);
  }
  
  Function Parser_SetValue($v, $Token)
  {
    Switch(GetType($v))
    {
    Case 'boolean' : $this->Parser_SetType(EType::Bool   ,$Token, $v); Break;
    Case 'integer' : $this->Parser_SetType(EType::Int    ,$Token, $v); Break;
    Case 'double'  : $this->Parser_SetType(EType::Float  ,$Token, $v); Break;
    Case 'string'  : $this->Parser_SetType(EType::String ,$Token, $v); Break;
    Case 'NULL'    : $this->Parser_SetType(EType::Null   ,$Token, $v); Break;
    Default: //Global log
      Log('Error', 'Unknown value ', $v)->BackTrace()->File($Token->GetFilePos()->ToArgs());
      Return;
    }
    $this->Value=$v; 
    $this->SetToken($Token); //TODO: Remove?
  }
  
  Function Parser_MakeKey(TValue $Key)
  {
    $Token=$Key->GetFirstToken();
    $this->Parser_MakeMap($Token);
    $Res=&$this->Value[$this->_Key($Key->Value)];
    $Res??=$this->Parser_NewValue();
    $Res->AddToken($Token);
    $Res->Key=$Key;
    Return $Res;
  }
  
  Function Parser_MakeMap  ($Token) { $this->Parser_SetType(EType::Map  ,$Token, 'Map'  ); }
  Function Parser_MakeList ($Token) { $this->Parser_SetType(EType::List ,$Token, 'List' ); }
  
  Function Parser_KeyMap  (TValue $Key, $Token) { $Res=$this->Parser_MakeKey($Key); $Res->Parser_MakeMap  ($Token); Return $Res; }
  Function Parser_KeyList (TValue $Key, $Token) { $Res=$this->Parser_MakeKey($Key); $Res->Parser_MakeList ($Token); Return $Res; }
  
  Function Parser_AddItem($Value=Null)
  {
    $Value??=$this->Parser_NewValue();
    If(!$Value->Key)
    {
      $Value->Key=$Value->Parser_NewValue();
      $Value->Key->Parser_SetValue(Count($this->Value), $Value->GetFirstToken());
    }
    $this->Value[]=$Value;
    Return $Value;
  }
  
  Function Parser_NewValue()
  {
    $Res=$this->NewValue();
    $Res->Parsed=True;
    Return $Res;
  }

//****************************************************************
// Modify

  Function Modify_NeedMake() { Return True; }
  Function Modify_MakeKey($key, $Key)
  {
    $this->Parser_MakeMap(Null);
    $Res=&$this->Value[$key];
    $Res??=$this->NewValue();
    ($Res->Key=$this->NewValue())->Parser_SetValue($Key, Null);
    Return $Res;
  }

//****************************************************************
// Array access
  Protected Function _Key($Key) { Return Is_String($Key)? StrToLower($Key):$Key; }
  Protected Function _Value($Value, $Key=Null) { Return $Value; }

  Function Get($Key)
  {
    $key=$this->_Key($Key);
    $List=&$this->Value;
    
    If(!Is_Array($List))
    {
      $this->Error('Wring type ',$this->Type,' access by key ', $Key, '  setted in ', $this->GetFilePos());
      If(!$this->Modify_NeedMake()) Return Null;
      $this->Modify_MakeKey  ($key, $Key);
    }
    
    If(!Array_Key_Exists($key, $List))
    {
      If(!$this->Modify_NeedMake())
      {
        $this->Error('Key ', $Key, ' not found in ', $this->GetFilePos());
        Return Null;
      }
      $this->Modify_MakeKey  ($key, $Key);
    }
    
    $Res=$List[$key];
    $Res->SetUsed();
    Return $Res;
  }
  
  Function Has($Key)
  {
    Return Is_Array($this->Value) && Array_Key_Exists($this->_Key($Key), $this->Value);
  }
  
  Function UnSet($Key)
  {
    If(!Is_Array($this->Value)) Return;
    UnSet($this->Vars[$this->_Key($Key)]);
  }
  
  Function Set($Key, $v)
  {
    if(Is_Null($Key))
      $this->Add($v);
    else
      $this->Value[$this->_Key($Key)]=$this->_Value($v, $Key);
  }
  
  Function Add($v)
  {
    $this->Value[]=$this->_Value($v);
  }
  
//****************************************************************
// ArrayAccess interface

  Public Function OffsetExists ($k    ):Bool  { return $this->Has   ($k);     }
  Public Function OffsetGet    ($k    ):Mixed { return $this->Get   ($k);     }
  Public Function OffsetSet    ($k ,$v):Void  { if(Is_Null($k)) $this->Add($v); else $this->Set($k, $v); }
  Public Function OffsetUnset  ($k    ):Void  {        $this->UnSet ($k);     }
  
//****************************************************************
// Countable interface

  Public Function Count():Int { Return Is_Array($this->Value)? Count($this->Value):0; }
  
//****************************************************************
// IteratorAggregate interface
  
  Function GetIterator():\Traversable
  {
    Return New ArrayIterator(Is_Array($this->Value)? $this->Value:[]);
  }
  
//****************************************************************
// Logging

  Function Warning (...$Args) { Return $this->Log('Warning' ,...$Args); }
  Function Error   (...$Args) { Return $this->Log('Error'   ,...$Args); }
  Function Debug   (...$Args) { Return $this->Log('Debug'   ,...$Args); }

  Function Log(String $LogLevel, ...$Args)
  {
    $Res=Log($LogLevel, $this->GetPath(), ': ', ...$Args)->Logger($this->Parser->Logger);
    If($Pos=$this->GetFilePos())
      $Res->File($Pos->ToArgs());
    Return $Res;
  }

//****************************************************************
// Debug

  Function ToDebug():TDebug { Return New TDebug()->Value($this); } 

  //WLib\Debug\ICustom
  Function Debug_Write(\WLib\Log\CFormat $To)
  {
    $To->Write(...$this->ToDebug()->Res);
  }

//****************************************************************
// Context

  Var $Parser;
  Var $Parent;
  
  Function GetPath()
  {
    $Parent=$this->Parent?->Get();
    $Res=$this->Key?->Value?? ($Parent? Null:'Root');
    $Res??=Log('Error', 'Unknown key for value: ', $this->ToDebug())->Ret('Unknown key');
    
    Return ($Parent?->Parent!==Null? $Parent->GetPath().'.':'').$Res;
  }

  Function NewValue()
  {
    $Res=New Self();
    $Res->Parser=$this->Parser;
    $Res->Parent=\WeakReference::Create($this);
    Return $Res;
  }
  
//****************************************************************
//

  Function _SetType(EType $Type, $Default=Null)
  {
    If($this->Type->Is($Type)) Return False; //The same type
    
    $Default??=$Type->GetDefaultValue(); //TODO: Default Null
    
    If(!$this->Type->IsVoid())
    {
      If($Type->CanCast($this->Value))
        $this->Value=$Type->Cast($this->Value);
      Else
        $this->Warning('Incompatible type ',$this->Type,', expected ', $Type, '; Current value is ', $this->Value)
          ->File($this->GetFilePos()->ToArgs());
    }
    
    $this->Type  =$Type    ;
    $this->Value =$Default ;
    Return True;
  }
  
  Function _SetValue($v, $Token)
  {
    Switch(GetType($v))
    {
    Case 'boolean' : $this->_SetType(EType::Bool   ,$v); Break;
    Case 'integer' : $this->_SetType(EType::Int    ,$v); Break;
    Case 'double'  : $this->_SetType(EType::Float  ,$v); Break;
    Case 'string'  : $this->_SetType(EType::String ,$v); Break;
    Case 'NULL'    : $this->_SetType(EType::Null   ,$v); Break;
    Default:
      Log('Error', 'Unknown value ', $v)->BackTrace()->File($Token->GetFilePos()->ToArgs());
      Return;
    }
    $this->Value=$v; 
    $this->SetToken($Token); //TODO: Remove?
  }
  
//****************************************************************
// Using

  Var $Used=False;
  
  Function CheckUnused()
  {
    If(!$this->Used) Return $this->Warning('This value is unused: ', $this->ToDebug())->Ret();
    $List=$this->Value;
    If(!Is_Array($List)) Return;
    ForEach($List As $Item)
      $Item->CheckUnused();
  }
  
  Function SetUsed() { $this->Used=True; }
  Function UnUsed() { $this->Used=False; }
//****************************************************************
// Validator and Getter

  Function GetByType(EType $Type, $Def)
  { //TODO: Nullable
    $this->_SetType($Type, Null, $Def);
  }
  
  Function GetNull   (          ) { Return $this->GetByType(EType::Null        ); }
  Function GetBool   ($Def=False) { Return $this->GetByType(EType::Bool   ,$Def); }
  Function GetInt    ($Def=0    ) { Return $this->GetByType(EType::Int    ,$Def); }
  Function GetFloat  ($Def=0.0  ) { Return $this->GetByType(EType::Float  ,$Def); }
  Function GetString ($Def=''   ) { Return $this->GetByType(EType::String ,$Def); }
  Function GetList   ($Def=[]   ) { Return $this->GetByType(EType::List   ,$Def); }
  Function GetMap    ($Def=[]   ) { Return $this->GetByType(EType::Map    ,$Def); }
  
//****************************************************************
}

?>