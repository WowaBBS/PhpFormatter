<?
NameSpace Reformat\Option;

Use Function Reformat\Utils\Str\IsWord;

Class TDebug
{
  Var $Res=[];
  
  Function __Invoke($v)
  {
    $this->Res[]=$v;
  }

  Function ToString() { Return Implode($this->Res); }
  
  Function _ValueKey(TValue $Value)
  {
    If($Value->Type===EType::String && IsWord($Value->Value))
      $this($Value->Value);
    Else
      $this->Value($Value);
  }
  
  Function ValueMapItem(TValue $v, $k)
  {
    If($v->Key)
      $this->_ValueKey($v->Key);
    Else
    {
      $this($k);
      Log('Error', 'Key ',$k,' is not exists in Value')
        ->File(...$Value->FilePos->GetFilePos()->ToArgs());
    }
    $this('=');
    $this->Value($v);
  }
  
  Function Value(TValue $Value)
  {
    $v=$Value->Value;
    Switch($Value->Type)
    {
    Case EType::Void   : $this('Undefined'); Break;
    Case EType::Null   : $this('Null'); Break;
    Case EType::Bool   : $this($v? 'True':'False'); Break;
    Case EType::Int    : $this($v); Break;
    Case EType::Float  : $this(Is_Finite($v)? $v:(Is_NaN($v)? 'NaN':($v>0? 'Inf':'-Inf'))); Break;
    Case EType::String : $this(Json_EnCode($v ,JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_UNESCAPED_SLASHES)); Break;
    Case EType::List   :
      $z=True;
      $this('[');
      ForEach($Value->Value As $v)
      {
        If($z) $z=False;
        Else $this(', ');
        $this->Value($v);
      }
      $this(']');
      Break;
    Case EType::Map    :
      If(False && Count($v)===1) //If short //TODO: Enable
      {
        $this('.');
        ForEach($Value->Value As $k=>$v)
          $this->ValueMapItem($v, $k);
        Break;
      }
      $this('{');
      $z=True;
      ForEach($Value->Value As $k=>$v)
      {
        If($z) $z=False;
        Else $this(', ');
        $this->ValueMapItem($v, $k);
      }
      $this('}');
      Break;
    }
  }
}
