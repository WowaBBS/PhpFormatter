<?
namespace Reformat;

Function ProcessFolder($root)
{
  if(!is_dir($root)) return Log('Error', 'Directory does not exist: ', $root)->Ret(-1);
  
  $filesProcessed = 0;
  $filesChanged = 0;
  $errors = 0;
  
  $iterator = new \RecursiveIteratorIterator(
    new \RecursiveDirectoryIterator(
      $root,
      \FilesystemIterator::SKIP_DOTS
    ),
    \RecursiveIteratorIterator::LEAVES_ONLY
  );
  
  foreach ($iterator as $file)
  {
    if (!$file->isFile()) continue;
    $ext=strtolower($file->getExtension());
  //if ($ext !== 'php') continue;
    if (!str_starts_with($ext, 'php')) continue;
  
    $filename = $file->getPathname();
  
    $result=ProcessFile($filename, $root);
    switch($result)
    {
    case -2: ++$errors; break;
    case -1: ++$errors; ++$filesProcessed; break;
    case  0: ++$filesProcessed; break;
    case  1: ++$filesProcessed; ++$filesChanged; break;
    default: Log('Error', 'Unknown resul code ', $result); break;
    }
  }
  
  echo "\n";
  echo "Files processed: {$filesProcessed}\n";
  echo "Files changed:   {$filesChanged}\n";
  echo "Errors:          {$errors}\n";
}