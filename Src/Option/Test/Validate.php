<?
NameSpace Reformat\Option\Test\Validate;

Function Validate($v)
{
  $v['P1']['k1']->GetInt();
}

Return [
  ['Conflicts', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Path.Class.Field{
        P1=True,
        P1=False,
        P2={k1=[1,2,3]},
        P2.k2=2,
        P2.k1=3,
        P3={k1=[1,2,3]; k2=9},
        P3=False,
      }
      HereDoc,
    'Logs'=><<<'HereDoc'
      Test.php(14,12) [Warning] Path.Class.Field.P1: Bool=False has already exist: True was setted in Test.php(13,12)
      Test.php(17,15) [Warning] Path.Class.Field.P2.k1: Int=3 has already exist: [1, 2, 3] was setted in Test.php(15,16)
      Test.php(19,12) [Warning] Path.Class.Field.P3: Bool=False has already exist: {k1=[1, 2, 3], k2=9} was setted in Test.php(18,12)
      HereDoc,
  ],
  ['WrongType', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      P1{
        k1=False,
      //k2='Hello',
      }
      HereDoc,
    'Validate'=>Validate(...),
    'Logs'=><<<'HereDoc'
      Test.php(31,12) [Warning] P1.k1: Incompatible type Bool, expected Int; Current value is False
      HereDoc,
  ],
  ['UnusedValues', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      P1{
        k1=100,
        k2='Hello',
      }
      HereDoc,
    'Validate'=>Validate(...),
    'Logs'=><<<'HereDoc'
      Test.php(44,12) [Warning] P1.k2: This value is unused: "Hello"
      HereDoc,
  ],
];