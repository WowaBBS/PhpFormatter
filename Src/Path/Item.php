<?
NameSpace Reformat\Path;

Use Function Reformat\Log;

Class TItem
{
  Var EType  $Type   ;
  Var String $Name   ;
  Var String $Prefix ;
  Var TLine  $Line   ;
  
  Function __Construct(
    EType  $Type   ,
    String $Name   ,
    String $Prefix ,
    TLine  $Line   ,
  )
  {
    $Res=New Self();
    $Res->Type   = $Type   ;
    $Res->Name   = $Name   ;
    $Res->Prefix = $Prefix ;
    $Res->Line   = $Line   ;
    Return $Res;
  }
  
}