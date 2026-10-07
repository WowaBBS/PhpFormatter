<? NameSpace MyNameSpace;
$argv[]=__FILE__; $LogFile=False; Include '../App.php8';

// WFormat: Debug.Path=True;

Class MyClass
{ //TestPath=\MyNameSpace\MyClass
  //TestPath=\MyNameSpace\MyClass::MyConst
  
  Const MyConst= //TestPath=\MyNameSpace\MyClass::MyConst
  [
    //TestPath=\MyNameSpace\MyClass::MyConst
    'Key'=>[
      //TestPath=\MyNameSpace\MyClass::MyConst['Key']
    ],
  ];
  
  Var $MyFiled=[
    //TestPath=\MyNameSpace\MyClass::$MyField
    'Key'=>[
      //TestPath=\MyNameSpace\MyClass::$MyField['Key']
    ],
  ];
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
    Set=>f( //TestPath=\MyNameSpace\MyClass::$MyProperty1::Set()
    );
  }

  Function MyFunction()
  {
    //TestPath='\MyNameSpace\MyClass::MyFunction()'
    $logger = new class 
    {
      //TestPath='\MyNameSpace\MyClass::MyFunction()::Class'
      public function log($msg)
      {
        //TestPath='\MyNameSpace\MyClass::MyFunction()::Class::log()'
        $fn = function()
        {
          //TestPath='\MyNameSpace\MyClass::MyFunction()=>Class::log()=>fn()'
        };
      }
    };
    
  }
}

Function MyFunc() //TestPath=\MyNameSpace\MyFunc()
{
}

Function  //TestPath=\MyNameSpace\MyFunc2()
  MyFunc2()
{
}

//TestPath=\MyNameSpace\MyFunc3()
Function
  MyFunc3()
{
}

Function() //TestPath=fn()
{
};

fn()=> //TestPath=fn()
  5;
  