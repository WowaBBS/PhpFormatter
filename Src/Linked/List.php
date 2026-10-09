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
    $Item->_List_CheckParent();
    $this->_List_Append_Range($Item, $Item);
  }
  
  Function _List_Append_Range($From, $To)
  {
    $From->_List_SetParentTo($To, $this);
    
    If($Last=$this->List_Last)
      $Last->List_Next=$From;
    $From->List_Prev=$this->List_Last;
  //$To  ->List_Next=Null; // Should be 0 by default
    $this->List_Last=$To;
    If(!$this->List_First)
      $this->List_First=$From;
  }
  
//Function GetIterator(): Iterator { Return New Iterator($this->List_First, $this->List_Last); }
  Function GetIterator(): Iterator { Return New Iterator($this->List_First); }
  
  //TODO: Attr DebugOnly return True;
  Function Debug_IntegrityTest()
  {
    $Res=True;
    $Prev=Null;
    ForEach($this As $Item)
    {
      If($Item->List_Prev!==$Prev)
        $Res=Log('Error', 'List_IntegrityTest: Prev different for ',$Item)->Debug([
          'Prev'=>$Prev,
          'Item.Prev'=>$Item->List_Prev,
        ])->BackTrace()->Ret(False);
      If($Item->List_Parent!==$this)
        $Res=Log('Error', 'List_IntegrityTest: Parent different')->Debug([
          'Parent'=>$this,
          'Item.Parent'=>$Item->List_Parent,
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
