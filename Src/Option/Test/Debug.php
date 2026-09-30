<? NameSpace Reformat\Option\Test\Debug;
If(!IsSet($Loader)) { $CustomTest=__FILE__; Include '../Test.php'; }

Return [
  ['Position', __LINE__+1, 16,
    'Option'=>'Key=DebugPos',
    'Logs'=>'Debug.php(6,20) [Debug] DebugPos',
  ],
];