<?php

require_once __DIR__ . "/../vendor/igestis/bootstrap.php";

$application = Application::getInstance();

Igestis\Utils\Debug::addLog("Application object instancied");

new IgestisParseRequest($application);