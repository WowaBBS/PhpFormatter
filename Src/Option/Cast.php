<?
NameSpace Reformat\Option;

Use Function Reformat\Log;

Function Cast_Bool   ($Value ) { Return @(Bool   )$Value; }
Function Cast_Int    ($Value )
{
  Return Match(GetType($Value)) {
    'NULL'    =>0      ,
    'integer' =>$Value ,
    'boolean' =>$Value? 1:0,
    'double'  =>\Floor($Value)  ,
    'string'  =>@(Int) $Value, //TODO:
    'array'   =>Count($Value)  ,
    'object'  =>0  , //TODO: Iterable and __ToString()
    Default   =>0,
  };
}

Function Cast_Float  ($Value )
{
  Return Match(GetType($Value)) {
    'integer' =>$Value ,
    'double'  =>\Floor($Value)  ,
    'string'  =>@(Float) $Value, //TODO:
    Default   =>Cast_Int($Value),
  };
}

Function Cast_String ($Value )
{ 
//Return @(String )$Value;
  Return Match(GetType($Value)) {
    'NULL'    =>''     ,
    'integer' =>$Value ,
    'boolean' =>$Value? '1':'0',
    'double'  =>@(String)$Value ,
    'string'  =>$Value,
    'array'   =>@(String)$Value , //TODO Implode
    'object'  =>Cast_String_Object($Value),
    Default   =>0,
  };
}

Function Cast_String_Object($Value):String
{
//Log('Debug', 'IsStringable=', $Value InstanceOf Stringable ,' ',$Value);
  If($Value InstanceOf Stringable)
    Return $Value;
  Return 'Object';
}

Function Cast_Array   ($Value ) { Return @(Array  )$Value; }

Function Cast_GetList()
{
  Return [
    'Bool'   =>Cast_Bool   (...),
    'Int'    =>Cast_Int    (...),
    'Float'  =>Cast_Float  (...),
    'String' =>Cast_String (...),
    'Array'  =>Cast_Array  (...),
  ];
}
