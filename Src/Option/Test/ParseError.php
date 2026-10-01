<? NameSpace Reformat\Option\Test\ParseError;
If(!IsSet($Loader)) { $CustomTest=__FILE__; Include '../Test.php'; }

Return [
  ['Empty', __LINE__+1, 16,
    'Option'=>'',
    'Logs'=>'ParseError.php(6,16) [Error] Unexpected end of option',
  ],
  ['EmptyWithComment', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      // The comment but no option
      HereDoc,
    'Logs'=>'ParseError.php(11,35) [Error] Unexpected end of option',
  ],
  ['ParseMapItem', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key}
      HereDoc,
    'Logs'=>'ParseError.php(17,10) [Error] ArrayItem: Excepted "=>", ":", "=", "{", ":", "." or "]", given "}"',
  ],
  ['ParseKey', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={;
      HereDoc,
    'Logs'=>'ParseError.php(23,12) [Error] ParseValue: Unknown token: ;',
  ],
  ['ParseMap', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={k1:'v1':
      HereDoc,
    'Logs'=>'ParseError.php(29,19) [Error] Array: Excepted ":", "." or "}", given ":"',
  ],
  ['ParseNumeric', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={k1:-v1}
      HereDoc,
    'Logs'=>'ParseError.php(35,16) [Error] Number expected, given: v1',
  ],
  ['ParseValue', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={k1:v1}
      HereDoc,
    'Logs'=>'ParseError.php(41,15) [Error] ParseValue: Unknown token: v1',
  ],
  ['ParseMap2', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key{k1:'v1' v2}
      HereDoc,
    'Logs'=>'ParseError.php(47,19) [Error] Array: Excepted ":", "." or "}", given "v2"',
  ],
  ['UnparsedData', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      {k1:'v1'} SomeData=3
      HereDoc,
    'Logs'=>'ParseError.php(53,17) [Warning] Has unparsed data: SomeData',
  ],
];