<?
NameSpace Reformat\Path;

Use Function Reformat\Log;

Class TItem
{
  Var EType  $Type   ;
  Var String $Prefix ;
  Var String $Value  ;
  Var TLine  $Line   ;
  
  Function __Construct(
     EType  $Type   =EType::UnKnown,
     String $Prefix ='',
     String $Value  ='',
    ?TLine  $Line   =Null,
  )
  {
    $this->Type   = $Type   ;
    $this->Prefix = $Prefix ;
    $this->Value  = $Value  ;
    $this->Line   = $Line   ;
  }
  
  Function IsSignificant() { Return $this->Type->IsSignificant(); }
  
  Function _Set($Type, $Prefix, $Value)
  {
    $this->Type   = EType::Cast($Type);
    $this->Prefix = $Prefix ;
    $this->Value  = $Value  ;
    Return $this;
  }
  
  Function _UnKnown($Info)
  {
    $this->Type   = EType::UnKnown;
    $this->Prefix = '';
    $this->Value  = $Info ;
    Return $this;
  }
  
  Function ToString()
  {
    Return $this->Prefix.$this->Value;
  }
  
  Function _ToString(Array &$Res)
  {
    $Res[]=$this->Prefix ;
    $Res[]=$this->Value  ;
  }
  
  Function ToDebug()
  {
    If($this->Type->IsUnKnown()) Return $this->ToString();
    
    Return $this->Type->ToString().' '
      .$this->Prefix
      .$this->Value;
  }
}