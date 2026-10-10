<?
NameSpace Reformat\Debug;

Use Reformat\Path\TTrace  As TPathTrace  ;
Use Reformat\Filter\TBase As TFilterBase ;
Use Function Reformat\Log;

Class TPath Extends TFilterBase
{
  Static Function GetName() { Return 'Debug\Path'; }
  
  Var Bool $CheckEvery =False ; //TODO: Enable for tests
  Var Bool $TestPath   =True  ;
  
  Function ProcessText($Token):Void
  {
    If($Token->GetTypeHandler()!=='Text') Return;
    If($Token->GetId()==='Option') Return;
    
    If($this->TestPath   ) $this->TestPath   ($Token);
    If($this->CheckEvery ) $this->CheckEvery ($Token);
  }
  
  Function CheckEvery($Token)
  {
    $Path=New TPathTrace();
    $DebugPath=$Path->MakeBracesPath($Token);
  }
  
  Function TestPath($Token)
  {
    If($Token->Id!==T_COMMENT && $Token->Id!==T_DOC_COMMENT) 
      $Text=$Token->Text;
    Else
      $Text=$Token->GetInnerText();
    $Key='TestPath=';
    $Pos=StrPos($Text, $Key);
    If($Pos===False) Return;
    $Desired=SubStr($Text, $Pos+StrLen($Key));
    $Desired=Trim(Explode("\n", $Desired)[0]);
    $PrevChar=$Text[$Pos-1]?? '';
    If($PrevChar==='\'' || $PrevChar==='"')
    {
      $End=StrPos($Desired, $PrevChar);
      If($End!==False)
        $Desired=SubStr($Desired, 0, $End);
    }
    
    $Path=New TPathTrace();
    $DebugPath=$Path->MakeBracesPath($Token);
    $File=$Token->GetFilePos()->ToArgs();
    
    $Actual=$DebugPath->ToString();
    If($Actual!==$Desired)
      Log('Error', 'TestPath is different:')->File($File)
        ('Desired : ', $Desired )
        ('Actual  : ', $Actual  ); //->Debug($DebugPath->ToDebug());
  //Else
  //  Log('Debug', 'DebugPath: ', $Desired)->File($File)->Debug($DebugPath->ToDebug());
  }

  Function Option_Do($Op)
  {
    Parent::Option_Do($Op);
    
    $this->CheckEvery =$Op->GetSet('CheckEvery' ,$this->CheckEvery );
    $this->TestPath   =$Op->GetSet('TestPath'   ,$this->TestPath   );
  }
}
