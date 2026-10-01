<?
NameSpace Reformat\Token;

Use Function Reformat\Log;
Use Function Reformat\Utils\Str     \{Starts_With_List,};
Use Function Reformat\Utils\StrList \{RemoveFirstLen,IsAllStartsWith,LTrimMinSpaces, };

Class TComment Extends TText
{
  Var $Id =T_COMMENT;
  
  Var $Group=Null;
  
  Var $InnerTab =2;
  Var $Start    ="/*\n" ;
  Var $Loop     =' * '  ;
  Var $EndCr    =True   ;
  Var $End      =" */"  ;
  
  Function __Construct($From, $To)
  {
    Parent::__Construct($From->Id, $From->Text, $From->Line, $From->Pos);
  }
  
  Function GetState() { Return [$this->State, $this->Id, $this->Start, $this->Loop, $this->EndCr, $this->End, $this->InnerTab]; }
  Function SetState($v) {      [$this->State, $this->Id, $this->Start, $this->Loop, $this->EndCr, $this->End, $this->InnerTab]=$v; }

//Function GetTypeHandler() { Return 'Text'; }
  
  Function SetText($v)
  {
    $this->_Detect($v);
    Parent::SetText($v);
  }
  
  Function _Detect($Value)
  {
    $List=Explode("\n", $Value);
    $First=Array_Shift($List);
    $Tab=LTrimMinSpaces($List, $this->Tab)-$this->Tab;
    Switch($With=Starts_With_List($First, ['#', '//', '/***', '/**', '/*']))
    {
    Case '#'  :
    Case '//' :
      Array_UnShift($List, $First);
      If(!IsAllStartsWith($List, $With)) Break; //Error
      RemoveFirstLen($List, StrLen($With));
      $Tab+=LTrimMinSpaces($List);
      
      $this->InnerTab  =$Tab ;
      $this->Start     =$With;
      $this->Loop      =$With;
      $this->EndCr     =False;
      $this->End       =''   ;
      Return;
    Case '/***':
      $With=SubStr($First, 0, StrLen(LTrim(SubStr($First,1), '*'))+1);
    Case '/**' :
    Case '/*'  :
      $First=SubStr($First, StrLen($With));
      $HasFirst=StrLen(Trim($First))>0;
      $Last=Array_Pop($List);
      $HasEnd=Str_Ends_With($Last, '*/');
      If($HasEnd)
        $Last=SubStr($Last,0, -2);
      $HasLast=StrLen($Last)>0;
      
      If(IsAllStartsWith($List, '* '))
      {
        RemoveFirstLen($List, 2);
        $Tab2=LTrimMinSpaces($List);
        $Loop='* '.Str_Repeat(' ', $Tab2);
      }
      Else
        $Loop='';

      $this->InnerTab  =$Tab ;
      $this->Start     =$With.($HasFirst? '':"\n");
      $this->Loop      =$Loop;
      $this->EndCr     =!$HasLast;
      $this->End       =$HasEnd? '*/' :'';
      Return;
    Default:
      
    }
    Return Log('Fatal', 'Wrong comments')->Debug($Value)->Ret();
  }
  
  Function GetInnerText():String
  {
    $v=$this->Text;
    //TODO: Starts with Start?
    $v=SubStr($v, StrLen($this->Start ));
    If($this->End!=='')
      $v=SubStr($v, 0, -StrLen($this->End   ));
    Return $v;
  }
  
  Function SetInnerText($v)
  {
    $v=Explode("\n", $v);
    $Space=Str_Repeat(' ', $this->InnerTab+$this->Tab);
    $Split="\n".$Space.$this->Loop;
    $End=$this->End;
    If($this->EndCr)
      $End=$Space."\n".$End;
    $v=$this->Start.Implode($Split, $v).$End;
    $this->SetText($v);
  }
  
  Function TestInnerText()
  {
    $Desired=$this->GetState();
    $InnerText=$this->GetInnerText();
    $this->SetInnerText($InnerText);
    $Actual=$this->GetState();
    $this->SetState($Desired);
  
    If($Actual===$Desired) Return True;
    Log('Error', 'TestInnerText Failed')->Debug([
      'Desired' => $Desired ,
      'Actual'  => $Actual  ,
    ]);
    Return False;
  }
}