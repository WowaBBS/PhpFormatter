<?
NameSpace Reformat\FilePos; //TFilePosText

Class TText Extends TInfo
{
//Var $NextPos=0; //For second and rest lines
  Function __Construct(
    ReadOnly String $FileName ='Source',
    ReadOnly Int    $Line     =1,
    ReadOnly Int    $Pos      =1,
    ReadOnly Int    $NextPos  =1, //For second and rest lines
  )
  {
  }
}
