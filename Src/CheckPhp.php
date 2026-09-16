<?
namespace Reformat;

$checkInPhp ??=False;

Function CheckPhp($result)
{
  Global $checkInPhp;
  If(!$checkInPhp)
    Return True;
  $tmp = tempnam(sys_get_temp_dir(), 'php_lowercase_');

  if ($tmp === false)
  {
    Log('Error', 'Cannot create temporary file');
    ++$errors;
    return false;
  }

  file_put_contents($tmp, $result);

  $command = escapeshellarg(PHP_BINARY)
    .' -l '
    .escapeshellarg($tmp)
    .' 2>&1';

  exec($command, $output, $exitCode);

  unlink($tmp);

  if ($exitCode === 0) return true;
  
  Log('Error' ,'Generated PHP is invalid:')(implode("\n", $output));
  return false;
}