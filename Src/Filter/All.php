<?
namespace Reformat\Filter;

include 'Base.php'       ;
include 'DebugToken.php' ;
include 'CrLf.php'       ;
include 'Tab.php'        ;
include 'KeyWord.php'    ;
include 'Translate.php'  ;

Function GetList()
{
  Return [
    TDebugToken ::class,
    TCrLf       ::class,
    TTab        ::class,
    TKeyWord    ::class,
    TTranslate  ::class,
  ];
}

Function CreateList($Config=[])
{
  $Res=[];
  ForEach(GetList() As $Class)
    If($Class::IsApplicable($Config))
      $Res[]=New $Class();
  Return $Res;
}
