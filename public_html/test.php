<?php
/*
$win = true;

if ($win) {
    $shell = new COM("WScript.Shell");
    $foo = $shell->Run("php -f C:/www3/wt/test2.php", 0, false);
    var_dump($foo);
}
else {
    $cmd = "ls -l";
    $foo = exec($cmd, $output, $return);
    var_dump($foo);
}

exit;

*/

$min_mem = "1024M";
$max_mem = "2048M";

$cmd = '"C:/Program Files/Java/jre7/bin/java.exe" -version'; //-Xms$min_mem -Xmx$max_mem -jar minecraft_server.jar nogui

$cmd = 'whoami';

$res = exec($cmd, $output, $return);

var_dump($res, $output, $return);