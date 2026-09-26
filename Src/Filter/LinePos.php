<?
namespace Reformat\Filter;
use function Reformat\Log;
use function Reformat\Utils\Str\LinePos;

/**
 * Recalcs Line, Pos, and Tab
 *  * Group inline comments
 *  * Detect code comments
 */
class TLinePos Extends TBase
{
  Static Function GetName() { Return 'LinePos'; }
  
  Var $FirstLine =0;
  Var $FirstPos  =0;
  Var $Line =0;
  Var $Pos  =0;
  Var $Tab  =0;

  Function CodeStart()
  {
    $this->Line =$this->FirstLine ;
    $this->Pos  =$this->FirstPos  ;
  }
  
  Function ProcessText($Token)//:Void|String
  {
    $Token->Line =$this->Line ;
    $Token->Pos  =$this->Pos  ;
    $Token->Tab  =$this->Tab  ;
    $Text=$Token->Text;
    
    If(LinePos($Text, $this->Line, $this->Pos))
    {
      $LastLineSize=$this->Pos;
      $this->Pos+=$this->FirstPos;
      Switch($Token->Id)
      { //TODO: Heredoc, Yield From, Tag, Html
      Case \T_WHITESPACE    :  $this->Tab=$LastLineSize; Break; //Ok
      Case \T_START_HEREDOC :
      Case \T_ENCAPSED_AND_WHITESPACE:
      Case \T_OPEN_TAG      :
      Case \T_COMMENT       :
      Case \T_DOC_COMMENT   :
      Case \T_YIELD_FROM    :
      Case \T_INLINE_HTML   :
        //TODO:
        Break;
      Default: Log('Warning', '\n is in ', $Token->GetTokenName())->Debug($Token->Text);
      }
    }
  }
}
