<?
NameSpace Reformat\Filter;

Use Reformat\Token\TComment As TTokenComment;

Use Function Reformat\Log;

// Key: TestComments
  
/**
 * Functions:
 *  * Group inline comments
 *  * Detect code comments
 */
Class TComment Extends TBase
{
  Static Function GetName() { Return 'Comment'; }
  
  Var $GroupInlineComments =True  ;
  Var $DebugCheckInfo      =False ;
  
  Function ProcessText($Token):Void
  {
    If($Token->Id!==T_COMMENT && $Token->Id!==T_DOC_COMMENT) Return;
    If($Token->GetId()==='Option') Return;
    $Res=New TTokenComment($Token);
    If(!$this->DebugCheckInfo) Return;
    
    $Res->TestInnerText();
    
    $Res->CheckDetect();
  }

  Function Option_Do($Op)
  {
    Parent::Option_Do($Op);
    $this->GroupInlineComments =$Op->GetSet('GroupInlineComments' ,$this->GroupInlineComments ,'Bool');
    $this->DebugCheckInfo      =$Op->GetSet('DebugCheckInfo'      ,$this->DebugCheckInfo      ,'Bool');
    If($Op->GetSet('DebugPos', False, 'Bool') && $this->InProcess) $Op->DebugPos();
  }
}
