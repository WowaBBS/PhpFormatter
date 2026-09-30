<?
NameSpace Reformat\Option\Test\ParseError;

Return [
  ['Empty', __LINE__+1, 16,
    'Option'=>'',
    'Logs'=>'Test.php(6,16) [Error] Unexpected end of option',
  ],
  ['EmptyWithComment', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      // The comment but no option
      HereDoc,
    'Logs'=>'Test.php(11,35) [Error] Unexpected end of option',
  ],
  ['ParseMapItem', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key}
      HereDoc,
    'Logs'=>'Test.php(17,10) [Error] Expected ".", "=", ":", "{" or "=>", given }',
  ],
  ['ParseKey', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={;
      HereDoc,
    'Logs'=>'Test.php(23,12) [Error] Unknown Key token: ;',
  ],
  ['ParseMap', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={k1:'v1':
      HereDoc,
    'Logs'=>'Test.php(29,19) [Error] Map: Excepted ":", "." or "}", given :',
  ],
  ['ParseNumeric', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={k1:-v1}
      HereDoc,
    'Logs'=>'Test.php(35,16) [Error] Number expected, given: v1',
  ],
  ['ParseValue', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key={k1:v1}
      HereDoc,
    'Logs'=>'Test.php(41,15) [Error] Unknown Value token: v1',
  ],
  ['ParseMap2', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Key{k1:'v1' v2}
      HereDoc,
    'Logs'=>'Test.php(47,19) [Error] Map: Excepted ":", "." or "}", given v2',
  ],
];