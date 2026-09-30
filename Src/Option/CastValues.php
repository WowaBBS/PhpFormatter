<?
Function Cast_Values()
{
  Return $Values=[
    Null  ,
    True  ,
    False ,
    0     ,
    3.0   ,
    3.14  ,
    1e100 ,
    \NAN  ,
    \INF  ,
    -\INF ,
    ''    ,
    'Str' ,
    '0'   ,
    '3.0' ,
    '3.14',
    '0123',
    '0o123',
    '0x1A',
    '0b11111111',
    '1_234_567',
    []    ,
    [1,2,3],
    ['a'=>'b'],
    New ArrayIterator([]),
    New ArrayIterator([1,2,3]),
    New ArrayIterator(['a'=>'b']),
    //TODO: Does not work
    New Class Implements Stringable{ Function __ToString():String { Return 'Hello'; } },
    New Class Implements Stringable{ Function __ToString():String { Return 15; } },
  ];
}