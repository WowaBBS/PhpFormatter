<? NameSpace Reformat\Option\Test\FixedBugs;
If(!IsSet($Loader)) { $CustomTest=__FILE__; Include '../Test.php'; }

Return [
  ['Option1', __LINE__+1, 16,
    'Option'=>'Filter.Comment.Test=True;',
  ],
  ['Option2', __LINE__+1, 16,
    'Option'=>'Filter.Comment.Test=True',
  ],
];