<?php
/**
 * Script to regenerate the locales after the core update / installation
 */
if(isset($argv[1]) && trim($argv[1])) {
    if(!is_dir($argv[1])) die("This is not a folder");
    require_once $argv[1] . "/vendor/igestis/bootstrap.php";
}
else {
    require_once __DIR__ . "/../vendor/igestis/bootstrap.php";
}

$getTextCaching = new \Igestis\Utils\GetTextCaching();
$getTextCaching->setCachingForAll();
