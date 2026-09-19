<?
namespace Reformat\Filter;

include 'Base.php'       ;
include 'List.php'       ;
include 'DebugToken.php' ;
include 'CrLf.php'       ;
include 'Tab.php'        ;
include 'Naming.php'     ;
include 'Translate.php'  ;

Function GetList()
{
  Return [
    TDebugToken ::class,
    TCrLf       ::class,
    TTab        ::class,
    TNaming     ::class,
    TTranslate  ::class,
  ];
}

Function CreateList($Process)
{
  $Res=New TList();
  $Res->Init($Process);
  ForEach(GetList() As $Class)
    If($Class::IsApplicable($Process))
      $Res->Add(New $Class());
  Return $Res;
}
