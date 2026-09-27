<?

(Function()
{
  If($Include_Path=Get_Include_Path())
  {
    //Fix include path for include security
    $Include_Path=Str_Replace(['.;', ';.','.:', ':.', '.'], '', $Include_Path);
    
    $Include_Path.=PATH_SEPARATOR;
  }
  Else
    $Include_Path='';
  $Include_Path.=__DIR__;
  Set_Include_Path($Include_Path);
})();

Include 'Log.php'          ;
Include 'CheckPhp.php'     ;
Include 'Config.php'       ;
Include 'Linked/_All.php'  ;
Include 'FilePos/_All.php' ;
Include 'Utils/_All.php'   ;
Include 'Option/_All.php'  ;
Include 'Token/_All.php'   ;
Include 'Filter/_All.php'  ;
Include 'Process/_All.php' ;
Include 'Process.php'      ;
