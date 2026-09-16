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
   * Doc comment
   */
  /**
     Doc? comment
   */
  class AAA {}

// В реальности здесь недоступно
yield from [3, 4]; // Делегирование массива

function Generator() {
  yield 1;
  yield from [3, 4]; // Делегирование массива
  yield
    from new ArrayIterator([5, 6]); // Делегирование ArrayIterator
}