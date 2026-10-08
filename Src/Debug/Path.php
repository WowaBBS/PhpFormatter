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
    $Text=SubStr($Text, StrLen($Key));
    
    $Path=New \Reformat\Path\TTrace();
    $DebugPath=$Path->MakeBracesPath($Token);
    Log('Debug', 'DebugPath: ', $Text)->File($Token->GetFilePos()->ToArgs())->Debug($DebugPath);
  }

  Function Option_Do($Op)
  {
    Parent::Option_Do($Op);
  }
}
