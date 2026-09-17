<?
namespace Reformat;

$dontWrite  ??=True;

Function ProcessFile($filename, $root)
{
  $ShortName=$filename;
  If(Str_Starts_With($filename, $root))
    $ShortName=SubStr($filename, StrLen($root));
  $Info=[
    'RootDir'   =>$root,
    'FileName'  =>$filename,
    'ShortName' =>$ShortName,
  ];

  Log('Progress', 'Processing: ', $ShortName);
  
  $source = @file_get_contents($filename);
  
  if ($source === false) Return Log('Error', 'Cannot read file ', $filename)->Ret(-2);
  
  $result = Reformat($source, $Info);
  
  if($result===-1) Return -1;
  if($result===0) Return Log('Progress', 'Processing: ', $ShortName, '  unchanged')->Ret(0);
  if(!Is_String($result)) Return Log('Error', 'Unknown resul code ', $result)->Ret(-1);
  if(!CheckPhp($result)) Return -1;
  
  //«аписываем только после успешной проверки.

  Global $dontWrite;
  If(!$dontWrite)
  If(@file_put_contents($filename, $result) === false)
    Return Log('Error', 'Cannot write file')->Ret(-1);

  Log('Log', 'Processing: ', $ShortName, ' changed');
  Return 1;
}