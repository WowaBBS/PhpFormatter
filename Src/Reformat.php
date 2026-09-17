<?
namespace Reformat;

Function Reformat($source, $FileName='')
{
//$tokens = token_get_all($source);
  $tokens = PhpToken::Tokenize($source);

  $changed = false;
  
  $Filters=Filter\CreateList($FileName); //TODO: $Config
  
  ForEach($Filters As $Filter)
    $Filter->Start();
  
  $result = [];
  foreach($tokens as $token)
  {
    $tokens2=[$token];
    ForEach($Filters As $Filter)
    {
      $back=[];
      While($tokens2)
      {
        $token=Array_Shift($tokens2);
        $r=$Filter->Process($token);
        If(Is_String($r))
        {
          If($token->text!==$r)
          {
            $token->text=$r;
            $r=$token;
          }
          Else
            $r=Null;
        }
        If($r!==Null) $changed=True;
        If($r===False) { Continue; }
        // TODO: Recalc row and column
        If($r===Null) { $back[]=$token; Continue; }
        If($r===True) { $back[]=$token; Continue; }
        If(Is_Object($r)) { $back[]=$r; Continue; }
        If(Is_Array($r)) { Array_Push($back, ...$r); Continue; }
        Log('Error', 'Unknown token process:')->Debug($r);
        Return -1;
      }
      $tokens2=$back;
    }
    
    ForEach($tokens2 As $tiken)
      $result[]=$tiken->text;

  //*********************************
  } 
  
  // TODO: Allow to add rest of tokens at the end
  ForEach($Filters As $Filter)
    $Filter->Finish();
  
  $result=Implode($result);
  
  //TODO: Check result and detect changing and compare with $changed and worn differences

  if (!$changed) return 0;
  
  return $result;
}
