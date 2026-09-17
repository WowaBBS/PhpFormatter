<?
namespace Reformat\Filter;
use function Reformat\Log;

class TTranslate Extends TBase
{
  Static Function GetName() { Return 'Translate'; }
  
  Var $NeedToTranslate=[];
  Var $FileName='.Translator.php';

  Static Function IsApplicable($FileName, $Config)
  {
    If(RealPath($FileName)===RealPath('.Translator.php')) //TODO: FileName from config
      Return False;
    Return True;
  }
  
  Function Start()
  {
    Parent::Start();
    $this->NeedToTranslate=$this->LoadFile();
  }
  
  Function Finish()
  {
    Parent::Finish();
    $this->SaveFile();
    $Res=$this->LoadFile();
    If($Res!==$this->NeedToTranslate)
      Log('Error', 'Cant save translate file')->Debug([
        'Desired' =>$this->NeedToTranslate,
        'Actual'  =>$Res,
      ]);
  }
  
  Function LoadFile()
  { //TODO: Optimize: Loading not for each file
    If(!Is_File($this->FileName)) Return [];
    
  //$Res=Include $this->FileName;
    $FileData=File_Get_Contents($this->FileName);
    $Res=Eval(SubStr($FileData, 2));
    If(Is_Array($Res)) //TODO: Verify result
      Return $Res;
    
    Log('Error', 'Wrong translater file')->Debug($Res);
    Return [];
  }
  
  Function SaveFile()
  {
    If(!$this->NeedToTranslate)
    {
      @UnLink($this->FileName);
      Return;
    }
    $Res=['<? Return ['];
    ForEach($this->NeedToTranslate As $k=>$v)
    {
      $Res[]='<<<\'TranslateFrom\'';
      $Res[]=$k;
      $Res[]='TranslateFrom';
      $Res[]='=>';
      $Res[]='<<<\'TranslateTo\'';
      $Res[]=$v;
      $Res[]='TranslateTo';
      $Res[]=',';
    }
    $Res[]='];';
    $Res=Implode("\n", $Res);
    File_Put_Contents($this->FileName, $Res);
  //ClearStatCache(true, RealPath($this->FileName));
  }
  
  Function Process($Token)
  {
    if(!preg_match('/[\x80-\xFF]/', $Token->text)) Return;
    $Text=$this->NeedToTranslate[$Token->text]?? $Token->text;
    If($Text!==$Token->text) Return;
    $this->NeedToTranslate[$Token->text]??=$Token->text;
  //Log('Debug', 'Found: ', $Token->text); //->Debug($this->NeedToTranslate);
  }
}
