<?
NameSpace Reformat\Token;

Use Function Reformat\Log;
Use Function Reformat\Utils\Str     \{Starts_With_List,};
Use Function Reformat\Utils\StrList \{RemoveFirstLen,IsAllStartsWith,LTrimMinSpaces, };

// Key: TestComments
  
Class TComment Extends TText
{
  Var $Id =T_COMMENT;
  
  Var $Group=Null;
  
  Var $Start    ="/*\n" ;
  Var $LoopTab  =2;
  Var $Loop     =' * '  ;
  Var $EndCr    =True   ;
  Var $EndTab   =0      ;
  Var $End      =" */"  ;
  
  Var $OldTokens;
  
  Function __Construct($From, $GroupInlineComments=True)
  {
    Parent::__Construct($From->Id, '', $From->Line, $From->Pos);
    
    $To=$GroupInlineComments? $this->_FindEndComment($From): $From;
    $this->OldTokens=$From;
    $To->Insert($this);
    $From->_RemoveTo($To);
    $Text=$this->_Text_FromTo($From, $To);
    $this->SetText($Text);
  }
  
  Function _FindEndComment($Token)
  {
    $To=$Token;
    If(!($LeftOnlySpace=$Token->Tab===$Token->Pos)) Return $To;
    Switch($With=Starts_With_List($Token->Text, ['//', '#', '/*']))
    {
    Case '/*': Break;
    Case '#':
    Case '//':
      For($Item=$To->Next; $Item?->Is(T_WHITESPACE, T_COMMENT); $Item=$Item->Next)
        If($Item->Id===T_COMMENT)
        {
          If($Item->Pos!==$Token->Pos) Break;
          If(!Str_Starts_With($Item->Text, $With)) Break;
          If(!($LeftOnlySpace=$Item->Tab===$Item->Pos)) Break;
          $To=$Item;
        }
        Else
        {
          If(SubStr_Count($Item->Text, "\n")!==1) Break;
        }
      Break;
    Default:
      Log('Fatal', 'Unknown token')->Debug($Token);
      Return False;
    }
    Return $To;
  }
  
  Function _Text_FromTo($From, $To)
  {
    $Text=[];
    For($Item=$From, $End=$To->Next; $Item!=$End; $Item=$Item->Next)
      $Text[]=$Item->Text;
    Return Join($Text);
  }
  
  Function GetState() { Return [$this->Text, $this->Start, $this->LoopTab, $this->Loop, $this->EndCr, $this->EndTab, $this->End];    } //$this->Id,
  Function SetState($v) {      [$this->Text, $this->Start, $this->LoopTab, $this->Loop, $this->EndCr, $this->EndTab, $this->End]=$v; } //$this->Id,

//Function GetTypeHandler() { Return 'Text'; }
  
  Function SetText($v)
  {
    $this->_Detect($v);
    Parent::SetText($v);
  }
  
  Static Function Move_Right_Spaces_To_The_End(&$Line, &$End)
  {
    $LenRightSpaces=StrLen($Line)-StrLen(RTrim($Line, ' '));
    If(!$LenRightSpaces) Return;
    $End=Str_Repeat(' ', $LenRightSpaces).$End;
    $Line=SubStr($Line, 0, $LenRightSpaces);
  }
  
  Function _Detect($Value)
  {
    $List=Explode("\n", $Value);
    $First=Array_Shift($List);
    Switch($Start=Starts_With_List($First, ['#', '//', '/*']))
    {
    Case '#'  :
    Case '//' :
      $SaveList=$List;
      $Tab=LTrimMinSpaces($List, $this->Pos)-$this->Pos;
      If($Tab)
        Log('Error', 'Detect Tab is wrong, Pos=',$this->Pos,', Tab=',$Tab)->Debug($SaveList);
      Array_UnShift($List, $First);
      If(!IsAllStartsWith($List, $Start)) Break; //Error
      RemoveFirstLen($List, StrLen($Start));
      $Tab2=LTrimMinSpaces($List);
      $Start.=Str_Repeat(' ', $Tab2);
      
      $this->Start    = $Start ;
      $this->LoopTab  = $Tab   ;
      $this->Loop     = $Start ;
      $this->EndCr    = False  ;
      $this->EndTab   = 0      ;
      $this->End      = ''     ;
      Return;
    Case '/*'  :
      $Start=SubStr($First, 0, StrLen($First)-StrLen(LTrim(SubStr($First, 1), '*'))+1);
      $First=SubStr($First, StrLen($Start));
      $HasFirst=StrLen(Trim($First, ' '))>0;
      If(!$HasFirst && $List)
        $Start.=$First."\n";
      $End='*/';
      If(!$List) // In line
      {
        $HasEnd=Str_Ends_With($First, $End);
        If($HasEnd)
          $First=SubStr($First, 0, -2);
        Else
          $End='';
        
        $HasBody=Trim($First, ' ')!=='';
        If(!$HasBody)
          Switch(StrLen($First))
          {
          Case 0: Break;
          Case 1: $Start.=' '; Break;
          Case 2: $Start.=' '; $End=' '.$End; Break;
          }
        Else
          Self::Move_Right_Spaces_To_The_End($First, $End);

        $this->Start   = $Start ;
        $this->LoopTab =     0  ;
        $this->Loop    = ''     ; //TODO:?
        $this->EndCr   = False  ;
        $this->EndTab  =     0  ;
        $this->End     = $End   ;
        Return;
      }

      $Last=Array_Pop($List);
      
      $HasEnd=Str_Ends_With($Last, $End);
      If($HasEnd)
        $Last=SubStr($Last,0, -2);
      Else
        $End='';
      
      $HasLast=!$HasEnd || StrLen(Trim($Last, ' '))>0;
      If($HasLast)
      {
        Self::Move_Right_Spaces_To_The_End($Last, $End);
        Array_Push($List, $Last);
      }
      
      $Tab=LTrimMinSpaces($List, $this->Pos)-$this->Pos;
      $EndTab=$HasLast? 0:StrLen($Last)-$this->Pos-$Tab;

      If(IsAllStartsWith($List, '* '))
      {
        RemoveFirstLen($List, 2);
        $Tab2=LTrimMinSpaces($List);
        $Loop='* '.Str_Repeat(' ', $Tab2);
      }
      Else
        $Loop='';

      $this->Start    = $Start  ;
      $this->LoopTab  = $Tab    ;
      $this->Loop     = $Loop   ;
      $this->EndCr    =!$HasLast;
      $this->EndTab   = $EndTab ;
      $this->End      = $End    ;
      Return;
    Default:
      
    }
    Return Log('Fatal', 'Wrong comment starts with: ', $Start)($Value)->Ret();
  }
  
  Function GetInnerText():String
  {
    $Res=$this->Text;
    //TODO: Starts with Start?

    $Res=SubStr($Res, StrLen($this->Start ));

    If($this->End!=='')
      $Res=SubStr($Res, 0, -StrLen($this->End   ));

    $Res=Explode("\n", $Res);
    $Skip=$this->LoopTab+$this->Pos+StrLen($this->Loop);
    $j=StrPos($this->Start, "\n")!==False? 0:1;
    ForEach($Res As $i=>&$v)
      If($i>=$j)
        $v=SubStr($v, $Skip);
    If($this->EndCr) Array_Pop($Res);
    $Res=Implode("\n", $Res);

    Return $Res;
  }
  
  Function GetInnerPos()
  {
    $FilePos=$this->GetFilePos();
    $LoopPos=StrLen($this->Loop)+$this->LoopTab+$this->Pos;
    $Height=SubStr_Count($this->Start, "\n");
    Return $FilePos->Add(
      $Height,
      $Height? $LoopPos:StrLen($this->Start),
      $LoopPos,
    );
  }
  
  Function SetInnerText($v)
  {
    $v=Explode("\n", $v);
    $Space=Str_Repeat(' ', $this->LoopTab+$this->Pos);
    $Split="\n".$Space.$this->Loop;
    $End=$this->End;
    If($this->EndCr)
      $End="\n".Str_Repeat(' ', $this->Pos+$this->LoopTab+$this->EndTab).$End;
    $Start=$this->Start;
    If(StrPos($Start, "\n")!==False)
      $Start.=$Space.$this->Loop;
    $v=$Start.Implode($Split, $v).$End;
    $this->SetText($v);
  }
  
//****************************************************************
// Test

  Function TestInnerText()
  {
    $Desired=$this->GetState();
    $InnerText=$this->GetInnerText();
    $this->SetInnerText($InnerText);
    $Actual=$this->GetState();
    $this->SetState($Desired);
    
    If($Actual===$Desired) Return True;

    $DesiredText = Array_Shift($Desired );
    $ActualText  = Array_Shift($Actual  );
  
    $Log=Log('Error', 'TestInnerText Failed')->File($this->GetFilePos()->ToArgs());
    If($Actual!==$Desired)
      $Log('  Desired :', $Desired )
          ('  Actual  :', $Actual  );
    Else
      $Log('  State   :', $Actual  );
    If($ActualText!==$DesiredText)
      $Log('Desired.Text:' )($DesiredText )
          ('Actual.Text:'  )($ActualText  )
          ('Inner.Text:'   )($InnerText   );
    Return False;
  }
  
  Function GetCheckInfo()
  {
    $Res=[$this->Start];
    
    If($this->LoopTab)
      $Res[]=$this->LoopTab;
    
    If($this->Loop && (Count($Res)!==1 || $this->Loop!==$Res[0]))
      $Res[]=$this->Loop;
    
    If($this->EndCr)
      $Res[]=$this->EndTab;
    
    If(StrLen($this->End))
      $Res[]=Str_Replace('*/','*\/', $this->End); //Slashes for tesy
    
    If(Count($Res)===1)
      $Res=$Res[0];
    
    Return $Res;
  }
  
  Function CheckDetect()
  {
    $Starts='Check=';
    
    $Text=$this->GetInnerText();
    $Text=Explode("\n", $Text);
    $Found=Null;
    $FoundLine=0;
    ForEach($Text As $Idx=>$Line)
      If(($FoundPos=StrPos($Line, $Starts))!==False)
      {
        $Found=SubStr($Line, $FoundPos);//+StrLen($Starts));
        $FoundLine=$Idx;
        Break;
      }
    If($Found===Null)
      Return Log('Debug', 'Check not found')->File($this->GetFilePos()->ToArgs())->Ret();
      
    $Name=$Text[0];
    If($FoundLine===0)
      $Name=SubStr($Name, 0, $FoundPos);
    $Name=Trim($Name);
      
    $Pos=$this->GetInnerPos()->Add($FoundLine, $FoundPos);
    $Parser=New \Reformat\Option\TParser($Found, $Pos);
    $Desired=$Parser->Parse();

    If($Desired===Null)
      Return Log('Error', 'Check decode falied: ', $Found)->File($this->GetFilePos()->ToArgs())->Ret();
    $Desired=$Desired['Check']->GetValue();
    
    $Actual  =$this->GetCheckInfo();
    If($Desired===$Actual) Return; //Ok
    Log('Error', 'Check comment falied: ', $Name)->File($this->GetFilePos()->ToArgs())
      ('Actual  : ',$Actual  )
      ('Desired : ',$Desired );
  }
//****************************************************************
}