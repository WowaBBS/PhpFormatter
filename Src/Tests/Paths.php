<?
//TestPath=\MyNameSpace
NameSpace MyNameSpace;
$argv[]=__FILE__; $LogFile=False; Include '../App.php8';

// WFormat: Debug.Path=True;

//TestPath=\MyNameSpace\MyClass
Class MyClass
{
  //TestPath=\MyNameSpace\MyClass::MyConst
  
  Const MyConst= //TestPath=\MyNameSpace\MyClass::MyConst
  [
    //TestPath=\MyNameSpace\MyClass::MyConst['Key']
    'Key'=>[
      //TestPath=\MyNameSpace\MyClass::MyConst['Key']
    ],
    'Key2'=>[
      //TestPath=\MyNameSpace\MyClass::MyConst['Key2']
      'Value',
    ],
  ];
  
  Var $MyField=[
    //TestPath=\MyNameSpace\MyClass::$MyField['Key']
    'Key'=>[
      //TestPath=\MyNameSpace\MyClass::$MyField['Key']
    ],
  ];
  
  Var $MyField2=['Key'=>'Value']; //TestPath=\MyNameSpace\MyClass::$MyField2
  //TestPath=\MyNameSpace\MyClass::$MyProperty1
  
  Var $MyProperty1{
    Get { Return 'Hello';
      //TestPath=\MyNameSpace\MyClass::$MyProperty1::Get()
    }
    Set($v) {
      //TestPath=\MyNameSpace\MyClass::$MyProperty1::Set()
    }
  }
  Var $MyProperty2{
    Get=>f( //TestPath=\MyNameSpace\MyClass::$MyProperty2::Get()
    );
    Set=>f( //TestPath=\MyNameSpace\MyClass::$MyProperty2::Set()
    );
  }

  Function MyFunction()
  {
    //TestPath=\MyNameSpace\MyClass::MyFunction()\Class
    $logger = new class 
    {
      //TestPath=\MyNameSpace\MyClass::MyFunction()\Class::log()
      public function log($msg)
      {
        //TestPath=\MyNameSpace\MyClass::MyFunction()\Class::log()=>fn()
        $fn = function()
        {
          //TestPath=\MyNameSpace\MyClass::MyFunction()\Class::log()=>fn()
        };
      }
    };
    
  }
}

Function MyFunc() //TestPath=\MyNameSpace\MyFunc()
{
}

Function //TestPath=\MyNameSpace\MyFunc2()
  MyFunc2()
{
}

//TestPath=\MyNameSpace\MyFunc3()
Function
  MyFunc3()
{
}

Function() //TestPath=\MyNameSpace=>fn()
{
};

fn()=> //TestPath=\MyNameSpace=>fn()
  5;
  