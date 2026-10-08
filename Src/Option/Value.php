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
  
  Function GetValue():Mixed
  {
    $Res=$this->Value;
    If(Is_Array($Res))
      ForEach($Res As $k=>$v)
        $Res[$k]=$v->GetValue();
    
    Return $Res;
  }
  
  Function SetValue($v) //, $Token)
  {
    $Detect=EType::Detect($v);
    If($this->SetType($Detect ,$v)) Return;
    If(Is_Array($v))
    {
      ForEach($v As $k=>$v)
        $this[$k]=$v;
    }
    Else
      $this->Value=$v; 
  //$this->SetToken($Token); //TODO:
  }
  
  Function SetType(EType $Type, $Default=Null):Bool //Returns true is default was assigned
  {
    If($this->Type->Is($Type)) Return False; //The same type
    
    $Res=True;
    If(Is_Array($Default) && Count($Default)!==0) { $Res=False; $Default=Null; }
  //If($Default!==Null) //TODO: Check type of $Default

    $Default??=$Type->GetDefaultValue(); //TODO: Default Null
    
    If(!$this->Type->IsVoid())
    {
      If($Type->CanCast($this->Value))
      {
        $Default=$Type->FastCast($this->Value);
        $Res=False;
      }
      Else
        $this->Warning('Incompatible type ',$this->Type,', expected ', $Type, '; Current value is ', $this->Value)
          ->File($this->GetFilePos()->ToArgs());
    }
    
    $this->Type  =$Type    ;
    $this->Value =$Default ;
    If(!$Type->IsMap())
      $this->SetHasValue();
    Return $Res;
  }
  
  //Unused
  Function SetError(Array|String $Error, $Token=Null)
  {
    $this->Type  =EType::Error;
    $this->Value =$Error;
    $this->SetToken($Token);
  }
  
//****************************************************************
// Has Value
  Var         $HasValue =False;
  
  Function SetHasValue()
  {
    If($this->HasValue) Return;
    $this->HasValue=True;
    $this->Parent?->SetHasValue();
  }
  
  Function CountHasValue()
  {
    $Res=0;
    ForEach($this->Value As $Item)
      If($Item->HasValue)
        $Res++;
    Return $Res;
  }

//****************************************************************
// Token info
  
  Var $Tokens=[];
  
  Function SetToken($v) { If($v) $this->Tokens  =[$v]; }
  Function AddToken($v) { If($v) $this->Tokens[]= $v ; }

  Function GetFirstToken () { Return Array_First ($this->Tokens); }
  Function GetLastToken  () { Return Array_Last  ($this->Tokens); }
  
  Function GetFilePos      ():TFilePos { Return $this->GetFirstToken ()?->GetFilePos()?? $this->GetFilePosError (); }
  Function GetFilePosEnd   ():TFilePos { Return $this->GetLastToken  ()?->GetFilePos()?? $this->GetFilePosError (); }
  Protected Function GetFilePosError ():TFilePos { Return TFilePos::GetEmptyWithError('Path: ', $this->GetPath()); }
  
//****************************************************************
// Parser interface
  
  Function Parser_SetType(EType $Type, $Token, $Value, $Merge=True)
  {
    If($this->Type->Is($Type) && $Type->IsMap() && $Merge)
    {
      $this->AddToken($Token);
      Return;
    }
    
    If(!$this->Type->IsVoid())
    {
      If($Type->IsArray() && $this->Type->IsArray() && $this->Count()===0)
        {}// Ok, allow change List<=>Map when array is empty
      Else
        $this->Warning($Type, '=',$Value,' has already exist: ', 
          $this->ToDebug(), ' was setted in ', $this->GetFilePos())
          ->File($Token->GetFilePos()->ToArgs());
        //TODO: Error
    }
    
    $this->Type    =$Type;
    $this->Value   =$Type->GetDefaultValue();
    $this->SetToken($Token);
    If(!$Type->IsMap())
      $this->SetHasValue();
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
    $this->Parser_MakeMap($Token, True);
    $Res=&$this->Value[$this->_Key($Key->Value)];
    $Res??=$this->Parser_NewValue();
    $Res->AddToken($Token);
    $Res->Key=$Key;
    Return $Res;
  }
  
  Function Parser_GetPathItem()
  {
    If($this->Count()===0) Return $this; //[]
    If($this->Count()!==1 || !$this->Has(0))
      Return $this->Error('ParsePath: Unknown key ',$Item, ' for path ', $Item->GetPath())
        ->File($Token->GetFilePos()->ToArgs())->Ret();
    $Res=$this->Value[0]; //[Key]
    Array_UnShift ($Res->Tokens, Array_Shift($this->Tokens));
    Array_Push    ($Res->Tokens, ...$this->Tokens);
    Return $Res;
  }

  Function Parser_MakePath(Array $Path)
  {
    $Res=$this;
    ForEach($Path As $Key)
      If($Key->Type->IsArray())
        $Res=$Res->Parser_AddItem(Null, $Key);
      Else
        $Res=$Res->Parser_MakeKey($Key);
    Return $Res;
  }

  Function Parser_MakeMap  ($Token, $Merge) { $this->Parser_SetType(EType::Map  ,$Token, 'Map'  ,$Merge); }
  Function Parser_MakeList ($Token, $Merge) { $this->Parser_SetType(EType::List ,$Token, 'List' ,$Merge); }
  
  Function Parser_KeyMap  (TValue $Key, $Token) { $Res=$this->Parser_MakeKey($Key); $Res->Parser_MakeMap  ($Token, True); Return $Res; }
  Function Parser_KeyList (TValue $Key, $Token) { $Res=$this->Parser_MakeKey($Key); $Res->Parser_MakeList ($Token, True); Return $Res; }
  
  Function Parser_AddItem ($Value=Null, $Key=Null)
  {
    If(!$this->Type->Is(EType::List))
    {
      $Key??=$Value;
      $this->Warning('Sould be list at ',$Key?->GetFilePos());
      $this->Parser_MakeList($Key->GetFirstToken(), True);
    }
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
  Function Modify_SetValue($v)
  {
    $this->Type  =EType::Detect($v); 
    $this->Value =$v; 
  }
  
  Function Modify_MakeKey($key, $Key)
  {
    $this->Parser_MakeMap(Null, True);
    $Res=&$this->Value[$key];
    $Res??=$this->NewValue();
    ($Res->Key=$this->NewValue())->Modify_SetValue($Key, Null);
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
      If(!$this->Type->IsVoid())
        $this->Error('Wrong type ',$this->Type,' access by key ', $Key, '  setted in ', $this->GetFilePos());
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
  
  Function Set($Key, $v) //TODO: Error
  {
    if(Is_Null($Key))
      $this->Add($v);
    else
      $this->Value[$this->_Key($Key)]=$this->_Value($v, $Key);
  }
  
  Function Add($v) //TODO: Error
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
    $Res=Log($LogLevel, $this->GetPath(), ': ', ...$Args)->Logger($this->GetLogger());
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

  Var $Owner  { Get=>$this->Owner  ?->Get(); Set=>$value? \WeakReference::Create($value):Null; }
  Var $Parent { Get=>$this->Parent ?->Get(); Set=>$value? \WeakReference::Create($value):Null; }
  
  Function GetLogger() { Return $this->Owner?->GetLogger(); }
  
  Function GetPath()
  {
    $Parent=$this->Parent;
    $Res=$this->Key?->Value?? ($Parent? Null:'Root');
    $Res??=$this->ToDebug();
  //$Res??=Log('Error', 'Unknown key for value: ', $this->ToDebug())->Ret('Unknown key');
    
    Return ($Parent?->Parent!==Null? $Parent->GetPath().'.':'').$Res;
  }

  Function NewValue()
  {
    $Res=New Self();
    $Res->Owner  =$this->Owner;
    $Res->Parent =$this;
    Return $Res;
  }
  
//****************************************************************
// Using

  Var $Used=False;
  
  Function CheckUnused()
  {
    If(!$this->Used) Return $this->Warning('This key is unused. Value: ', $this->ToDebug())->Ret();
    $List=$this->Value;
    If(!Is_Array($List)) Return;
    ForEach($List As $Item)
      $Item->CheckUnused();
  }
  
  Function SetUsed() { $this->Used=True; }
  Function UnUsed() { $this->Used=False; }
  Function UseAll()
  {
    $this->SetUsed();
    $List=$this->Value;
    If(!Is_Array($List)) Return;
    ForEach($List As $Item)
      $Item->UseAll();
  }
//****************************************************************
// Verify

  Function Verify()
  { //TODO: Only if debug
    If($Key=$this->Key)
      If(($Parent=$Key->Parent)!==$this)
        Log('Error', 'Key has wrong parent');
        
    If(Is_Array($List=$this->Value))
      ForEach($List As $Item)
        If(($Parent=$Item->Parent)!==$this)
          Log('Error', 'Item has wrong parent');
  }
  
//****************************************************************
// Validator and Getter
  
  Function CheckType(String|Callable $CheckType, $ShowError=True)
  {
    If(Is_Callable($CheckType))
    {
      $Res=$Type($this); //Returns Str: Error|Null
      If($Res===Null) Return True;
      If(!$ShowError) Return False;
    }
    Else
    {
      $Type=$this->Type->IsType($CheckType);
      If($Type && $Type->CanCast($this->Value))
      {
      //$this->Value=$Type->FastCast($this->Value);
        Return True;
      }
      If(!$ShowError) Return False;
      $Res=['Wrong type ', $this->Type, ', required ', $CheckType];
    }
    
    $this->Log('Error', 'CheckType: ', ...((Array)$Res));
    Return False;
  }
  
//****************************************************************
}

?>