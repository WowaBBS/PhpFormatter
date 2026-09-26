<?
namespace Reformat\Token;

Include_Once '../All.php';
Include_Once 'Option.php';
//$Loader->Load_Type('Debug/Depth'); Use Function WLib\Debug\Depth;

Use Function Reformat\Log;
Use Reformat\Utils\TOption;

Set_Time_Limit(2);

//Нужно написать программу на ПХП, которая парсит строку с опциями 'Path,Class.Field{Param1=True, Param2={SubParam1="Helo",SubParam2=4}}' и возвращает в виде масива ['Path'=>['Class'=>['Field'=>['Param1'=>true,'Param2'=>['SubParam1'=>"Helo",'SubParam2'=>4]]]]] в качестве токенайзера использовать PhpToken::Tokenize
$Option  =TOption::$Test['Option'];
$Desired =TOption::$Test['Result'];
$Parser  =New TOption(75, 7);
$Actual=$Parser->Parse($Option);

// Проверяем, совпадает ли результат
If($Actual !== $Desired)
  Log('Error')//, Depth(100))
    ('Actual  :', Json_EnCode($Actual  ))
    ('Desired :', Json_EnCode($Desired ));
