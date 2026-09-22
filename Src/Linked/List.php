<?
namespace Reformat\Linked;

Include_Once 'Weak.php';
Include_Once 'Node.php';

Trait TList
{
  Use TWeak;
//Function ToWeak() { Return WeakReference::Create($this); }
  
  Var $First  ;
  Var $Last   ;
  
  Function Add($Item)
  {
    $Item->_CheckParent();
    $this->_Append_Range($Item, $Item);
  }
  
  Function _Append_Range($From, $To)
  {
    $From->_SetParentTo($To, $this);
    
    If($Last=$this->Last)
      $Last->Next=$From;
    $From->Prev=$this->Last;
  //$To  ->Next=Null; // Should be 0 by default
    $this->Last=$To;
    If(!$this->First)
      $this->First=$From;
  }
}
