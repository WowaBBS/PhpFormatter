<?
namespace Reformat\Option;

Include_Once '../_All.php';
//$Loader->Load_Type('Debug/Depth'); Use Function WLib\Debug\Depth;

Use Function Reformat\Log;
Use Reformat\Option\TParser;

Set_Time_Limit(1);

$SourceFile=BaseName(__FILE__);

$Tests=[
  ['BaseTest', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Path.Class.Field{
        P1=True,
        P2={
          S1="Helo\n",
          S2=4,
          S3=False,
          S4='Hello\n',
          S5=[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2],
          S6={k1=2}, 
          S6.k2:3.14, //Contains sub key k2
          S7{k:1}, 
          S8[2]
        }
      }
      HereDoc,
    'Desired'=>
      ['Path'=>['Class'=>['Field'=>['P1'=>true,'P2'=>[
        'S1'=>"Helo\n",
        'S2'=>4,
        'S3'=>False,
        'S4'=>'Hello\n',
        'S5'=>[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2],
        'S6'=>['k1'=>2, 'k2'=>3.14],
        'S7'=>['k'=>1],
        'S8'=>[2]
      ]]]]],
  ],
  ['DebugPos', __LINE__+1, 16,
    'Option'=>'Key=DebugPos',
  ],
  ['Empty', __LINE__+1, 16,
    'Option'=>'',
  ],
];

ForEach($Tests As $Test)
{
  $Name=$Test[0];
  Log('Debug', 'Test: ', $Name);
  $FilePos=[$SourceFile, $Test[1], $Test[2]];
  $Option=$Test['Option'];
  $Parser  =New TParser($FilePos);
  $Result=$Parser->Parse($Option, $FilePos);
  If($Desired=$Test['Desired']?? Null)
  {
  //$Actual=$Result->ToValue();
    $Actual=$Result;
    // Проверяем, совпадает ли результат
    If($Actual !== $Desired)
      Log('Error', 'Parse.Result=')//, Depth(100))
        ('  Actual  :', Json_EnCode($Actual  ))
        ('  Desired :', Json_EnCode($Desired ));
    Else
      Log('Debug', 'Parse.Result=',Json_EnCode($Actual  ));//, Depth(100))
    
  }
}

$Loader->Done();