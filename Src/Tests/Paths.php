<?
//TestPath=\MyNameSpace
NameSpace MyNameSpace;
$argv[]=__FILE__; $LogFile=False; Include '../App.php8';

// WFormat: Debug.Path=True;

/**
 * TestPath=\MyNameSpace\MyClass//Doc
 */
//TestPath=\MyNameSpace\MyClass//Comment
Class MyClass
{
  //TestPath=\MyNameSpace\MyClass::MyConst//Comment
  
  Const MyConst= //TestPath=\MyNameSpace\MyClass::MyConst//Comment
  [
    //TestPath=\MyNameSpace\MyClass::MyConst['Key']//Comment
    'Key'=>[
      //TestPath=\MyNameSpace\MyClass::MyConst['Key']//Comment
    ],
    'Key2'=>[
      //TestPath=\MyNameSpace\MyClass::MyConst['Key2']//Comment
      'Value',
    ],
  //''Key3'=>'Value', //TODO: TestPath=\MyNameSpace\MyClass::MyConst//Code['Key3']
  //"TestPath=\MyNameSpace\MyClass::MyConst//Code//String",
  ];
  
  Var $MyField=[
    //TestPath=\MyNameSpace\MyClass::$MyField['Key']//Comment
    'Key'=>[
      //TestPath=\MyNameSpace\MyClass::$MyField['Key']//Comment
    ],
  ];
  
  Var $MyField2=['Key'=>'Value']; //TestPath=\MyNameSpace\MyClass::$MyField2//Comment
  //TestPath=\MyNameSpace\MyClass::$MyProperty1//Comment
  
  Var $MyProperty1{
    Get { Return 'Hello';
      //TestPath=\MyNameSpace\MyClass::$MyProperty1::Get()//Comment
    }
    Set($v) {
      //TestPath=\MyNameSpace\MyClass::$MyProperty1::Set()//Comment
    }
  }
  Var $MyProperty2{
    Get=>f( //TestPath=\MyNameSpace\MyClass::$MyProperty2::Get()//Comment
    );
    Set=>f( //TestPath=\MyNameSpace\MyClass::$MyProperty2::Set()//Comment
    );
  }

  Function MyFunction()
  {
    //TestPath=\MyNameSpace\MyClass::MyFunction()\Class//Comment
    $logger = new class 
    {
      //TestPath=\MyNameSpace\MyClass::MyFunction()\Class::log()//Comment
      public function log($msg)
      {
        //TestPath=\MyNameSpace\MyClass::MyFunction()\Class::log()=>fn()//Comment
        $fn = function()
        {
          //TestPath=\MyNameSpace\MyClass::MyFunction()\Class::log()=>fn()//Comment
        };
      }
    };
    
  }
}

Function MyFunc() //TestPath=\MyNameSpace\MyFunc()//Comment
{
?>TestPath=\MyNameSpace\MyFunc()//Data<?
}

Function //TestPath=\MyNameSpace\MyFunc2()//Comment
  MyFunc2()
{
?>
TestPath=\MyNameSpace\MyFunc2()//Data
<?
}

//TestPath=\MyNameSpace\MyFunc3()//Comment
Function
  MyFunc3()
{
}

Function() //TestPath=\MyNameSpace=>fn()//Comment
{
};

Function() Use(
  $Hello//TODO: TestPath=\MyNameSpace=>fn()//Comment
)
{
};

fn()=> //TestPath=\MyNameSpace=>fn()//Comment
  5;

Function TestString()
{
  Return [
    "TestPath=\MyNameSpace\TestString()//String",
    'TestPath=\MyNameSpace\TestString()//String',
    "$Data TestPath=\MyNameSpace\TestString()//String",
    <<<'HereDoc'
      TestPath=\MyNameSpace\TestString()//HereDoc
    HereDoc,
    <<<HereDoc
      $Data
      TestPath=\MyNameSpace\TestString()//HereDoc
    HereDoc,
  ];
?>
  TestPath=\MyNameSpace\TestString()//Data
<?php
}

Function TestArgs(
  Int    $Arg1, //TestPath=\MyNameSpace\TestArgs()@Arg:$Arg1//Comment
  String $Arg2='TestPath=\MyNameSpace\TestArgs()@Arg:$Arg2//String',
  //TestPath=\MyNameSpace\TestArgs()//Comment
)
{
}