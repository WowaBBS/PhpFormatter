<?
NameSpace Reformat\Linked;

Use Function Reformat\Log;

Trait TList //Implements IteratorAggregate
{
  Use TWeak;
//Function ToWeak() { Return WeakReference::Create($this); }
  
  Var $List_First ;
  Var $List_Last  ;
  
  Function List_Add($Item)
  {
    $Item->_CheckParent();
    $this->_Append_Range($Item, $Item);
  }
  
  Function _List_Append_Range($From, $To)
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
  
//Function GetIterator(): Iterator { Return New Iterator($this->First, $this->Last); }
  Function GetIterator(): Iterator { Return New Iterator($this->First); }
  
  //TODO: Attr DebugOnly return True;
  Function Debug_IntegrityTest()
  {
    $Res=True;
    $Prev=Null;
    ForEach($this As $Item)
    {
      If($Item->Prev!==$Prev)
        $Res=Log('Error', 'List_IntegrityTest: Prev different for ',$Item)->Debug([
          'Prev'=>$Prev,
          'Item.Prev'=>$Item->Prev,
        ])->BackTrace()->Ret(False);
      If($Item->Parent!==$this)
        $Res=Log('Error', 'List_IntegrityTest: Parent different')->Debug([
          'Parent'=>$this,
          'Item.Parent'=>$Item->Parent,
          'Item'=>$Item,
        ])->BackTrace()->Ret(False);
      $Prev=$Item;
    }
    Return $Res;
  }
//****************************************************************
// Depracated

  Var $First { Get=>$this->List_First ; Set($v) { $this->List_First =$v; } }
  Var $Last  { Get=>$this->List_Last  ; Set($v) { $this->List_Last  =$v; } }
  
  Function  Add          ($Item     ) { Return $this-> List_Add          ($Item     ); }
  Function _Append_Range ($From, $To) { Return $this->_List_Append_Range ($From, $To); }

//****************************************************************
}
