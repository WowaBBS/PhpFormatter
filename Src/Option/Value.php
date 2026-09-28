<?
NameSpace Reformat\Option;

Class TValue Implements \ArrayAccess, \Countable, \IteratorAggregate
{
  Var ?TValue $Key   ;
  Var         $Value ;
  Var  EType  $Type  =EType::Default;
  
  Var $FilePos;
  
  Function SetFilePos($v) { $this->FilePos  =$v; }
  Function AddFilePos($v) { $this->FilePos??=$v; }
  
  Function ToValue():Mixed
  {
    $Res=$this->Value;
    If(Is_Array($Res))
      ForEach($Res As $k=>$v)
        $Res[$k]=$v->ToValue();
    
    Return $Res;
  }
  
  Function SetType(EType $Type, $Token)
  {
    If($this->Type->Is($Type) && $Type->IsMap())
    {
      $this->AddFilePos($Token);
      Return $this;
    }
    If(!$this->Type->IsVoid())
    {
      Log('Error', $Type, ': Value has already exist: ', $this->ToValue());
      //TODO: Error
    }
    
    $this->Type    =$Type;
    $this->Value   =$Type->GetDefaultValue();
    $this->SetFilePos($Token);
    Return $this;
  }
  
  Function SetValue($v, $Token): Bool
  {
    Switch(GetType($v))
    {
    Case 'boolean' : $this->SetType(EType::Bool   ,$Token); Break;
    Case 'integer' : $this->SetType(EType::Int    ,$Token); Break;
    Case 'double'  : $this->SetType(EType::Float  ,$Token); Break;
    Case 'string'  : $this->SetType(EType::String ,$Token); Break;
    Case 'NULL'    : $this->SetType(EType::Null   ,$Token); Break;
    Default:
      Log('Error', 'Unknown value ', $v)->File(...$Token->GetFilePos()->ToArgs());
      Return False;
    }
    $this->Value=$v; 
    $this->SetFilePos($Token); //TODO: Remove?
    Return True;
  }
  
  Function MakeKey(TValue $Key)
  {
    $this->MakeMap($Key->FilePos);
    $Res=&$this->Value[$Key->Value];
    If($Res)
    {
      $Res->AddFilePos($Key->FilePos);
      Return $Res;
    }
    $Res=New TValue();
    $Res->SetFilePos($Key->FilePos);
    Return $Res;
  }
  
  Function MakeMap  ($Token) { Return $this->SetType(EType::Map  ,$Token); }
  Function MakeList ($Token) { Return $this->SetType(EType::List ,$Token); }
  
  Function KeyMap  (TValue $Key, $Token) { Return $this->MakeKey($Key)->MakeMap  ($Token); }
  Function KeyList (TValue $Key, $Token) { Return $this->MakeKey($Key)->MakeList ($Token); }
  
//****************************************************************
  Function _Key($Key) { Return Is_String($Key)? StrToLower($Key):$Key; }
  Function _Value($Value, $Key=Null) { Return $Value; }

  Function Get($Key)
  {
    $key=$this->_Key($Key);
    $List=&$this->Value;
    If(!Is_Array($List)) Return Null;
    Return Is_Array($List)? 
      Array_Key_Exists($key, $List)
    ($this->Value[$key]?? Null):Null;
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
  
  
}

?>