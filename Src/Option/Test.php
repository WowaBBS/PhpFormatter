<?
namespace Reformat\Option;

Include_Once __DIR__.'/../_All.php';
//Include_Once '../_All.php';
$Loader->Load_Type('/Debug/Depth'); Use Function WLib\Debug\Depth;

Use Function Reformat\Log;
Use Reformat\FilePos\TInfo As TFilePos;
Use Reformat\Option\TParser;

Set_Time_Limit(1);

If(IsSet($CustomTest))
  $TestList=[
    BaseName($CustomTest, '.php')=>Include $CustomTest
  ];
Else
  $TestList=[
    'Examples'   =>Include 'Test/Examples.php'   ,
    'Debug'      =>Include 'Test/Debug.php'      ,
    'ParseError' =>Include 'Test/ParseError.php' ,
    'FixedBugs'  =>Include 'Test/FixedBugs.php'  ,
    'Validate'   =>Include 'Test/Validate.php'   ,
  ];
  
$LogBuffer=$Loader->Create_Object('/Stream/Buffer');
$LogStream=$Loader->Create_Object('/Log/Logger/Stream', ['Stream'=>$LogBuffer, 'AutoDoneStream'=>False]);

ForEach($TestList As $TestsName=>$Tests)
{
  Log('Log', 'Tests: ', $TestsName);
  $SourceFile=$TestsName.'.php';
  ForEach($Tests As $Test)
  {
    $Name=$Test[0];
    Log('Log', '  Test: ', $Name);
    $FilePos=New TFilePos($SourceFile, $Test[1], $Test[2], $Test[3]?? $Test[2]);
    $Option=$Test['Option'];
    
    $LogBuffer->Clear();
    
    $Parser=New TParser($Option, $FilePos);
    $Parser->Logger=$LogStream;
    $Result=$Parser->Parse();
    If($Validate=$Test['Validate']?? Null)
    {
      $Validate($Result);
      $Result->CheckUnused();
    }
    $Parser->Logger=Null;
    
    $ActualLog=Trim($LogBuffer->Get_Content());
    
    If(($Desired=$Test['Desired']?? Null)!==Null)
    {
      $Actual  =New TDebug($Result  );
      $Desired =New TDebug($Desired );
      
      // Проверяем, совпадает ли результат
      If($Actual != $Desired)
        Log('Error', 'Parse.Result: ')
          ('  Actual  : ', $Actual  )
          ('  Desired : ', $Desired );
      Else If($Test['ShowResult']?? False)
        Log('Log', 'Parse.Result=', $Actual);
    }
    
    If(($DesiredLog=$Test['Logs']?? '')!==False)
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
    //  Log('Log, 'Parse.LogResult=', $ActualLog);//, Depth(100))
    }
  }
}

$LogStream->Done();
$LogBuffer->Done();
$Loader->Done();
