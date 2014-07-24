<?php
#!/usr/bin/env php
# app/console

require_once __DIR__ . "/includes/bootstrap.php";

$igestisInstance = Application::getInstance();
$application = new Symfony\Component\Console\Application();
$application->add(new Igestis\Bash\DatabaseUpdater);
$application->add(new Igestis\Bash\i18n());
$hookListener = \Igestis\Utils\Hook::getInstance();

$hookListener->callHook(
    "command",
    new Igestis\Types\HookParameters(array(
        "application" => $application
    ))
);



$application->run();
