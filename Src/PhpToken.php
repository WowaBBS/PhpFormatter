<?
namespace Reformat;

include 'Token/Types.php'    ;
include 'Token/KeyWords.php' ;

class PhpToken extends \PhpToken 
{
  Function GetDebug()
  {
    if($this->id<128) Return [$this->text];
    Return [token_name($this->id), ': ', $this->text];
  }
  
  public function GetLowerText()
  {
    return strtolower($this->text);
  }
}