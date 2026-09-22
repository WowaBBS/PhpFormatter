<?
namespace Reformat\Linked;

Include_Once 'Weak.php';

Trait TNode
{
  Use TWeak;
  // Try Weak field and ToWeak to compare
//Function ToWeak() { Return WeakReference::Create($this); }
  
  Var $Parent { Get=>$this->Parent ?->Get(); Set=>$value?->ToWeak(); } //Weak
  Var $Prev   { Get=>$this->Prev   ?->Get(); Set=>$value?->ToWeak(); } //Weak
  Var $Next   ;
  
  Function Remove()
  {
    $this->_RemoveTo($this);
    $this->Parent =Null;
  }
  
  Function _RemoveTo($To)
  {
    $Next   =$To  ->Next   ; $To  ->Next   =Null;
    $Prev   =$this->Prev   ; $this->Prev   =Null;
    
    $Parent =$this->Parent ;
    If(!$Parent) Return;
    
    If($Next) $Next  ->Prev  =$Prev;
    Else      $Parent->Last  =$Prev;
    If($Prev) $Prev  ->Next  =$Next;
    Else      $Parent->First =$Next;
  }
  
  Function _CheckParent()
  {
    If(!$this->Parent) Return False;
    Log('Error', 'Item has already placed'); //$Item->Remove();
    $this->Remove();
    Return True;
  }
  
  Function Insert($Item) //Right
  {
    $Item->CheckParent();
    
    $this->_Insert_Range($Item, $Item);
  }

  Function _Insert_Range($From, $To) //Right
  {
    If(!$this->Next)
      $this->Parent->Last=$To; //Parent can be weak?
    
    $From->_SetParentTo($To, $this->Parent);

    $To   ->Next   =$this->Next;
    $From ->Prev   =$this;
    
    $this ->Next=$From;
  }

  Function _SetParentTo($To, $Parent)
  {
    $To=$To->Next;
    For($Item=$this;$Item!==$To;$Item=$Item->Next)
      $Item->Parent=$Parent;
  }
  
}
