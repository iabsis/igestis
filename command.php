<?php
#!/usr/bin/env php
# app/console

include __DIR__ . "/includes/common_librairie.php";

$application = new Symfony\Component\Console\Application();
$application->add(new Igestis\Bash\IgestisScripts);

$hookListener = \Igestis\Utils\Hook::getInstance();
$hookListener->callHook(
    "command",
    new Igestis\Types\HookParameters(array(
        "application" => $application
    ))
);



$application->run();
