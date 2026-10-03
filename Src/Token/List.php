<?
NameSpace Reformat\Token;

Use Reformat\FilePos\TInfo As TFilePos;
Use Function Reformat\Log;

Class TList Extends TBase Implements \IteratorAggregate
{
  Use \Reformat\Linked\TList;

  Function AddText($Id, $Text, $Line, $Pos)
  {
    $this->Add(New TText($Id, $Text, $Line, $Pos));
  }

  Function GetId() { Return $this->First?->GetId()?? Parent::GetId(); }
  Function GetTypeHandler() { Return 'Node'; }

  Function GetInnerText():String
  {
    $this->_GetInnerText($this->First, $this->Last);
  }

  Function _GetInnerText($From, $To):String
  {
    $Result=[];
    For($To=$To->Next; $From!=$To; $From=$From->Next)
      $From->_ToString($Result);
    Return Implode($Result); //TODO: Trim left whitespaces
  }
  
  Function _GetParentNext()
  {
    $Res=$this->Parent;
    While($Res && !$Res->Next)
      $Res=$Res->Parent;
    Return $Res?->Next;
  }
  
  Function GetTreePrev() { Return $this->Prev?? $this->Parent; }
  Function GetTreeNext() { Return $this->First?? $this->Next?? $this->_GetParentNext(); }
  
  Function GetFilePos():TFilePos
  {
    Return $this->First?->GetFilePos()
      ??Log('Error', 'FilePos is not supported')->Ret(
        $this->Next?->GetFilePos()
        ?? $this->_GetParentNext()?->GetFilePos()
        ?? TFilePos::GetEmptyWithError()
      );
  }
  
  Function GetFilePosEnd():TFilePos
  {
    Return $this->Last?->GetFilePosEnd()
      ??Log('Error', 'FilePosEnd is not supported')->Ret(
        $this->Prev?->GetFilePosEnd()
        ?? $this->Parent?->GetFilePosEnd()
        ?? TFilePos::GetEmptyWithError()
      );
  }
  

//****************************************************************
// String
  
  Function _ToString(Array &$Result)
  {
    For($Item=$this->First; $Item; $Item=$Item->Next)
      $Item->_ToString($Result);
  }

//****************************************************************
// Change

  Protected Function DoResetChanged()
  {
    For($Item=$this->First; $Item; $Item=$Item->Next)
      $Item->ResetChanged();
  }

//****************************************************************
// Debug

  Function _Debug_Serialize(&$Res)
  {
    Parent::_Debug_Serialize($Res);
    UnSet($Res['First'  ]);
    UnSet($Res['Last'   ]);
    UnSet($Res['Parent' ]); //TODO: Path?
  }
  
//****************************************************************
}