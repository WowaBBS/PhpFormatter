<?
NameSpace Reformat\Token;

Use Reformat\FilePos\TInfo As TFilePos;

Class TDocument Extends TList
{
  Var TFilePos $FilePos;
  
  Function __Construct(?TFilePos $FilePos=Null)
  {
    $this->FilePos =$FilePos?? TFilePos::GetEmpty();
  }
  
  Function GetFilePos(): TFilePos { Return $this->FilePos; }
  Function GetFilePosEnd():TFilePos { Return $this->Last?->GetFilePosEnd() ?? $this->FilePos; }
}