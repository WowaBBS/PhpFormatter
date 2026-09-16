<?
namespace Reformat;

Function Reformat($source)
{
//$tokens = token_get_all($source);
  $tokens = PhpToken::Tokenize($source);

  $changed = false;
  
  $Filters=Filter\CreateList(); //TODO: $Config
  
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
  
  $result=Implode($result);

  if (!$changed) return 0;
  
  return $result;
}
