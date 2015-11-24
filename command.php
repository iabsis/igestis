<?php
#!/usr/bin/env php
# app/console
require_once __DIR__ . "/vendor/igestis/bootstrap.php";

$igestisInstance = Application::getInstance();
$application = new Symfony\Component\Console\Application();
$application->add(new Igestis\Bash\DatabaseUpdater);
$application->add(new Igestis\Bash\i18n());
$application->add(new Igestis\Bash\CreateMigrationFile());
$application->add(new Igestis\Bash\RegenLocales());

$hookListener = \Igestis\Utils\Hook::getInstance();
$hookListener->callHook(
    "command",
    new Igestis\Types\HookParameters(array(
        "application" => $application
    ))
);



$application->run();
