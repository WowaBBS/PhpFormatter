<?
NameSpace Reformat\Debug;

Use Reformat\Option\IReceiver  As IOptionReceiver  ;
Use Function Reformat\Log;

Class TController Implements IOptionReceiver
{
  Var $Source { Get=>$this->Source?->Get(); Set=>$value? \WeakReference::Create($value):Null; }
  
  Function __Construct($Source)
  {
    $this->Source=$Source;
  }
  
  Var $List=[
    'Path'=>TPath::class,
  ];
  
  //****************************************************************
  // Option
  
  Function Option_Do($Op)
  {
    $Filters=$this->Source->Filter;
    ForEach($this->List As $Name=>$Class)
    {
      $FilterName=$Class::GetName();
      $Filter=$Filters->Has($FilterName)? $Filters->Get($FilterName): Null;
      $Enable=$Op->GetSet($Name, $Filter?->Enable?? False);
      If($Enable && !$Filter)
      {
        $Filter=New $Class($this->Source);
        $Filters->Add($Filter);
      }
      If($Filter)
        $Filter->Enable=$Enable;
    }
    
    $Op->BaseCalled();
  }
  
  //****************************************************************
}
