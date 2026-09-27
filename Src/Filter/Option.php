<?
NameSpace Reformat\Filter;

Use Function Reformat\Log;
Use Function Reformat\Utils\Str\Starts_With_List;
Use Function Reformat\Utils\StrList ;
Use Reformat\Token\TOption As TTokenOption;

/**
 */
Class TOption Extends TBase
{
  Static Function GetName() { Return 'Option'; }
  
  Var $OptionKey='WFormat:'; //TODO: Several formats
  
  Function ProcessText($Token)//:Void|String|Token
  {
    If($Token->Id!==T_COMMENT) Return;
    $Text=$Token->Text;

    Switch($With=Starts_With_List($Text, ['//', '#', '/*']))
    {
    Case '/*' : $Text=SubStr($Text, 0, -2);
    Case '#'  :
    Case '//' : $Text=Trim(SubStr($Text, StrLen($With))); Break;
    Default:
      Log('Error', 'Unknown token')->Debug($Token);
      Return;
    }
    If(!Str_Starts_With($Text, $this->OptionKey))
    {
      If(StrPos($Text, $this->OptionKey))
        Log('Warning', 'Detected wrong location of the option')->Debug($Token);
      Return;
    }
    $Text=Trim(SubStr($Text, StrLen($this->OptionKey)));
    
    $Res=New TTokenOption($Text, $Token, $this->GetSource());
    
    $Token->Insert($Res);
    $Token->Remove();
    
    Return $Res;
  }
}
