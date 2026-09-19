<?
namespace Reformat;

Function Reformat($Source, $Info, $Filters=Null)
{
  $Tokens = PhpToken::Tokenize($Source);

  $Changed = False;
  
  $Filters??=Filter\CreateList($Info); //TODO: $Config
  
  ForEach($Filters As $Filter)
  {
    $Result=$Filter->ProcessAll($Tokens);
    If($Result===False) Return -1; //Error happend
    If(Is_Array($Result))
    {
      $Changed=True;
      $Tokens=$Result;
    }
  }
  
  $Result = [];
  foreach($Tokens As $Token)
    $Result[] = $Token->text;
  
  $Result=Implode($Result);
  
  $IsRealChanged=$Source!==$Result;
  
  If(!$Changed) Return 0;
  
  Return $Result;
}
