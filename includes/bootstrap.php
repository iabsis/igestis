<?php

function messageOnError()
{

  if(!headers_sent() && error_get_last() !== NULL) {
    ob_start();
    var_dump(error_get_last());
    $ErrorContent = "<pre>" . ob_get_clean() . "</pre>";
    //if(!defined('DEBUG_MODE') || !DEBUG_MODE) $ErrorContent = "";

    $html = file_get_contents(__DIR__ . "/../public/error500.html");
    $html = str_replace("{ErrorContent}", $ErrorContent, $html);
    die($html);
  }
}
register_shutdown_function('messageOnError');

error_reporting(0);

// Start the igestis session
session_start();

// Include common file. This one will include all the necessary files and invoke the autoloaders

header('Content-Type: text/html; charset=utf-8');
// Inclusion des librairies
require_once dirname(__FILE__) . "/wizz_librairie.php";
require_once dirname(__FILE__) . "/smb_librairie.php";
require_once dirname(__FILE__) . "/errors_handler.php";


// Set libraries autoloaderq
require_once dirname(__FILE__) . "/autoloader.php";
require_once dirname(__FILE__) . '/Twig/Autoloader.php';
require_once dirname(__FILE__) . '/Doctrine/Doctrine/ORM/Tools/Setup.php';

Doctrine\ORM\Tools\Setup::registerAutoloadDirectory(__DIR__ . "/Doctrine/");
Twig_Autoloader::register();
IgestisAutoloader::register();
Igestis\Utils\Debug::getInstance();

// Include core application class
require_once __DIR__ . "/coreClasses/Application.php";
Igestis\Utils\Debug::addLog("Loading librairies completed");

