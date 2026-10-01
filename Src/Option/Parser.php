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
  
  Function _SafeNext()
  {
    $Res=$this->PreView();
    $this->Log_FilePos($Res?? $this->Tokens->GetFilePosEnd());
    $this->NextToken=$Res?->Next;
    Return $Res;
  }
  
  Function Next()
  {
    Return $this->_SafeNext()?? $this->Error('Unexpected end of option')->Ret();
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
    $Vars=$this->NewValue();
    $Vars->SetUsed();
    $Vars->Parser_MakeMap($this->PreView()?? $this->Tokens, False);
    
  //If($this->IsNext('(')) $this->ParseArray ($Vars, ')'); Else //TODO: ?
    If($this->IsNext('[')) $this->ParseArray ($Vars, ']'); Else //TODO: Ini file like, now PhpLike
    If($this->IsNext('{')) $this->ParseArray ($Vars, '}'); Else
      While(1)
      {
        If(!$this->ParseArrayItem($Vars))
          Return $Vars; // Error
        If(!$this->IsNext(',', ';'))
          Break;
        If(!$this->Preview())
          Break;
      }
      
    If($Rest=$this->Preview())
      $this->Warning('Has unparsed data: ', $Rest->Text)->File($Rest->GetFilePos()->ToArgs());
    
  //Log('Debug', 'Parsed: ', $Vars);
    //TODO: Only if debug
    $Vars->Verify();
    Return $Vars;
  }
  
  Function ParseArrayItem($Vars, $End=']'):Bool
  {
    $Path=$this->ParsePath($Vars);
  //Log('Debug', 'ParseArrayItem.Path: ', $Path);
    Switch(($Token=$this->PreView())?->Text?? '')
    {
    Case '{': //Merge mode
      $this->Next();
      $Vars=$Vars->Parser_MakePath($Path);
      $Vars->Parser_MakeMap  ($Token, True); 
      Return $this->ParseArray($Vars, '}');
    Case '=>': //Is really key
    Case ':':
    Case '=':
      $this->Next();
    //Log('Debug', 'KeyToValue: ', $Token);
      Break; // The next is a Value
    Case ';': //Is Value
    Case ',':
    Case $End:
      If(Count($Path)!==1)
        Return $this->Error('Array: Value ',New TDebug($Path), ' like a key path')->Ret(False);
      $Vars->Parser_AddItem($Path[0]); //TODO: Check $
      Return True;
    Case '': Return False;
    Default: Return $this->Error('ArrayItem: Excepted "=>", ":", "=", "{", ":", "." or "', $End, '", given "', $Token, '"')->File($Token->GetFilePos()->ToArgs())->Ret(False);
    }
    
    $Vars=$Vars->Parser_MakePath($Path);
    $Value=$this->ParseValue($Vars);
    If(!$Value) Return False;
    Return True;
  }
  
  Function ParseArray($Vars, $End=']')
  {
    If($this->IsNext($End)) Return True;
    While(1)
    {
      If(!$this->ParseArrayItem($Vars, $End)) Return False;
      
      Switch(($Token=$this->Next())?->Text?? '')
      {
      Case '': Return False;
      Case ';':
      Case ',': Break;
      Case $End: Return True;
      Default: Return $this->Error('Array: Excepted ":", "." or "', $End, '", given "', $Token?->Text, '"')->Ret(False);
      }
      If($this->IsNext($End)) Return True; //,] Or ;]
    }
    Return True;
  }
  
  Function ParseKey($Parent, $ForNext=False):?TValue { Return $this->ParseValue($Parent->NewValue(), True, $ForNext); }
  
  Function ParsePath($Parent):?Array
  {
    $Item=$this->ParseKey($Parent);
  //Log('Debug', 'ParsePath.First=', $Item);
    If(!$Item) Return Null;
    $Res=[$Item];
    While(1)
    {
      $Token=$this->PreView();
    //Log('Debug', 'ParsePath.Token: ', $Token);
      Switch($Token?->Text?? '')
      {
      Case '.'  :
      Case '\\' :
      Case '::' :
      Case '/'  :
      Case '->' :
        $this->Next();
        $Item=$this->ParseKey($Item, True);
        If(!$Item) Return Null;
      //Log('Debug', 'ParsePath.Add: ', New TDebug($Item));
        $Res[]=$Item;
        Break;
      Case '[':
        $this->Next();
        $Item=$Item->NewValue();
        $Item->Parser_MakeList($Token);
        If(!$this->ParseArray($Item, ']')) Return Null;
        If($Item->Count()!==1 || !$Item->Has(0))
          Return $this->Error('ParsePath: Unknown key ',$Item, ' for path ', $Item->GetPath())
            ->File($Token->GetFilePos()->ToArgs())->Ret();
        $Item=$Item->Value[0]; //TODO: Parser_GetPathItem()
        If(!$Item) Return Null;
      //Log('Debug', 'ParsePath.Add[]: ', New TDebug($Item));
        $Res[]=$Item;
        Break;
    //Case ':': Case '=': Case '{': Case '=>': Break 2;
      Default:
      //Return $this->Error('ParsePath: Unknown key ',$Item, ' for path ', $Item->GetPath())
      //  ->File($Token->GetFilePos()->ToArgs())->Ret();
        Break 2;
      }
    }
  //Log('Debug', 'ParsePath: ', New TDebug($Res));
    Return $Res;
  }
  
  Function _ParseNumeric():False|Ind|Float
  {
    $Token=$this->Next();
    If($Token===Null) Return False;
    Switch($Token->Id)
    {
    Case T_LNUMBER:
    Case T_DNUMBER:
      Return Eval('Return '.$Token->Text.';');
    Default:
      Switch(StrToLower($Token->Text))
      {
      Case 'nan': Return NAN; Break;
      Case 'inf': Return INF; Break;
      }
    }
    $this->Error('Number expected, given: ', $Token->Text)->Ret(False);
    Return False;
  }
  
  Function ParseValue(TValue $Res, $ForKey=False, $ForNextKey=False, $Merge=!False):?TValue
  {
  //$Res=$Parent->NewValue();
    $Token=$this->Next();
  //Log('Debug', 'ParseValue.Token: ', $Token);
    If($Token===Null) Return Null;
    Switch($Token->Id)
    {
    Case T_VARIABLE:
      // TODO: If(!$ForKey) Unknown token
      $Value=SubStr($Token->Text,1);
      Break;
    Case T_LNUMBER:
    Case T_DNUMBER:
    Case T_CONSTANT_ENCAPSED_STRING:
    //Log('Debug', 'Eval ',$Token->Text);
      $Value=Eval('Return '.$Token->Text.';');
      Break;
    Default:
      If($ForNextKey && $Token->IsWord()) { $Value=$Token->Text; Break; }
      Switch(StrToLower($Token->Text))
      {
      Case '-'     : 
        $Value=$this->_ParseNumeric(); 
        If($Value===False) Return Null;
        $Value=-$Value;
        Break;
      Case 'null'  : $Value=Null  ; Break;
      Case 'true'  : $Value=True  ; Break;
      Case 'false' : $Value=False ; Break;
      Case 'nan'   : $Value=NAN   ; Break;
      Case 'inf'   : $Value=INF   ; Break;
      Case 'debugpos' : $Value=$this->Debug('DebugPos')->Ret(True); Break;

      Case '{': $Res->Parser_MakeMap  ($Token, $Merge); Return $this->ParseArray($Res, '}')? $Res:Null;
      Case '[': $Res->Parser_MakeList ($Token, $Merge); Return $this->ParseArray($Res, ']')? $Res:Null;
    //Case '(': $Res->Parser_MakeList ($Token, $Merge); Return $this->ParseArray($Res, ')')? $Res:Null;
      Default:
        If($ForKey && $Token->IsWord()) { $Value=$Token->Text; Break; }
        Return $this->Error('ParseValue: Unknown token: ', $Token->Text)->Ret();
      }
    }
  //Log('Debug', 'ParseValue=', $Value);
    $Res->Parser_SetValue($Value ,$Token);
    Return $Res;
  }
  
  Function NewValue()
  {
    $Res=New TValue();
    $Res->Parser=$this;
    Return $Res;
  }
}
