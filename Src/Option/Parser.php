<?
NameSpace Reformat\Option;

Use Function Reformat\Log;
Use Reformat\FilePos\TInfo     As TFilePos;
Use Reformat\FilePos\IProvider As IFilePos;
Use Function Reformat\Utils\Token\TokenizeCode;

Class TParser
{
  Var IFilePos $FilePos   ;
  Var          $Tokens    ;
  Var          $NextToken ;

  Function GetFilePos() { Return $this->FilePos?->GetFilePos()?? TFilePos::GetEmpty(); }
  
  Function __Construct(String $Text, ?IFilePos $FilePos=Null)
  {
    $this->FilePos = $FilePos;
    $Tokens=TokenizeCode($Text, $FilePos);
    
    $this->Tokens    =$Tokens;
    $this->NextToken =$Tokens->First;    
  }
  
  // Returns next token is not Comment or WhiteSpace
  Function PreView()
  {
    $Res=$this->NextToken;
    
    While($Res?->Is(T_COMMENT, T_WHITESPACE))
      $Res=$Res->Next;
      
    Return $Res;
  }
  
  Function _SafeNext()
  {
    $Res=$this->PreView();
    $this->FilePos   =$Res?? $this->Tokens->GetFilePosEnd();
    $this->NextToken =$Res?->Next;
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
    $Vars=New TValue();
    $Vars->Parser=$this;
    
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
    
    //TODO: Only if debug
    $Vars->Verify();
    Return $Vars;
  }
  
  Function ParseArrayItem($Vars, $End=']'):Bool
  {
    $Path=$this->ParsePath($Vars);
    If($Path===Null) Return False;
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
    If(!$Item) Return Null;
    $Res=[$Item];
    While(1)
    {
      $Token=$this->PreView();
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
        $Res[]=$Item;
        Break;
      Case '[':
        $this->Next();
        $Item=$Item->NewValue();
        $Item->Parser_MakeList($Token, True);
        If(!$this->ParseArray($Item, ']')) Return Null;
        If($Item->Count()===0)
        { //Special for autoincrement key
          $Res[]=Null;
          Break;
        }
        If($Item->Count()!==1 || !$Item->Has(0))
          Return $this->Error('ParsePath: Unknown key ',$Item, ' for path ', $Item->GetPath())
            ->File($Token->GetFilePos()->ToArgs())->Ret();
        $Item=$Item->Value[0]; //TODO: Parser_GetPathItem()
        If(!$Item) Return Null;
        $Res[]=$Item;
        Break;
      Default:
        Break 2;
      }
    }
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
  
  Function ParseValue(TValue $Res, $ForKey=False, $ForNextKey=False, $Merge=False):?TValue
  {
    $Token=$this->Next();
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
    $Res->Parser_SetValue($Value ,$Token);
    Return $Res;
  }
  
//****************************************************************
// Log

  Var $Logger;

  Function Warning (...$Args) { Return $this->Log('Warning' ,...$Args); }
  Function Error   (...$Args) { Return $this->Log('Error'   ,...$Args); }
  Function Debug   (...$Args) { Return $this->Log('Debug'   ,...$Args); }

  Function Log(String $LogLevel, ...$Args)
  {
    Return Log($LogLevel, ...$Args)->Logger($this->Logger)->File($this->GetFilePos()->ToArgs());
  }
//****************************************************************
}
