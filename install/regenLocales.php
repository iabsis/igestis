<?php
/**
 * Script to regenerate the locales after the core update / installation
 */
if(isset($argv[1]) && trim($argv[1])) {
    if(!is_dir($argv[1])) die("This is not a folder");
    require_once $argv[1] . "/includes/bootstrap.php";
}
else {
    require_once __DIR__ . "/../includes/bootstrap.php";
}

$getTextCaching = new \Igestis\Utils\GetTextCaching();
$getTextCaching->setCachingForAll();
