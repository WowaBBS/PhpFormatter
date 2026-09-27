<?
NameSpace Reformat\Utils\Token;

Include_Once '../_All.php';

Use Function Reformat\Log;

$Code=<<<'HereDoc'
  $Data //Comment
  /*
    Comment
  */
HereDoc;

$Tokens=TokenizeCode($Code);
RemoveComments    ($Tokens);
RemoveWhiteSpaces ($Tokens);
$Actual=$Tokens->ToString();
$Desired='$Data';
Log('Debug', $Actual);