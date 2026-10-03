<? NameSpace Reformat\Option\Test\Validate;
If(!IsSet($Loader)) { $CustomTest=__FILE__; Include '../Test.php'; }

$Validate=Null;
$Res=[
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
      Validate.php(10,12) [Warning] Path.Class.Field.P1: Bool=False has already exist: True was setted in Validate.php(9,12)
      Validate.php(13,15) [Warning] Path.Class.Field.P2.k1: Int=3 has already exist: [1, 2, 3] was setted in Validate.php(11,16)
      Validate.php(15,12) [Warning] Path.Class.Field.P3: Bool=False has already exist: {k1=[1, 2, 3], k2=9} was setted in Validate.php(14,12)
      HereDoc,
  ],
  ['WrongType', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      P1{
        k1=False,
      //k2='Hello',
      }
      HereDoc,
    'Validate'=>&$Validate,
    'Logs'=><<<'HereDoc'
      Validate.php(27,12) [Error] P1.k1: CheckType: Wrong type Bool, required Int
      HereDoc,
  ],
  ['UnusedValues', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      P1{
        k1=100,
        k2='Hello',
      }
      HereDoc,
    'Validate'=>&$Validate,
    'Logs'=><<<'HereDoc'
      Validate.php(40,12) [Warning] P1.k2: This value is unused: "Hello"
      HereDoc,
  ],
];

$Validate=
  Function($v)
  {
    $v['P1']['k1']->CheckType('Int');
  };

UnSet($Validate);

Return $Res;
