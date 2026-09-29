<?
NameSpace Reformat\Option;

Use Reformat\FilePos\TInfo     As TFilePos;
Use Reformat\FilePos\IProvider As IFilePos;
Use Function Reformat\Log;

Class TValue Implements IFilePos, \ArrayAccess, \Countable, \IteratorAggregate, \WLib\Debug\ICustom
{
  Var ?TValue $Key   =Null;
  Var         $Value ;
  Var  EType  $Type  =EType::Default;
  
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
  
  Protected Function Parser_SetType(EType $Type, $Token, $Value)
  {
    If($this->Type->Is($Type) && $Type->IsMap())
    {
      $this->AddToken($Token);
      Return $this;
    }
    If(!$this->Type->IsVoid())
    {
      $this->Warning($Type, ': Value ',$this->GetPath(),'=',$Value,' has already exist: ', 
        $this->ToDebug(), ' was setted in ', $this->GetFilePos())
        ->File(...$Token->GetFilePos()->ToArgs());
      //TODO: Error
    }
    
    $this->Type    =$Type;
    $this->Value   =$Type->GetDefaultValue();
    $this->SetToken($Token);
    Return $this;
  }
  
  Function Parser_SetValue($v, $Token): Bool
  {
    Switch(GetType($v))
    {
    Case 'boolean' : $this->Parser_SetType(EType::Bool   ,$Token, $v); Break;
    Case 'integer' : $this->Parser_SetType(EType::Int    ,$Token, $v); Break;
    Case 'double'  : $this->Parser_SetType(EType::Float  ,$Token, $v); Break;
    Case 'string'  : $this->Parser_SetType(EType::String ,$Token, $v); Break;
    Case 'NULL'    : $this->Parser_SetType(EType::Null   ,$Token, $v); Break;
    Default:
      Log('Error', 'Unknown value ', $v)->BackTrace()->File(...$Token->GetFilePos()->ToArgs());
      Return False;
    }
    $this->Value=$v; 
    $this->SetToken($Token); //TODO: Remove?
    Return True;
  }
  
  Function Parser_MakeKey(TValue $Key)
  {
    $Token=$Key->GetFirstToken();
    $this->Parser_MakeMap($Token);
    $Res=&$this->Value[$Key->Value];
    $Res??=$this->NewValue();
    $Res->AddToken($Token);
    $Res->Key=$Key;
    Return $Res;
  }
  
  Function Parser_MakeMap  ($Token) { Return $this->Parser_SetType(EType::Map  ,$Token, 'Map'  ); }
  Function Parser_MakeList ($Token) { Return $this->Parser_SetType(EType::List ,$Token, 'List' ); }
  
  Function Parser_KeyMap  (TValue $Key, $Token) { Return $this->Parser_MakeKey($Key)->Parser_MakeMap  ($Token); }
  Function Parser_KeyList (TValue $Key, $Token) { Return $this->Parser_MakeKey($Key)->Parser_MakeList ($Token); }
  
  Function Parser_AddItem($Value=Null)
  {
    $Value??=$this->NewValue();
    If(!$Value->Key)
    {
      $Value->Key=$Value->NewValue();
      $Value->Key->Parser_SetValue(Count($this->Value), $Value->GetFirstToken());
    }
    $this->Value[]=$Value;
    Return $Value;
  }

//****************************************************************
  Protected Function _Key($Key) { Return Is_String($Key)? StrToLower($Key):$Key; }
  Protected Function _Value($Value, $Key=Null) { Return $Value; }

  Function Get($Key)
  {
    $key=$this->_Key($Key);
    $List=&$this->Value;
    
    If(!Is_Array($List))
    {
      $this->Error('Key ', $Key, ' not found in ', $this->GetFilePos());
      If(!$this->IsValidating()) Return Null;
      $this->Parser_MakeMap  ();
    }
    
    If(!Array_Key_Exists($key, $List))
    {
      $this->Error('Key ', $Key, ' not found in ', $this->GetFilePos());
      If(!$this->IsValidating()) Return Null;
      //TODO: Create Key
    }
    
    Return $List[$key];
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
    Return Log($LogLevel, ...$Args)->Logger($this->Parser->Logger);
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
    $Res=$this->Key?->Value?? 'Unknown';
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
}

?>