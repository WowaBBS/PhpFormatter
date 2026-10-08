<?
NameSpace Reformat\Path;

Use Function Reformat\Log;

Enum EBrace
{
  Case None  ;
  Case Left  ; //Open
  Case Right ; //Close
  
  Protected Const Array Detect=[
    '['=>[Self::Left  ,']'],
    '('=>[Self::Left  ,')'],
    '{'=>[Self::Left  ,'}'],
    ']'=>[Self::Right ,'['],
    ')'=>[Self::Right ,'('],
    '}'=>[Self::Right ,'{'],
  ];
  
  Function IsNone  () { Return $this===Self::None  ; }
  Function IsLeft  () { Return $this===Self::Left  ; }
  Function IsRight () { Return $this===Self::Right ; }

  Static Function Detect($v) { Return Self::Detect[$v][0]?? Self::None; }
  Static Function Pair($v) { Return Self::Detect[$v][1]; }
}
