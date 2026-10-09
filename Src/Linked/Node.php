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
    $this-> List_Parent =Null;
  }
  
  Function _List_RemoveTo($To)
  {
    Iterator::Iterators_OnRemove($this, $To);
  
    $Next   =$To  ->List_Next   ; $To  ->List_Next   =Null;
    $Prev   =$this->List_Prev   ; $this->List_Prev   =Null;
    
    $Parent =$this->List_Parent ;
    If(!$Parent) Return;
    
    If($Next) $Next  ->List_Prev  =$Prev;
    Else      $Parent->List_Last  =$Prev;
    If($Prev) $Prev  ->List_Next  =$Next;
    Else      $Parent->List_First =$Next;
  }
  
  Function _List_CheckParent()
  {
    If(!$this->List_Parent) Return False;
    Log('Error', 'Item has already placed'); //$Item->List_Remove();
    $this->List_Remove();
    Return True;
  }
  
  Function List_Insert($Item) //Right
  {
    $Item->_List_CheckParent();
    $this->_List_Insert_Range($Item, $Item);
    Return $Item;
  }

  Function _List_Insert_Range($From, $To) //Right
  {
    If(!$this->List_Next)
      $this->List_Parent->List_Last=$To; //Parent can be weak?
    
    $From->_List_SetParentTo($To, $this->List_Parent);
    
    $Next=$this->List_Next;

    $To   ->List_Next   =$Next;
    $From ->List_Prev   =$this;
    
    $this ->List_Next=$From;
    
    If($Next)
      $Next->List_Prev=$To;
  }

  Function _List_SetParentTo($To, $Parent)
  {
    $To=$To->List_Next;
    For($Item=$this; $Item!==$To; $Item=$Item->List_Next)
      $Item->List_Parent=$Parent;
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
