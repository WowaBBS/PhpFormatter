<?
namespace Reformat\Linked;

Include_Once 'Weak.php';

use function Reformat\Log;

Class Iterator Implements \Iterator 
{
  Use TWeak;

  Var $Index =0;
  Var $Current ;
  Var $Until   ;
  
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
      If($Cur===$this->Current ) $this->Current =$To;
      If($Cur===$this->Until   ) $this->Until   =$To;
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
  Function Key     (): Mixed { Return $this->Index; }
  Function Next    (): Void { ++$this->Index; $this->Current=$this->Current?->Next; }
  Function Valid   (): Bool { Return $this->Current && $this->Current!=$this->Until; }
}
