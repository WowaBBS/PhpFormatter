<?
NameSpace Reformat\Debug;

Use Reformat\Filter\TBase As TFilterBase;
Use Function Reformat\Log;

Class TPath Extends TFilterBase
{
  Static Function GetName() { Return 'Debug\Path'; }
  
  Function ProcessText($Token):Void
  {
    If($Token->Id!==T_COMMENT && $Token->Id!==T_DOC_COMMENT) Return;
    If($Token->GetId()==='Option') Return;
    $Text=$Token->GetInnerText();
    $Key='TestPath=';
    If(!Str_Starts_With($Text, $Key)) Return; //TODO: Error
    $Desired=SubStr($Text, StrLen($Key));
    
    $Path=New \Reformat\Path\TTrace();
    $DebugPath=$Path->MakeBracesPath($Token);
    $File=$Token->GetFilePos()->ToArgs();
    
    $Actual=$DebugPath->ToString();
    If($Actual!==$Desired)
      Log('Error', 'TestPath is different:')->File($File)
        ('Desired : ', $Desired )
        ('Actual  : ', $Actual  );
  //Else
  //  Log('Debug', 'DebugPath: ', $Desired)->File($File)->Debug($DebugPath->ToDebug());
  }

  Function Option_Do($Op)
  {
    Parent::Option_Do($Op);
  }
}
