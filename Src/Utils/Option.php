<?
NameSpace Reformat\Utils;

Use Function Reformat\Log;

Use Function Reformat\Utils\Token\{
  TokenizeCode      ,
  RemoveComments    ,
  RemoveWhiteSpaces ,
};
Use Function Reformat\Utils\Stream\{
  Next   ,
  Skip   ,
  Filter ,
  Map    ,
};

Class TOption
{
  Var        $Tokens    ;
  Var        $LastToken ;
  Var Bool   $HasToken  ;
  Var String $File      ;
  Var Int    $Line      ;
  Var Int    $Pos       ;
  Var Bool   $HasError  =False;
  
  Function __Construct($Line=0, $Pos=0, $File='Test')
  {
    $this->File =$File ;
    $this->Line =$Line ;
    $this->Pos  =$Pos  ;
  }
  
  Function NextToken(Bool $Next=True)
  {
    Return $this->SafeNextToken($Next)?? $this->Error('Unexpected end or option')->Ret();
  }
  
  Function SafeNextToken(Bool $Next=True)
  {
    $Res=$Next? Next($this->Tokens):$this->Tokens->Current();
    $this->HasToken=(Bool)$Res;
    If($Res)
      $this->LastToken=$Res;
    Return $Res;
  }
  
  Function Warning (...$Args) { Return $this->Log('Warning' ,...$Args); }
  Function Error   (...$Args) { Return $this->Log('Error'   ,...$Args); }
  Function Log(String $LogLevel, ...$Args)
  {
    $this->HasError=True;
  
    $Line =$this->Line ;
    $Pos  =$this->Pos  ;
    If($Token=$this->LastToken)
    {
      $Line +=$Token->Line-1;
      $Pos  +=$Token->Pos   ;
      If(!$this->HasToken)
        LinePos($Token->Text, $Line, $Pos);
    }
    Return Log($LogLevel, ...$Args)->File($this->File, $Line, $Pos);
  }
  
  Function CheckWord($Token): False|String
  {
    If(!$Key->IsWord()) Return $this->Error('Unknown word: ',$Token->Text)->Ret(False);
    Return $Token->Text;
  }
  
  Static $Test=[
    'Option'=><<<'HereDoc'
      Path.Class.Field{P1=True, P2={S1="Helo\n",S2=4,S3=False,S4='Hello\n',S5=[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2],S6={k1=2}, S6.k2:3.14, S7{k:1}, S8[2]}
      HereDoc,
    'Result'=>
      ['Path'=>['Class'=>['Field'=>['P1'=>true,'P2'=>[
        'S1'=>"Helo\n",
        'S2'=>4,
        'S3'=>False,
        'S4'=>'Hello\n',
        'S5'=>[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2],
        'S6'=>['k1'=>2, 'k2'=>3.14],
        'S7'=>['k'=>1],
        'S8'=>[2]
      ]]]]],
  ];

  Function Parse($Text)
  {
    $Tokens=TokenizeCode($Text);
    
  //RemoveComments    ($Tokens);
  //RemoveWhiteSpaces ($Tokens);
    $this->Tokens=$Tokens
      |> Filter(fn($Token)=>!$Token->Is(T_COMMENT, T_WHITESPACE));
    
    $Vars=[];
    While($this->ParseMapItem($Vars, [','=>True, ';'=>True, ''=>True])===Null)
    {
    }
    
    Return $Vars;
  }
  
  Function ParseMapItem(&$Vars, $End):?Bool
  {
    If(!Is_Array($Vars))
    {
      If($Vars!==Null)
        $this->Error('Map: Value has already: ', $Vars);
      $Vars=[];
    }
    $Token=$this->NextToken(False);
    If($End[$Token?->Text?? '']?? False) Return Null;
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
    Default   : Return $this->Error('Excepted ".", "=", ":", "{" or "=>", given ',$Text)->Ret(False);
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
      Default: Return $this->Error('Unkbown token ', $Token)->Ret(False);
      }
    }
    Return True;
  }
  
  Function ParseList(&$Vars)
  {
    $Res=[];
    While(1)
    {
      $Token=$this->NextToken(False);
      If($Token===']') Break;
      If($Token===',') $this->NextToken(); //TODO: Always ,
      $Value=Null;
      If(!$this->ParseValue($Value)) Return False;
    }
    $Vars=$Res;
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
      $Vars=Eval($Token->Text);
      Break;
    Default:
      If($Token->IsWord()) { $Vars=$Token->Text; Break; }
      Return $this->Error('Unknown token: ', $Token)->Ret(False);
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
      $Value=Eval($Token->Text.';');
      Break;
    Default:
      Switch(StrToLOwer($Token->Text))
      {
      Case 'null'  : $Value=Null  ; Break;
      Case 'true'  : $Value=True  ; Break;
      Case 'false' : $Value=False ; Break;
      Case '{': Return $this->ParseMap($Vars);
      Case '[': $Value=Null; If(!$this->ParseList($Value)) Return False; Break;
      Default:
        If($WordAllow && $Token->IsWord()) { $Value=$Token->Text; Break; }
        Return $this->Error('Unknown token: ', $Token)->Ret(False);
      }
    }
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
