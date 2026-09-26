<?
namespace Reformat\Token;

Include_Once '../All.php';
Include_Once 'Option.php';
//$Loader->Load_Type('Debug/Depth'); Use Function WLib\Debug\Depth;

Use Function Reformat\Log;
Use Reformat\Utils\TOption;

Set_Time_Limit(2);

//Нужно написать программу на ПХП, которая парсит строку с опциями 'Path,Class.Field{Param1=True, Param2={SubParam1="Helo",SubParam2=4}}' и возвращает в виде масива ['Path'=>['Class'=>['Field'=>['Param1'=>true,'Param2'=>['SubParam1'=>"Helo",'SubParam2'=>4]]]]] в качестве токенайзера использовать PhpToken::Tokenize
$Option  =TOption::$Test_Option; $Pos=FindProperty(TOption::class, 'Test_Option');
$Desired =TOption::$Test_Result; //Log('Debug', 'Pos=',$Pos);
$Parser  =New TOption(...$Pos);
$Actual=$Parser->Parse($Option);

// Проверяем, совпадает ли результат
If($Actual !== $Desired)
  Log('Error', 'Parse.Result=')//, Depth(100))
    ('  Actual  :', Json_EnCode($Actual  ))
    ('  Desired :', Json_EnCode($Desired ));
Else
  Log('Debug', 'Parse.Result=',Json_EnCode($Actual  ));//, Depth(100))

$Option  =TOption::$Test_DebugPos; $Pos=FindProperty(TOption::class, 'Test_DebugPos');
$Parser  =New TOption(...$Pos);
$Actual=$Parser->Parse($Option);

Function FindProperty($Class, $Name)
{
  $Reflector = New \ReflectionClass($Class);
  $Properties = $Reflector->getProperties(\ReflectionProperty::IS_STATIC); // или PRIVATE, PUBLIC
  $File=$Reflector->getFileName();
  $Text=File_Get_Contents($File);
  $File=PathInfo($File)['basename'];
  $i=StrPos($Text, 'Static $'.$Name);
  If($i===False) { Log('Error', 'Property Static $', $Name, ' not found'); Return [0,0,0,$File]; }
  $Text=SubStr($Text,0,$i);
  $Text=Explode("\n", $Text);
  $Line=Count($Text);
  $Text=$Text[Count($Text)-1];
  $Pos=StrLen($Text)-StrLen(LTrim($Text));
//Return [0, 0, 0, $File];
  $Line +=2;
  $Pos  +=3;
  Return [$Line, $Pos, $Pos, $File];
}