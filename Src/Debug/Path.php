<?
NameSpace Reformat\Debug;

Use Reformat\Filter\TBase As TFilterBase;
Use Function Reformat\Log;

Class TPath Extends TFilterBase
{
  Static Function GetName() { Return 'Debug\Path'; }
  
  Function ProcessText($Token):Void
  {
    If($Token->GetTypeHandler()!=='Text') Return;
    If($Token->GetId()==='Option') Return;
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
    
    $Path=New \Reformat\Path\TTrace();
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
  }
}
