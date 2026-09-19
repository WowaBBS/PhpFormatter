<?
namespace Reformat\Filter;

class TBase
{
  Static Function GetName() { Return 'Base'; }
  
  Static Function IsApplicable($Info, $Config) { return True; }

  Function FileStart($Info) {}
  
  Function ProcessAll($Tokens):Array|Null|False
  {
    $this->CodeStart();
    $Res=[]; //TODO: Prealloc?
    $Changed=False;
    $First=Null;
    ForEach($Tokens As $Token)
    {
      $First??=$Token;
    //$Text=$Token->Text; //TODO: Check the text is changed?
      $r=$this->Process($Token);
      If(Is_String($r))
      {
        If($Token->text!==$r)
        {
          $Token->text=$r;
          $r=$Token;
        }
        Else
          $r=Null;
      }
      If($r!==Null) $Changed=True;
      If($r===False) Continue;
      // TODO: Recalc row and column
      If($r===Null) { $Res[]=$Token; Continue; }
      If($r===True) { $Res[]=$Token; Continue; }
      If(Is_Object($r)) { $Res[]=$r; Continue; }
      If(Is_Array($r)) { Array_Push($Res, ...$r); Continue; }
      Log('Error', 'Unknown token process:')->Debug($r);
      Return False;
    }
    
    $r=$this->CodeFinish();
    If($r) $Changed=True;
  //If($r===true) // If changed some tokens offline
    If(Is_Object($r)) $Res[]=$r;
    If(Is_Array($r)) Array_Push($Res, ...$r);
    
    If(!$Changed) Return Null;
      
    $this->ReIndexLinePos($Res, $First);
    
    Return $Res;
  }
  
  Function ReIndexLinePos($Tokens, $First)
  { //TODO: Add Unicode support?
    IF(!$Tokens) Return;
    $Line     = $First->line ;
    $FirstPos = $First->pos  ;
    $Pos      = $FirstPos    ;
    ForEach($Tokens As $Token)
    {
      $Token->line =$Line ;
      $Token->pos  =$Pos  ;
      $Text=$Token->text;
      
      $i=StrRPos($Text, "\n");
      If($i!==False)
      {
        $Line+=SubStr_Count($Text, "\n");
        $Pos=$FirstPos-$i-1;
      }
      $Pos+=StrLen($Text);
    }
  }
  
  Function CodeStart()
  {
  }
  
  Function CodeFinish()
  {
  }
  
  Function Process($Token)//:Null|Object|Array|String
  {
  }
}