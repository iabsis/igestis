<?php

require_once __DIR__ . "/../includes/bootstrap.php";


$application = Application::getInstance();
Igestis\Utils\Debug::addLog("Application object instancied");

new IgestisParseRequest($application);