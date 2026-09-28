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
  
  Function __Construct(String $Text, ?IFilePos $FilePos=Null)
  {
    $this->Log_FilePos($FilePos);
    $Tokens=TokenizeCode($Text, $FilePos);
    
    $this->Tokens    =$Tokens;
    $this->NextToken =$Tokens->First;    
  }
  
  // Returns next token is not Comment or WhiteSpace
  Function PreView()
  {
    $Res=$this->NextToken;
    
    While($Res?->Is(T_COMMENT, T_WHITESPACE))
      $Res=$Res->Next; //Log('Debug', 'Token.Skip ', $Res);
      
    Return $Res;
  }
  
  Function SafeNext()
  {
    $Res=$this->PreView();
    $this->Log_FilePos($Res?? $this->Tokens->GetFilePosEnd());
    $this->NextToken=$Res?->Next;
    Return $Res;
  }
  
  Function Next()
  {
    Return $this->SafeNext()?? $this->Error('Unexpected end of option')->Ret();
  }
  
  Function IsNext(String ...$Args)
  {
    $Token=$this->PreView();
    If(!In_Array($Token?->Text?? '', $Args, True))
      Return False;
    $this->Next();
    Return True;
  }
  
  Function Parse()
  {
    $Vars=New TValue();
    $Vars->MakeMap($this->PreView()?? $this->Tokens);
    While(1)
    {
      If(!$this->ParseMapItem($Vars)) 
        Return $Vars; // Error
      If(!$this->IsNext(',', ';'))
        Break;
    }
    
  //Log('Debug', 'Parsed: ', $Vars);
    
    Return $Vars;
  }
  
  Function ParseMapItem($Vars):Bool
  {
    $Key=New TValue();
    If(!$this->ParseKey($Key)) Return False;
    $Token=$this->Next();
    Switch($Text=$Token?->Text?? '')
    {
    Case '.'  : Return $this->ParseMapItem ($Vars->KeyMap  ($Key, $Token));
    Case '{'  : Return $this->ParseMap     ($Vars->KeyMap  ($Key, $Token));
    Case '['  : Return $this->ParseList    ($Vars->KeyList ($Key, $Token));
    Case '=>' :
    Case '='  :
    Case ':'  : Break;
    Default   : Return $this->Error('Expected ".", "=", ":", "{" or "=>", given ',$Text)->Ret(False);
    }
    Return $this->ParseValue($Vars->MakeKey($Key));
  }
  
  Function ParseMap($Vars)
  {
    If($this->IsNext('}')) Return True;
    While(1)
    {
      $R=$this->ParseMapItem($Vars);
      If($R===False) Return False;
      Switch(($Token=$this->Next())?->Text?? '')
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
  
  Function ParseList($Vars)
  {
    If($this->IsNext(']')) Return True;
    While(1)
    {
      $Value=New TValue();
      If(!$this->ParseValue($Value)) Return False;
      $Vars[]=$Value;
      
      Switch(($Token=$this->Next())?->Text?? '')
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
  
  Function ParseKey($Vars)
  {
    $Token=$this->Next();
    If($Token===Null) Return False;
    Switch($Token->Id)
    {
    Case T_LNUMBER:
    Case T_DNUMBER:
    Case T_CONSTANT_ENCAPSED_STRING:
      $Vars->SetValue(Eval('Return '.$Token->Text.';'), $Token);
      Break;
    Default:
      If($Token->IsWord()) { $Vars->SetValue($Token->Text, $Token); Break; }
      Return $this->Error('Unknown Key token: ', $Token)->Ret(False);
    }
    Return True;
  }
  
  Function ParseValue($Vars, $WordAllow=False)
  {
    $Token=$this->Next();
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
      Case 'nan'   : $Value=NAN   ; Break;
      Case 'inf'   : $Value=INF   ; Break;
      Case 'debugpos' : $Value=$this->Debug('DebugPos')->Ret(True); Break;
      Case '{': Return $this->ParseMap  ($Vars->MakeMap  ($Token));
      Case '[': Return $this->ParseList ($Vars->MakeList ($Token));
      Default:
        If($WordAllow && $Token->IsWord()) { $Value=$Token->Text; Break; }
        Return $this->Error('Unknown Value token: ', $Token)->Ret(False);
      }
    }
    $Vars->SetValue($Value ,$Token); 
    Return True;
  }
  
  Function ParseVarsMap($Vars)
  {
    While(1)
    {
      $this->_Parse($Vars);
      $Token=$this->Next();
      If(!$Token) Return;
      If($Token->Text===',') Continue;
      If($Token->Text==='}') Return True;
    }
    $this->Error('Expected , or } but taken: ',$Token->Text)->Ret();
  }
  
  Function ParseVars($Vars)
  {
    $Token=$this->Next();
    If(!$Token) Return;
    If($Token->Text==='=') Return $this->ParseValue($Vars);
    If($Token->Text==='{') Return $this->ParseVarsMap($Vars);
    If(!$Token->IsWord()) Return $this->Error('Unknown word: ',$Token->Text)->Ret(False);
    Return $this->_Parse($Vars[$Token->Text]);
  }
}
