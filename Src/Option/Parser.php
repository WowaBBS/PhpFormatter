<?
NameSpace Reformat\Option;

Use Function Reformat\Log;

Use Function Reformat\Utils\Token\{
  TokenizeCode      ,
  RemoveComments    ,
  RemoveWhiteSpaces ,
  LinePosReIndex    ,
};
Use Function Reformat\Utils\Stream\{
  Next   ,
  Skip   ,
  Filter ,
  Map    ,
};

Class TParser
{
  Use TraitLog;

  Var $Tokens;
  
  Function __Construct(Array $FilePos=[])
  {
    $this->FilePos=$FilePos;
  }
  
  Function NextToken(Bool $Next=True)
  {
    Return $this->SafeNextToken($Next)?? $this->Error('Unexpected end of option')->Ret();
  }
  
  Function SafeNextToken(Bool $Next=True)
  {
    $Res=$Next? Next($this->Tokens):$this->Tokens->Current();
    $this->Log_SetToken($Res);
    Return $Res;
  }
  
  Function CheckWord($Token): False|String
  {
    If(!$Key->IsWord()) Return $this->Error('Unknown word: ',$Token->Text)->Ret(False);
    Return $Token->Text;
  }
  
  Function Parse($Text, Array $FilePos=[])
  {
    $Tokens=TokenizeCode($Text); //TODO:, $FilePos);
    
  //RemoveComments    ($Tokens);
  //RemoveWhiteSpaces ($Tokens);
    $this->Tokens=$Tokens
      |> Filter(fn($Token)=>!$Token->Is(T_COMMENT, T_WHITESPACE));
    
  //$Vars=New TValue();
    $Vars=[];
    While($this->ParseMapItem($Vars, [','=>True, ';'=>True, ''=>True])===Null)
    {
    }
    
    Return $Vars;
  }
  
  Function ParseMapItem(&$Vars, $End):?Bool
  {
  //$Vars->MakeMap();
    If(!Is_Array($Vars))
    {
      If($Vars!==Null)
        $this->Error('Map: Value has already exist: ', $Vars);
      $Vars=[];
    }
    $Token=$this->NextToken(False);
    If($Token===Null) Return False;
    If($End[$Token->Text?? '']?? False) Return Null;
    If(!$Token) Return False;
    $Key=Null;
    If(!$this->ParseKey($Key)) Return False;
    Switch($Text=$this->NextToken()?->Text)
    {
    Case '.'  : Return $this->ParseMapItem ($Vars[$Key], $End);
    Case '{'  : Return $this->ParseMap     ($Vars[$Key]);
    Case '['  : Return $this->ParseList    ($Vars[$Key]);
    Case '=>' :
    Case '='  :
    Case ':'  : Break;
    Default   : Return $this->Error('Expected ".", "=", ":", "{" or "=>", given ',$Text)->Ret(False);
    }
    $Value=Null;
    If(!$this->ParseValue($Value)) Return False;
    If(Array_Key_Exists($Key, $Vars))
      $this->Warning('Key ',$Key, ' has already exists'); //TODO: Where?
    $Vars[$Key]=$Value;
    Return True;
  }
  
  Function ParseMap(&$Vars)
  {
    While(1)
    {
      $R=$this->ParseMapItem($Vars, ['}'=>True, ','=>True, ';'=>True]);
    //If($R===Null) Break;
      If($R===False) Return;
      Switch(($Token=$this->NextToken())->Text)
      {
      Case ';':
      Case ',': Continue 2;
      Case '}': Return True;
      Default: Return $this->Error('Unknown token ', $Token)->Ret(False);
      }
    }
    Return True;
  }
  
  Function ParseList(&$Vars)
  {
    If($Vars!==Null)
      $this->Warning('List: Value has already exist: ', $Vars);
    $Vars=[];
    While(1)
    {
      $Token=$this->NextToken(False)?->Text;
      If($Token===']') { $this->NextToken(); Break; }
      If($Token===',') $this->NextToken(); //TODO: Always ,
      $Value=Null;
      If(!$this->ParseValue($Value)) Return False;
      $Vars[]=$Value;
    }
    Return True;
  }
  
  Function ParseKey(&$Vars)
  {
    $Token=$this->NextToken();
    If($Token===Null) Return False;
    Switch($Token->Id)
    {
    Case T_LNUMBER:
    Case T_DNUMBER:
    Case T_CONSTANT_ENCAPSED_STRING:
      $Vars=Eval('Return '.$Token->Text.';');
      Break;
    Default:
      If($Token->IsWord()) { $Vars=$Token->Text; Break; }
      Return $this->Error('Unknown Key token: ', $Token)->Ret(False);
    }
    Return True;
  }
  
  Function ParseValue(&$Vars, $WordAllow=False)
  {
    $Token=$this->NextToken();
    If($Token===Null) Return False;
    Switch($Token->Id)
    {
    Case T_LNUMBER:
    Case T_DNUMBER:
    Case T_CONSTANT_ENCAPSED_STRING:
    //Log('Debug', 'Eval ',$Token->Text);
      $Value=Eval('Return '.$Token->Text.';');
      Break;
    Default:
      Switch(StrToLOwer($Token->Text))
      {
      Case 'null'  : $Value=Null  ; Break;
      Case 'true'  : $Value=True  ; Break;
      Case 'false' : $Value=False ; Break;
      Case 'debugpos' : $Value=$this->Debug('DebugPos')->Ret(True); Break;
      Case '{': Return $this->ParseMap($Vars);
      Case '[': Return $this->ParseList($Vars);
      Default:
        If($WordAllow && $Token->IsWord()) { $Value=$Token->Text; Break; }
        Return $this->Error('Unknown Value token: ', $Token)->Ret(False);
      }
    }
    If($Vars!==Null)
      $this->Warning('Value overrided, Old: ', $Vars, '; New:', $Value, ';');
    $Vars=$Value;
    Return True;
  }
  
  Function ParseVarsMap(&$Vars)
  {
    While(1)
    {
      $this->_Parse($Vars);
      $Token=$this->NextToken($Tokens);
      If(!$Token) Return;
      If($Token->Text===',') Continue;
      If($Token->Text==='}') Return True;
    }
    $this->Error('Expected , or } but taken: ',$Token->Text)->Ret();
  }
  
  Function ParseVars(&$Vars)
  {
    $Token=$this->NextToken();
    If(!$Token) Return;
    If($Token->Text==='=') Return $this->ParseValue($Vars);
    If($Token->Text==='{') Return $this->ParseVarsMap($Vars);
    If(!$Token->IsWord()) Return $this->Error('Unknown word: ',$Token->Text)->Ret(False);
    Return $this->_Parse($Vars[$Token->Text]);
  }
}
