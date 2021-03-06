<?php

function messageOnError()
{
    if (!headers_sent() && error_get_last() !== NULL) {
        ob_start();
        $errorMessage = error_get_last();
        var_dump($errorMessage);
        $errorMessage = ob_get_clean();
        $ErrorContent = "<pre>" . $errorMessage . "</pre>";

        $html = file_get_contents(__DIR__ . "/../../public/error500.html");
        $html = str_replace("{ErrorContent}", $ErrorContent, $html);
        
        if (method_exists('ConfigIgestisGlobalVars', "logFile")) {
            $logFile = ConfigIgestisGlobalVars::logFile();
        } else {
            $logFile = '/var/log/igestis/igestis.log';
        }

        if (php_sapi_name() != "cli") {
            echo $html;
        } else {
            echo $errorMessage;
        }


        if (method_exists('\Igestis\Utils\Debug', 'FileLogger')) {
            \Igestis\Utils\Debug::FileLogger($errorMessage, $logFile);
        } elseif (is_file($logFile) && is_writable($logFile)) {
            file_put_contents($logFile, date("Y-m-d H:i:s") . " - System - " . $errorMessage . "\n", FILE_APPEND);
        }

        exit;
    }
}

register_shutdown_function('messageOnError');


error_reporting(0);

// Start the igestis session
session_start();

// Include common file. This one will include all the necessary files and invoke the autoloaders

header('Content-Type: text/html; charset=utf-8');
// Inclusion des librairies

require_once dirname(__FILE__) . "/no_gettext_compatibility.php";
require_once dirname(__FILE__) . "/wizz_librairie.php";
require_once dirname(__FILE__) . "/smb_librairie.php";
require_once dirname(__FILE__) . "/errors_handler.php";


// Set libraries autoloaderq
require_once dirname(__FILE__) . "/autoload.php";
require_once dirname(__FILE__) . "/../autoload.php";

IgestisAutoloader::register();

//Doctrine\ORM\Tools\Setup::registerAutoloadDirectory(__DIR__ . "/Doctrine/");
//die("bla");
Igestis\Utils\Debug::getInstance();

// Include core application class
require_once __DIR__ . "/coreClasses/Application.php";
Igestis\Utils\Debug::addLog("Loading librairies completed");

