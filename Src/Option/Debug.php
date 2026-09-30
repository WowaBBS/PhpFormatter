<?
NameSpace Reformat\Option;

Use Function Reformat\Log;
Use Function Reformat\Utils\Str\IsWord;

Class TDebug Implements \WLib\Debug\ICustom
{
  Var Array $Res=[];
  
  Function Last() { Return Array_Last($this->Res); }
  Function Last_Is(...$l) { Return In_Array($this->Last()?? '', $l, True); }
  Function Last_Remove()  { Return Array_Pop($this->Res); }
  
  Function Raw    ($v) { $this->Res[]=$v; Return $this; }
  Function Null     () { Return $this->Raw('Null'); }
  Function Undefined() { Return $this->Raw('Undefined'); }
  Function Bool   ($v) { Return $this->Raw($v? 'True':'False'); }
  Function Int    ($v) { Return $this->Raw($v); }
  Function Float  ($v) { Return $this->Raw(Is_Finite($v)? $v:(Is_NaN($v)? 'NaN':($v>0? 'Inf':'-Inf'))); }
  Function String ($v) { Return $this->Raw(Json_EnCode($v ,JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_UNESCAPED_SLASHES)); }
  
  Function Error (...$Args)
  {
    Log('Error', ...$Args)->BackTrace();
    Return $this->Write('Error', '(', ...$Args)->Raw(')');
  }
  
  Function Write(...$Args)
  {
    ForEach($Args As $v)
      $this->WriteItem($v);
    Return $this;
  }
  
  Function __Invoke(...$Args) { Return $this->Write(...$Args); }
  Function __Construct(...$Args) { $this->Write(...$Args); }
  
  Function WriteItem($v)
  {
    Switch(GetType($v))
    {
      Case 'string'  :
      Case 'integer' : Return $this->Raw($v);
      Default: Return $this->PhpVal($v);
    }
  }
  
  Function PhpVal(Mixed $v)
  {
    Switch(GetType($v))
    {
    Case 'NULL'    : Return $this->Null   (  );
    Case 'integer' : Return $this->Int    ($v);
    Case 'boolean' : Return $this->Bool   ($v);
    Case 'double'  : Return $this->Float  ($v);
    Case 'string'  : Return $this->String ($v);
    Case 'array'   : Return $this->Array  ($v);
    Case 'object'  : Return $this->Object ($v);
    Default        : Return $this->Error  ('Unknown value ', $v);
    }
  }
  
  Function Value(TValue $Value)
  {
    $v=$Value->Value;
    Switch($Value->Type)
    {
    Case EType::Void   : $this->Undefined  (  ); Break;
    Case EType::Null   : $this->Null       (  ); Break;
    Case EType::Bool   : $this->Bool       ($v); Break;
    Case EType::Int    : $this->Int        ($v); Break;
    Case EType::Float  : $this->Float      ($v); Break;
    Case EType::String : $this->String     ($v); Break;
    Case EType::List   : $this->Value_List ($v); Break;
    Case EType::Map    : $this->Value_Map  ($v); Break;
    }
    Return $this;
  }

  Function Array($v) { Return Array_Is_List($v)? $this->List($v):$this->Map($v); }
  
  Function Object($v)
  {
    Switch(Get_Class($v))
    {
    Case TValue::Class : Return $this->Value($v);
    Default            : Return $this->Error('Unknown Class ', Get_Class($v));
    }
  }
  
  Function Map_Key($v)
  {
    If(Is_String($v) && IsWord($v))
      Return $this->Raw($v);
    Else
      Return $this->PhpVal($v);
  }
  
  Function Value_Map_Key(TValue $Value)
  {
    If($Value->Type===EType::String && IsWord($Value->Value))
      Return $this->Raw($Value->Value);
    Else
      Return $this->Value($Value);
  }
  
  Function Map_Item($v, $k)
  {
    $this->Map_Key($k);
    $this->Raw('=');
    Return $this->PhpVal($v);
  }
  
  Function Value_Map_Item(TValue $v, $k)
  {
    If($v->Key)
      $this->Value_Map_Key($v->Key);
    Else
    {
      $this->Raw($k);
      Log('Error', 'Key ',$k,' is not exists in Value')
        ->File($Value->GetFilePos()->ToArgs());
    }
    $this->Raw('=');
    $this->Value($v);
  }
  
  Function List($Value)
  {
    $z=True;
    $this->Raw('[');
    ForEach($Value As $v)
    {
      If($z) $z=False;
      Else $this->Raw(', ');
      $this->PhpVal($v);
    }
    Return $this->Raw(']');
  }
  
  Function Value_List(Array $Value)
  {
    $z=True;
    $this->Raw('[');
    ForEach($Value As $v)
    {
      If($z) $z=False;
      Else $this->Raw(', ');
      $this->Value($v);
    }
    $this->Raw(']');
  }

  Function Map($v)
  {
    If($this->Last_Is('=', '') && Count($v)===1)
      Return $this->Map_Short($v);
    Else
      Return $this->Map_Long($v);
  }
  
  Function Value_Map($v)
  {
    If($this->Last_Is('=', '') && Count($v)===1)
      $this->Value_Map_Short($v);
    Else
      $this->Value_Map_Long($v);
  }
  
  Function Map_Short($Value)
  {
    If($this->Last_Remove())
      $this->Raw('.');
    ForEach($Value As $k=>$v)
      $this->Map_Item($v, $k);
    Return $this;
  }
  
  Function Value_Map_Short(Array $Value)
  {
    If($this->Last_Remove())
      $this->Raw('.');
    ForEach($Value As $k=>$v)
      $this->Value_Map_Item($v, $k);
    Return $this;
  }
  
  Function Map_Long($Value)
  {
    $this->Raw('{');
    $z=True;
    ForEach($Value As $k=>$v)
    {
      If($z) $z=False;
      Else $this->Raw(', ');
      $this->Map_Item($v, $k);
    }
    $this->Raw('}');
    Return $this;
  }
  
  Function Value_Map_Long(Array $Value)
  {
    $this->Raw('{');
    $z=True;
    ForEach($Value As $k=>$v)
    {
      If($z) $z=False;
      Else $this->Raw(', ');
      $this->Value_Map_Item($v, $k);
    }
    $this->Raw('}');
    Return $this;
  }
//****************************************************************
// String 

  Function ToString() { Return Implode($this->Res); }
  Function __ToString() { Return $this->ToString(); }

//****************************************************************
// Debug

  //WLib\Debug\ICustom
  Function Debug_Write(\WLib\Log\CFormat $To)
  {
    $To->Write(...$this->Res);
  }

//****************************************************************
}
