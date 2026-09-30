<?
namespace Reformat\Option;

Include_Once '../_All.php';
$Loader->Load_Type('/Debug/Depth'); Use Function WLib\Debug\Depth;

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
        P2{
          S1="Helo\n",
          S2=4,
          S3=False,
          S4='Hello\n',
          S5=[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2,-1,Nan,Inf,-Inf],
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
        'S5'=>[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2,-1,\NAN,\INF,-\INF],
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
  ['EmptyWithComment', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      // The comment but no option
      HereDoc,
    'Logs'=>'Test.php(55,35) [Error] Unexpected end of option',
  ],
  ['ParseError.ParseMapItem', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key}
      HereDoc,
    'Logs'=>'Test.php(61,10) [Error] Expected ".", "=", ":", "{" or "=>", given }',
  ],
  ['ParseError.ParseKey', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={;
      HereDoc,
    'Logs'=>'Test.php(67,12) [Error] Unknown Key token: ;',
  ],
  ['ParseError.ParseMap', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={k1:'v1':
      HereDoc,
    'Logs'=>'Test.php(73,19) [Error] Map: Excepted ":", "." or "}", given :',
  ],
  ['ParseError.ParseNumeric', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={k1:-v1}
      HereDoc,
    'Logs'=>'Test.php(79,16) [Error] Number expected, given: v1',
  ],
  ['ParseError.ParseValue', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={k1:v1}
      HereDoc,
    'Logs'=>'Test.php(85,15) [Error] Unknown Value token: v1',
  ],
  ['ParseError.ParseMap2', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key{k1:'v1' v2}
      HereDoc,
    'Logs'=>'Test.php(91,19) [Error] Map: Excepted ":", "." or "}", given v2',
  ],
  ['Errors', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Path.Class.Field{
        P1=True,
        P1=False,
        P2={k1=[1,2,3]},
        P2.k2=2,
        P2.k1=3,
        P3={k1=[1,2,3]; k2=9},
        P3=False,
      }
      HereDoc,
    'Logs'=><<<'HereDoc'
      Test.php(99,12) [Warning] Bool: Value Path.Class.Field.P1=False has already exist: True was setted in Test.php(98,12)
      Test.php(102,15) [Warning] Int: Value Path.Class.Field.P2.k1=3 has already exist: [1, 2, 3] was setted in Test.php(100,16)
      Test.php(104,12) [Warning] Bool: Value Path.Class.Field.P3=False has already exist: {k1=[1, 2, 3], k2=9} was setted in Test.php(103,12)
      HereDoc,
  ],
  ['Validate', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      P1{
        k1=False,
        k2='Hello',
      }
      HereDoc,
    'Validate'=>Validate(...),
    'Logs'=><<<'HereDoc'
      Test.php(116,12) [Warning] P1.k1: Incompatible type Bool, expected Int; Current value is False
      HereDoc,
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
  
  $Parser=New TParser($Option, $FilePos);
  $Parser->Logger=$LogStream;
  $Result=$Parser->Parse();
  If($Validate=$Test['Validate']?? Null)
    $Validate($Result);
  $Parser->Logger=Null;
  
  $ActualLog=Trim($LogBuffer->Get_Content());
  
  If(($Desired=$Test['Desired']?? Null)!==Null)
  {
    $Actual=$Result->ToValue();
    
    If(True)
    { //New
      $Actual  =New TDebug($Result  );
      $Desired =New TDebug($Desired );
    }
    Else
    { //Old
      $LogBuffer->Clear();
      Log('Log', Depth(1000), $Actual)->Logger($LogStream);
      $Actual=Trim($LogBuffer->Get_Content());

      $LogBuffer->Clear();
      Log('Log', Depth(1000), $Desired)->Logger($LogStream);
      $Desired=Trim($LogBuffer->Get_Content());
    }
    // Проверяем, совпадает ли результат
    If($Actual != $Desired)
      Log('Error', 'Parse.Result=')
        ('  Actual  :', $Actual  )
        ('  Desired :', $Desired );
    Else
    {
      $Debug=New TDebug();
      $Debug->Value($Result);
      Log('Debug', 'Parse.Result=', ...$Debug->Res);
    //Log('Debug', 'Parse.Result=', ...$Result->ToDebugArr());
    }
  }
  
  If(($DesiredLog=$Test['Logs']?? Null)!==Null)
  {
    $ActualLog =Explode("\n", $ActualLog);
    If(!Is_Array($DesiredLog)) $DesiredLog=Explode("\n", $DesiredLog);
  //If(Is_Array($DesiredLog)) $DesiredLog=Implode("\n", $DesiredLog);
  
    Array_Walk($ActualLog  ,fn(&$v)=>$v=Trim($v)); //TODO: Space at the end of line
    Array_Walk($DesiredLog ,fn(&$v)=>$v=Trim($v));
    
    $ActualLog  =Implode("\n  | ", $ActualLog  );
    $DesiredLog =Implode("\n  | ", $DesiredLog );
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

Function Validate($v)
{
  $v['P1']['k1']->GetInt();
}
