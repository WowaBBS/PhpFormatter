<?
namespace Reformat\Filter;
use function Reformat\Log;

$traslateUsingIn??=Null;

class TTranslate Extends TBase
{
  Static Function GetName() { Return 'Translate'; }
  
  Var $IsActive=True;
  Var $NeedToTranslate=[];
  Var $TranslateFileName='.Translator.php';
  Var $CurrentFile='';
  Var $UsedIn=[];
  Var $MyComment='//PHPFormatter: Translate file';

  Function FileStart()
  {
    $this->CurrentFile=$this->GetSource()->ShortPath;
    $this->IsActive=
      RealPath($this->GetSource()->FilePath)!==
      RealPath($this->TranslateFileName); //TODO: FileName from config
  }
  
  Function CodeStart()
  {
    Parent::CodeStart();
    $this->NeedToTranslate=Self::LoadFile();
  }
  
  Function CodeFinish()
  {
    Parent::CodeFinish();
    Self::SaveFile();
    $Res=Self::LoadFile();
    If($Res!==$this->NeedToTranslate)
      Log('Error', 'Cant save translate file')->Debug([
        'Desired' =>$this->NeedToTranslate,
        'Actual'  =>$Res,
      ]);
  }
  
  Function LoadFile()
  { //TODO: Optimize: Loading not for each file
    If(!Is_File($this->TranslateFileName)) Return [];
    
    $FileData=File_Get_Contents($this->TranslateFileName);
    $Res=Eval(SubStr($FileData, 2));
    If(!Is_Array($Res)) Return Log('Error', 'Wrong translater file')->Debug($Res)->Res([]);
    $Convert=[];
    ForEach($Res As $k=>$v)
    {
      If(Is_String($k) && Is_String($v))
      { //Old format
        $Convert[$k]=$v;
        Continue;
      }
      ElseIf(Is_Int($k) && Is_Array($v) && Count($v)===2 && Is_String($v[0]) && Is_String($v[1]))
      { //New format
        $Convert[$v[0]]=$v[1];
        Continue;
      }
      Log('Error', 'Wrong format')->Debug([$k=>$v]);
    }
    Return $Convert;
  }
  
  Function SaveFile()
  {
    If(!$this->NeedToTranslate)
    {
      @UnLink($this->TranslateFileName);
      Return;
    }
    $Res=['<? Return ['.$this->MyComment];
    ForEach($this->NeedToTranslate As $k=>$v)
    {
      $UsedIn=[];      
      ForEach($this->UsedIn[$k]?? [] As $Item)
        If(Is_String($Item))
          $UsedIn[]=$Item;
      If($UsedIn)
        $UsedIn='// ---------------- '.Implode(', ', $UsedIn);
      Else
        $UsedIn='// UNUSED ********************';
      
      $Res[]='['.$UsedIn;
      $Res[]='<<<\'TranslateFrom\'';
      $Res[]=$k;
      $Res[]='TranslateFrom,';
      $Res[]='<<<\'TranslateTo\'';
      $Res[]=$v;
      $Res[]='TranslateTo],';
    }
    $Res[]='];';
    $Res=Implode("\n", $Res);
    File_Put_Contents($this->TranslateFileName, $Res);
  }
  
  Function ProcessText($Token)//:Void|String
  {
    If(!$this->IsActive) Return;
    If($Token->Id===T_COMMENT && $Token->Text===$this->MyComment)
    { //Skip my file
      $this->IsActive=False;
      Return;
    }
    If(!Preg_Match('/[\x80-\xFF]/', $Token->Text)) Return;
    $Text=$this->NeedToTranslate[$Token->Text]?? $Token->Text;
    $this->NeedToTranslate[$Token->Text]??=$Token->Text;
    
    $this->AddUsing($Token);
    If($Text!==$Token->Text) Return;
    Return $Text;
  }
  
  Function AddUsing($Token)
  {
    $UsedIn=&$this->UsedIn[$Token->Text];
    $UsedIn??=[];
    
    $FileName=$this->CurrentFile;
    $Line=$Token->Line; //TODO: Real line
    Switch($Token->Id)
    {
    Case T_COMMENT     : $Type='Rem'; Break;
    Case T_DOC_COMMENT : $Type='Doc'; Break;
    Default: $Type=UCWords(SubStr($Token->GetTokenName(), 2), ' _');
    }

    Global $traslateUsingIn;
    If($traslateUsingIn)
      If($traslateUsingIn($UsedIn, $FileName, $Line, $Type)===False)
        Return;

    $Key=$FileName.':'.$Line.$Type;
    If(IsSet($UsedIn[$Key])) Return;
    $UsedIn[$Key]=True;
    $UsedLine=&$UsedIn[$FileName];
    $UsedLine??=$FileName;
    //TODO: $Type
    If($Line>0)
      $UsedLine.=':'.$Line;
  //Log('Debug', 'Found: ', $Token->Text); //->Debug($this->NeedToTranslate);
  }
}
