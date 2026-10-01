<? NameSpace Reformat\Option\Test\Examples;
If(!IsSet($Loader)) { $CustomTest=__FILE__; Include '../Test.php'; }

$Desired=
  ['Path'=>['Class'=>['Field'=>['P1'=>true,'P2'=>[
    'S1'=>"Helo\n",
    'S2'=>4,
    'S3'=>False,
    'S4'=>'Hello\n',
    'S5'=>[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2,-1,\NAN,\INF,-\INF],
    'S6'=>['k1'=>2, 'k2'=>3.14],
    'S7'=>['k'=>1],
    'S8'=>[2]
  ]]]]];

Return [
  ['ShortFormat', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Path.Class.Field{
        P1=True,
        P2{
          S1="Helo\n",
          S2=4,
          S3=False,
          S4='Hello\n',
          S5=[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2,-1,Nan,Inf,], //Forgot -Inf
          S6={k1=2},
          S6.k2:3.14, //Contains sub key k2
          S7{k:1}, // Merge mode
          S8=[2],
          S5[]=-Inf, //Add forgotten ,-Inf
        }
      }
      HereDoc,
    'Desired'=>$Desired,
    'ShowResult'=>True,
  ],
  ['JsonLike', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      {"Path":{"Class":{"Field":{
        "P1":true,
        "P2":{
          "S1":"Helo\n",
          "S2":4,
          "S3":False,
          "S4":'Hello\n',
          "S5":[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2,-1,nan,inf,-inf],
          "S6":{"k1":2},
          "S6" {"k2":3.14}, //":" is not need for merge //Contains sub key k2
          "S7":{"k":1},
          "S8":[2]
        }
      }}}}
      HereDoc,
    'Desired'=>$Desired,
  ],
  ['JavaScriptLike', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      Path.Class.Field={
        P1: true,
        P2:{
          S1: "Helo\n",
          S2: 4,
          S3: False,
          S4: 'Hello\n',
          S5: [1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2,-1,Nan,Inf,-Inf],
          S6: {k1: 2},
          S6  {k2: 3.14}, //Contains sub key k2
          S7: {k: 1},
          S8: [2],
        }
      }
      HereDoc,
    'Desired'=>$Desired,
  ],
  ['PhpLike.Array', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      ['Path'=>['Class'=>['Field'=>[
        'P1'=>true,
        'P2'=>[
          'S1'=>"Helo\n",
          'S2'=>4,
          'S3'=>False,
          'S4'=>'Hello\n',
          'S5'=>[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2,-1,NAN,INF,-INF],
          'S6'=>['k1'=>2],
          'S6'  {'k2'=>3.14}, //Contains sub key k2
          'S7'=>['k'=>1],
          'S8'=>[2]
        ]
      ]]]]
      HereDoc,
    'Desired'=>$Desired,
  ],
  ['PhpLike.Var', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      $Path['Class']['Field']=[
        'P1'=>True,
        'P2'=>[
          'S1'=>"Helo\n",
          'S2'=>4,
          'S3'=>False,
          'S4'=>'Hello\n',
          'S5'=>[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2,-1,NAN,INF,], //Forgot ,-INF
          'S6'=>['k1'=>2],
          'S6'  {'k2'=>3.14}, //Contains sub key k2
          'S7'=>['k'=>1],
          'S8'=>[2]
        ]
      ];
      $Path['Class']['Field']['P2']['S5'][]=-INF; //Add forgotten ,-INF
      HereDoc,
    'Desired'=>$Desired,
  ],
  ['PhpLike.OOP', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      $Path->Class->Field=[
        'P1'=>True,
        'P2'=>[
          'S1'=>"Helo\n",
          'S2'=>4,
          'S3'=>False,
          'S4'=>'Hello\n',
          'S5'=>[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2,-1,NAN,INF,], //Forgot ,-INF
          'S6'=>['k1'=>2],
          'S6'  {'k2'=>3.14}, //Contains sub key k2
          'S7'=>['k'=>1],
          'S8'=>[2]
        ]
      ];
      $Path->Class->Field['P2']['S5'][]=-INF; //Add forgotten ,-INF
      HereDoc,
    'Desired'=>$Desired,
  ],
/* TODO:
  ['IniLike', __LINE__+2, 7,
    'Option'=><<<'HereDoc'
      [Path.Class.Field]
      
      P1=True
      P2.S1="Helo\n"
      P2.S2=4
      P2.S3=False
      P2.S4='Hello\n'
      P2.S5=[1234,0123,0o123,0x1A,0b11111111,1_234_567,1e2,-1,NAN,INF]
      P2.S5[]=-INF
      P2.S6=['k1'=>2]
      P2.S6=['k2'=>3.14] //Contains sub key k2
      P2.S7=['k'=>1]
      P2.S8=[2]
      
      HereDoc,
    'Desired'=>$Desired,
  ],
  */
];