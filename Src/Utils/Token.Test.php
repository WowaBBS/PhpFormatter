<?
NameSpace Reformat\Utils\Token;

use function Reformat\Log;

Include_Once '../All.php';
Include_Once 'Token.php';

$Code=<<<'HereDoc'
  $Data //Comment
  /*
    Comment
  */
HereDoc;

$Tokens=TokenizeCode($Code);
RemoveComments($Tokens);
RemoveWhiteSpaces($Tokens);
$Actual=$Tokens->ToString();
$Desired='$Data';
Log('Debug', $Actual);