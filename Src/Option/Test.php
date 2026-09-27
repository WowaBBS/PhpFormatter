<?
namespace Reformat\Option;

Include_Once '../_All.php';
//$Loader->Load_Type('Debug/Depth'); Use Function WLib\Debug\Depth;

Use Function Reformat\Log;
Use Reformat\FilePos\TInfo As TFilePos;
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
    'Logs'=>'Test.php(46,20) [Debug] DebugPos',
  ],
  ['Empty', __LINE__+1, 16,
    'Option'=>'',
    'Logs'=>'Test.php(50,16) [Error] Unexpected end of option',
  ],
];

$LogBuffer=$Loader->Create_Object('/Stream/Buffer');
$LogStream=$Loader->Create_Object('/Log/Logger/Stream', ['Stream'=>$LogBuffer, 'AutoDoneStream'=>False]);
$Loader->GetLogger()->Add($LogStream);

ForEach($Tests As $Test)
{
  $Name=$Test[0];
  Log('Debug', 'Test: ', $Name);
  $FilePos=New TFilePos($SourceFile, $Test[1], $Test[2]);
  $Option=$Test['Option'];
  
  $LogBuffer->Clear();
  
  $Parser  =New TParser($FilePos);
  $Result=$Parser->Parse($Option, $FilePos);
  
  $ActualLog=Trim($LogBuffer->Get_Content());
  
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
  
  If($DesiredLog=$Test['Logs']?? Null)
  {
    $ActualLog =Explode("\n", $ActualLog);
    If(!Is_Array($DesiredLog)) $DesiredLog=Explode("\n", $DesiredLog);
  //If(Is_Array($DesiredLog)) $DesiredLog=Implode("\n", $DesiredLog);
    $ActualLog =Implode("\n  | ", $ActualLog );
    $DesiredLog=Implode("\n  | ", $DesiredLog);
    If($ActualLog !== $DesiredLog)
      Log('Error', 'Parse.LogResult=')//, Depth(100))
        ('  Actual  :', $ActualLog  )
        ('  Desired :', $DesiredLog );
  //Else
  //  Log('Debug', 'Parse.LogResult=', $ActualLog);//, Depth(100))
  }
}

$Loader->GetLogger()->Remove($LogStream);
$LogStream->Done();
$LogBuffer->Done();
$Loader->Done();