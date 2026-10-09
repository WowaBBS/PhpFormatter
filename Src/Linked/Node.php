<?
NameSpace Reformat\Linked;

Use Function Reformat\Log;

Trait TNode //Implements \IteratorAggregate
{
  Use TWeak;
  
  Var $List_Parent { Get=>$this->List_Parent ?->Get(); Set=>$value?->ToWeak(); } //Weak
  Var $List_Prev   { Get=>$this->List_Prev   ?->Get(); Set=>$value?->ToWeak(); } //Weak
  Var $List_Next   ;
  
  Function List_Remove()
  {
    $this->_List_RemoveTo($this);
    $this->Parent =Null;
  }
  
  Function _List_RemoveTo($To)
  {
    Iterator::Iterators_OnRemove($this, $To);
  
    $Next   =$To  ->Next   ; $To  ->Next   =Null;
    $Prev   =$this->Prev   ; $this->Prev   =Null;
    
    $Parent =$this->Parent ;
    If(!$Parent) Return;
    
    If($Next) $Next  ->Prev  =$Prev;
    Else      $Parent->Last  =$Prev;
    If($Prev) $Prev  ->Next  =$Next;
    Else      $Parent->First =$Next;
  }
  
  Function _List_CheckParent()
  {
    If(!$this->Parent) Return False;
    Log('Error', 'Item has already placed'); //$Item->Remove();
    $this->Remove();
    Return True;
  }
  
  Function List_Insert($Item) //Right
  {
    $Item->_CheckParent();
    $this->_Insert_Range($Item, $Item);
    Return $Item;
  }

  Function _List_Insert_Range($From, $To) //Right
  {
    If(!$this->Next)
      $this->Parent->Last=$To; //Parent can be weak?
    
    $From->_SetParentTo($To, $this->Parent);
    
    $Next=$this->Next;

    $To   ->Next   =$Next;
    $From ->Prev   =$this;
    
    $this ->Next=$From;
    
    If($Next)
      $Next->Prev=$To;
  }

  Function _List_SetParentTo($To, $Parent)
  {
    $To=$To->Next;
    For($Item=$this; $Item!==$To; $Item=$Item->Next)
      $Item->Parent=$Parent;
  }
  
  Function List_IterateUntil($To=Null): Iterator { Return New Iterator($this, $To); }

//****************************************************************
// Depracated

  Var $Parent { Get=>$this->List_Parent ; Set($v) { $this->List_Parent =$v; } }
  Var $Prev   { Get=>$this->List_Prev   ; Set($v) { $this->List_Prev   =$v; } }
  Var $Next   { Get=>$this->List_Next   ; Set($v) { $this->List_Next   =$v; } } 
  
  Function  Remove      (            ) { Return $this-> List_Remove      (            ); }
  Function _RemoveTo    ($To         ) { Return $this->_List_RemoveTo    ($To         ); }
  Function _CheckParent (            ) { Return $this->_List_CheckParent (            ); }
  Function  Insert      ($Item       ) { Return $this-> List_Insert      ($Item       ); }
  Function _Insert_Range($From, $To  ) { Return $this->_List_Insert_Range($From, $To  ); }
  Function _SetParentTo ($To, $Parent) { Return $this->_List_SetParentTo ($To, $Parent); }
  Function  IterateUntil($To=Null    ) { Return $this-> List_IterateUntil($To=Null    ); }
  
//****************************************************************
}
