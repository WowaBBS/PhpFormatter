<?
NameSpace Reformat\Utils\Stream;

Function Skip($Count)
{
  Return Function(Iterable $List) Use($Count)
  {
    ForEach($List As $k=>$v)
      If(--$Count<0)
        Yield $k=>$v;
  };
}

Function Filter(Callable $CallBack)
{
  Return Function(Iterable $List) Use($CallBack)
  {
    ForEach($List As $k=>$v)
      If($CallBack($v))
        Yield $k=>$v;
  };
}

Function FilterPair(Callable $CallBack)
{
  Return Function(Iterable $List) Use($CallBack)
  {
    ForEach($List As $k=>$v)
      If($CallBack($k, $v))
        Yield $k=>$v;
  };
}

Function Map(Callable $CallBack)
{
  Return Function(Iterable $List) Use($CallBack)
  {
    ForEach($List As $k=>$v)
      Yield $k=>$CallBack($v);
  };
}

Function MapPair(Callable $CallBack)
{
  Return Function(Iterable $List) Use($CallBack)
  {
    ForEach($List As $k=>$v)
      Yield $k=>$CallBack($k, $v);
  };
}

Function MapKey(Callable $CallBack)
{
  Return Function(Iterable $List) Use($CallBack)
  {
    ForEach($List As $k=>$v)
      Yield $CallBack($v, $k)=>$v;
  };
}

//Is not applicable Function ForEach(Callable $CallBack)
Function ForEachVal(Callable $CallBack)
{
  Return Function(Iterable $List) Use($CallBack)
  {
    ForEach($List As $k=>$v)
      $CallBack($v);
  };
}

Function ForEachPair(Callable $CallBack)
{
  Return Function(Iterable $List) Use($CallBack)
  {
    ForEach($List As $k=>$v)
      $CallBack($k, $v);
  };
}

Function Done(Iterable $List)
{
  ForEach($List As $v);
}

Function Counting(Iterable $List)
{
  $Res=0;
  ForEach($List As $v)
    ++$Res;
  Return $Res;
}

Function ToArray(Iterable $List)
{
  Return Iterator_To_Array($List);
}

Function ToIterator(Iterable $List)
{
  //TODO: Chek if it's an iterator
  Return (Function($List)
  {
    ForEach($List As $k=>$v)
      Yield $k=>$v;
  })($List);
}

Function Next(Iterable $List)
{
  If(Is_Array($List)) Return Log('Fatal', 'Array is not supported')->Ret();
  If(!$List->Valid()) Return Null;
  $Res=$List->Current();
  $List->Next();
  Return $Res;
}
