<?
NameSpace Reformat\FilePos; //TFilePos

Use Function Reformat\Log;

$Loader->Load_Interface('/Debug/Custom');

Use Function Reformat\Utils\Str\TextSize;

Class TInfo Implements IProvider, \WLib\Debug\ICustom
{
  Function __Construct(
    ReadOnly String $FileName ='Source',
    ReadOnly Int    $Line     =1,
    ReadOnly Int    $Pos      =1,
  )
  {
  }
  
  Function AddText($Text) { Return $this->Add(...TextSize($Text)); }
  
  Function Add($Height, $Width)
  {
    Return New Static(
      $this->FileName,
      $this->Line+$Height,
      ($Height? 0:$this->Pos)+$Width,
    );
  }
  
  
  Static Function GetEmpty():TInfo { Static $Res=New TInfo(); Return $Res; }
  Static Function GetEmptyWithError(...$Args):TInfo
  {
    Log('Error', 'There is no FilePos ', ...$Args)->BackTrace();
    Return Self::GetEmpty();
  }
  
  Function GetFilePos():TInfo { Return $this; }
  Function GetFilePosEnd():TInfo { Return $this; }
  
  Function ToArgs() { Return [$this->FileName, $this->Line, $this->Pos]; }

//****************************************************************
// Debug

  //WLib\Debug\ICustom
  Function Debug_Write(\WLib\Log\CFormat $To)
  {
    $To->File(...$this->ToArgs());
  }

//****************************************************************
}
