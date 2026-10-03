<?
NameSpace Reformat\Process;

Use Function Reformat\Log;
Use Function Reformat\CheckPhp;
Use Function Reformat\Utils\Token\Tokenize;
Use Reformat\FilePos\TInfo As TFilePos;

Abstract Class TSource Extends TBase
{
  Function Source($Source)
  {
    $Document=Tokenize($Source, New TFilePos($this->ShortPath));
  
    $Result=$this->Filter->ProcessAll($Document);
    If($Result===False) Return -1; //Error happend
    $this->Option_CheckUnused();
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