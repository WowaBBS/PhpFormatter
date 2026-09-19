<?

sgfsdf sdfw;
$Select->ReadOnly();

include 'All.php';
require 'All.php';
include_once 'All.php';
require_once 'All.php';
func();
$a->method();

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
