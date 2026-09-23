<?
namespace Reformat\Process;
use function Reformat\Log;
use function Reformat\CheckPhp;
use          Reformat\PhpToken;

Abstract Class TSource Extends TBase
{
  Function Source($Source)
  {
    $Document=New \Reformat\Token\TList();
    $Tokens = \PhpToken::Tokenize($Source); //Token_Get_All($Source);
    ForEach($Tokens As $Item)
      $Document->AddText(
        $Item->id   ,
        $Item->text ,
        $Item->line ,
        $Item->pos  ,
      );
    UnSet($Tokens);
  
    $Result=$this->Filters->ProcessAll($Document);
    If($Result===False ) Return -1; //Error happend
    $Changed=$Result===True  ;
    
    $Result = $Document->ToString();
    
    $IsRealChanged=$Source!==$Result;
    
    If($Changed!==$IsRealChanged)
      If($IsRealChanged) //TODO: Save Actual and desired versions
        Return Log('Error', 'Actually document is changed')->Ret(-1);
      Else
        Log('Warning', 'Actually document is not changed');
    
    If(!$Changed) Return 0;
    if(!CheckPhp($Result)) Return -1;
    
    Return $Result;
  }
}