<?
NameSpace Reformat\Linked;

Use Function Reformat\Log;

Class Iterator Implements \Iterator 
{
  Use TWeak;

  Var  Int    $Index =0;
  Var ?Object $Current ;
  Var  Bool   $Next    =False; //If Current Removed
  Var ?Object $Until   ;
  
  Static $Iterators=[];
  
  Static Function Iterators_OnRemove($From, $To)
  {
    ForEach(Self::$Iterators As $Iterator)
      $Iterator->Get()->OnRemove($From, $To);
  }
  
  Protected Function OnRemove($From, $To)
  {
    $To=$To->Next;
    For($Cur=$From; $Cur!==$To; $Cur=$Cur->Next)
    {
      If($Cur===$this->Current ) { $this->Current =$To; $this->Next=True; }
      If($Cur===$this->Until   )   $this->Until   =$To;
    }
  }

  Function __Construct($From, $To=Null)
  {
    $this->Current = $From ;
    $this->Until   = $To?->Next;
    Self::$Iterators[Spl_Object_Id($this)]=$this->ToWeak();
  }
  
  Function __Destruct()
  {
    UnSet(Self::$Iterators[Spl_Object_Id($this)]);
  }

  Function ReWind  (): Void {} //TODO: Make rewindable?
  Function Current (): Mixed { Return $this->Current; }
  Function Key     (): Mixed { Return $this->Index; } //TODO: Alter key
  Function Next    (): Void { ++$this->Index; If($this->Next) $this->Next=False; Else $this->Current=$this->Current?->Next; }
  Function Valid   (): Bool { Return $this->Current && $this->Current!=$this->Until; }
}
