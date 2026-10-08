<?
namespace Sample;

sgfsdf sdfw;
$Select->ReadOnly();

aa\bb\cc

include '_All.php';
require '_All.php';
include_once '_All.php';
require_once '_All.php';
$Numeric=[
  1234, // Десятичное число
  0123, // Восьмеричное число (эквивалентно 83 в десятичной системе)
  0o123, // Восьмеричное число (начиная с PHP 8.1.0)
  0x1A, // Шестнадцатеричное число (эквивалентно 26 в десятичной системе)
  0b11111111, // Двоичное число (эквивалентно 255 в десятичной системе)
  1_234_567, // Десятичное число (с PHP 7.4.0)
  1e2,
  'Hello\n',
  "Hello\n",
];
func();
$a->method();
//Test keywords fo functions
$a->Yield  (1);
$a->Echo   (1);
$a->Return (1);
$a->True   (True);
//Test keywords fo fields
$a->Yield  =1;
$a->Echo   =1;
$a->Return =1;
$a->True   =True;
//End test keywords

#[a(b)]
Function a() {}
  'string';
  "string";
  "string$a";
  "string$a[10]";
  "string$a[10][20]";
  "string${a}";
  "string{$a}";
  "Hello ${SubStr('Fear',1,1)} world!\n";
  "Hello {$(SubStr('Fear',1,1))} world!\n";
  "{${'a'.'2'}}";
  "{${a.'2'}}";
  "${foo}";
  "${(foo)}";
  "{${foo}}";
  "{${(foo)}}";
  
  <<<HereDoc
      //HereDoc
    HereDoc;
  <<<'HereDoc'
      //HereDoc
    HereDoc;
  <<<"HereDoc"
      //HereDoc
    HereDoc;

  
  // inline comment
  # inline comment
  /*
   Multiline comment
  */
  /**
     Doc? comment
   */
  /**
   * Doc comment
   * @param float $a
   * return double
   */
function a($a);   
   
  class AAA {}

// В реальности здесь недоступно
yield from [3, 4]; // Делегирование массива

function Generator() {
  yield 1;
  yield from [3, 4]; // Делегирование массива
  yield
    from new ArrayIterator([5, 6]); // Делегирование ArrayIterator
}

$a=['wonderful', 'fullfill',['helpful','pure']];
$e='express';
echo "Hello $a[0] world!\n";
echo "Hello $a[1][20] world!\n";
echo "Hello {$a[2][0]} world!\n";
echo "Hello {$a[2][0+1]} world!\n";
echo "Hello ${SubStr('Fear',1,1)} world!\n";
echo "Hello {${SubStr('Fear',1,1)}} world!\n";

Class MyClass
{
  //TestPath=\MyNameSpace\MyClass
  
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
}

fn()=> //TestPath=fn()
  5;


/*
  Comment is not ended
