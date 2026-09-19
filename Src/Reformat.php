<?
namespace Reformat;

Function Reformat($Source, $Info, $Filters=Null)
{
  $Tokens = PhpToken::Tokenize($Source);

  $Changed = False;
  
  $Filters??=Filter\CreateList($Info); //TODO: $Config
  
  $Result=$Filters->ProcessAll($Tokens);
  If($Result===False) Return aaa(-1); //Error happend
  If(Is_Array($Result))
  {
    $Changed=True;
    $Tokens=$Result;
  }
  
  $Result = [];
  ForEach($Tokens As $Token)
    $Result[] = $Token->text;
  
  $Result=Implode($Result);
  
  $IsRealChanged=$Source!==$Result;
  
  If($Changed!==$IsRealChanged)
    If($IsRealChanged) //TODO: Save Actual and desired versions
      Return Log('Error', 'Actually document is changed')->Ret(-1);
    Else
      Log('Warning', 'Actually document is not changed');
  
  If(!$Changed) Return 0;
  
  Return $Result;
}
