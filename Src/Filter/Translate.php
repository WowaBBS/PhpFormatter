<?
namespace Reformat\Filter;

Use Reformat\Using\TDependent As TUsingDependent ;
Use Reformat\Using\TPoint     As TUsingPoint     ;
Use Reformat\Using\TPoints    As TUsingPoints    ;

Use Function Reformat\Log;

$traslateUsingIn??=Null;

/*
 * This filter allow to create translation map for strings and comments
 */
Class TTranslate Extends TBase
{
  Static Function GetName() { Return 'Translate'; }
  
  Var TUsingPoints $NeedToTranslate   ;
  Var              $TranslateFileName ='.Translator.php';
  Var              $CurrentFile       ='';
  Var              $UsedIn            =[];
  Var              $MyComment         ='//PHPFormatter: Translate file';

  Function Init($Source)
  {
    Parent::Init($Source);
    $this->NeedToTranslate=Self::LoadFile();
  }
  
  Function Dispose()
  {
    Self::SaveFile();
    $Res=Self::LoadFile();
    $Actual  =$Res                   ->ToCompare();
    $Desired =$this->NeedToTranslate ->ToCompare();
    If($Actual!==$Desired)
      Log('Error', 'Cant save translate file')->Debug([
        'Desired' =>$Desired ,
        'Actual'  =>$Actual  ,
      ]);
    Parent::Dispose();
  }
  
  
  Function FileStart()
  {
    $this->CurrentFile=$this->Source->ShortPath;
    $this->Enable=
      RealPath($this->Source->FilePath)!==
      RealPath($this->TranslateFileName); //TODO: FileName from config
  }
  
  Function CodeStart()
  {
    Parent::CodeStart();
  }
  
  Function CodeFinish()
  {
    Parent::CodeFinish();
  }
  
  Function LoadFile()
  {
    $Res=New TUsingPoints();
    If(!Is_File($this->TranslateFileName)) Return $Res;
    
    $FileData=File_Get_Contents($this->TranslateFileName);
    $List=Eval(SubStr($FileData, 2));
    If(!Is_Array($List)) Return Log('Error', 'Wrong translater file')->Debug($List)->Res($Res);
    ForEach($List As $k=>$v)
    {
      If(Is_String($k) && Is_String($v))
      { //Old format
        $Res[$k]=[$k, $v];
        Continue;
      }
      ElseIf(Is_Int($k) && Is_Array($v) && Count($v)===2)
      { //New format
        If(Is_String($v[0]?? 0) && Is_String($v[1]?? 0))
        {
          $Res[$v[0]]=[$v[0], $v[1]];
          Continue;
        }
        If(Is_String($v['From']?? 0) && Is_String($v['To']?? 0))
        {
          $Res[$v['From']]=[$v['From'], $v['To']];
          Continue;
        }
      }
      Log('Error', 'Wrong format')->Debug([$k=>$v]);
    }
    Return $Res;
  }
  
  Function SaveFile()
  {
    If($this->NeedToTranslate->IsEmpty())
    {
      @UnLink($this->TranslateFileName);
      Return;
    }
    $Res=['<? Return ['.$this->MyComment];
    ForEach($this->NeedToTranslate As $k=>$v)
    {
      $UsedIn=$v->Make_UsedIn();
      If($UsedIn)
        $UsedIn='// ---------------- '.Implode(', ', $UsedIn);
      Else
        $UsedIn='// UNUSED ********************';
      
      $Res[]='['.$UsedIn;
      $Res[]='<<<\'TranslateFrom\'';
      $Res[]=$v->Value[0];
      $Res[]='TranslateFrom,';
      $Res[]='<<<\'TranslateTo\'';
      $Res[]=$v->Value[1];
      $Res[]='TranslateTo],';
    }
    $Res[]='];';
    $Res=Implode("\n", $Res);
    File_Put_Contents($this->TranslateFileName, $Res);
  }
  
  Function ProcessText($Token):Void
  {
    $IsComment=$Token->Id===T_COMMENT;
    If($IsComment && $Token->Text===$this->MyComment)
    { //Skip my file
      $this->Enable=False;
      Return;
    }
    $Text=$Source=$IsComment? $Token->GetInnerText():$Token->Text;
    $OldText=$Text;
    If(!Preg_Match('/[\x80-\xFF]/', $Text)) Return;
    $Point=$this->NeedToTranslate->Add($Text, [$Text, $Text]);
    //TODO: Global $traslateUsingIn;
    $Point->Add(New TUsingDependent($Token));
    $Text=$Point->Value[1];
    If($Text!==$OldText) Return;
    If($IsComment)
      $Token->SetInnerText($Text);
    Else
      $Token->SetText($Text);
  }
}
