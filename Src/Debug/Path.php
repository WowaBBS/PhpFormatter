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

    $Path=New \Reformat\Utils\TPath();
    $DebugPath=$Path->MakeBracesPath($Token);
    Log('Debug', 'DebugPath')->File($Token->GetFilePos()->ToArgs())->Debug($DebugPath);
  }

  Function Option_Do($Op)
  {
    Parent::Option_Do($Op);
  }
}
