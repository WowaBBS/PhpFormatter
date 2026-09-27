<?
NameSpace Reformat\Option;

Use Function Reformat\Log;
Use Reformat\FilePos\IProvider As IFilePos;
Use Function Reformat\Utils\Token\TokenizeCode;

Class TParser
{
  Use TraitLog;

  Var $Tokens    ;
  Var $NextToken ;
  
  Function __Construct(?IFilePos $FilePos=Null)
  {
    $this->Log_FilePos($FilePos);
  }
  
  Function NextToken(Bool $PreView=False)
  {
    Return $this->SafeNextToken($PreView)?? ($PreView? Null:$this->Error('Unexpected end of option')->Ret());
  }
  
  // Returns next token is not Comment or WhiteSpace
  Function SafeNextToken(Bool $PreView)
  {
    $Res=$this->NextToken;
    
    While($Res?->Is(T_COMMENT, T_WHITESPACE))
      $Res=$Res->Next; //Log('Debug', 'Token.Skip ', $Res);
    
    If($PreView)
      Return $Res;

    $this->Log_FilePos($Res?? $this->Tokens->GetFilePosEnd());
  //If(!$Res) Log('Debug', 'LastFilePos: ', $this->Tokens->GetFilePosEnd());

    $this->NextToken=$Res?->Next;
  //Log('Debug', 'Token.', $PreView? 'PreView':'Next', ' ', $Res);
    Return $Res;
  }
  
  Function CheckWord($Token): False|String
  {
    If(!$Key->IsWord()) Return $this->Error('Unknown word: ',$Token->Text)->Ret(False);
    Return $Token->Text;
  }
  
  Function IsNext(String ...$Args)
  {
    $Token=$this->NextToken(True);
    If(!In_Array($Token?->Text?? '', $Args, True))
      Return False;
    $this->NextToken();
    Return True;
  }
  
  Function Parse($Text, ?IFilePos $FilePos=Null)
  {
    $Tokens=TokenizeCode($Text, $FilePos);
    
    $this->Tokens    =$Tokens;
    $this->NextToken =$Tokens->First;
    
  //$Vars=New TValue();
    $Vars=[];
    While(1)
    {
      If(!$this->ParseMapItem($Vars)) 
        Return $Vars; // Error
      If(!$this->IsNext(',', ';'))
        Break;
    }
    
    Return $Vars;
  }
  
  Function ParseMapItem(&$Vars):Bool
  {
  //$Vars->MakeMap();
    If(!Is_Array($Vars))
    {
      If($Vars!==Null)
        $this->Error('Map: Value has already exist: ', $Vars);
      $Vars=[];
    }
    
    $Key=Null;
    If(!$this->ParseKey($Key)) Return False;
    Switch($Text=$this->NextToken()?->Text)
    {
    Case '.'  : Return $this->ParseMapItem ($Vars[$Key]);
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
    If(!Is_Array($Vars))
    {
      If($Vars!==Null)
        $this->Warning('Map: Value has already exist: ', $Vars);
      $Vars=[];
    }
    If($this->IsNext('}')) Return True;
    While(1)
    {
      $R=$this->ParseMapItem($Vars);
      If($R===False) Return False;
      Switch(($Token=$this->NextToken())?->Text?? '')
      {
      Case '': Return False;
      Case ';':
      Case ',': Continue 2;
      Case '}': Return True;
      Default: Return $this->Error('Map: Excepted ":", "." or "}", given ', $Token)->Ret(False);
      }
    }
    Return True;
  }
  
  Function ParseList(&$Vars)
  {
    If($Vars!==Null)
      $this->Warning('List: Value has already exist: ', $Vars);
    $Vars=[];
    
    If($this->IsNext(']')) Return True;
    While(1)
    {
      $Value=Null;
      If(!$this->ParseValue($Value)) Return False;
      $Vars[]=$Value;
      
      Switch(($Token=$this->NextToken())?->Text?? '')
      {
      Case '': Return False;
      Case ';':
      Case ',': Continue 2;
      Case ']': Return True;
      Default: Return $this->Error('Map: Excepted ":", "." or "]", given ', $Token)->Ret(False);
      }
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
      $Token=$this->NextToken();
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
