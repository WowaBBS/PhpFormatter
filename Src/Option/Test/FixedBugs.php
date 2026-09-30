<?
NameSpace Reformat\Option\Test\FixedBugs;

Return [
  ['Option1', __LINE__+1, 16,
    'Option'=>'Filter.Comment.Test=True;',
    'Logs'=>'',
  ],
  ['Option2', __LINE__+1, 16,
    'Option'=>'Filter.Comment.Test=True',
    'Logs'=>'',
  ],
];