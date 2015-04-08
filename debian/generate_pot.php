<?php

$igestisPath = './';
$modulePrefix = './';

require_once $igestisPath . 'includes/Twig/Autoloader.php';
require_once $igestisPath . 'config/igestis/ConfigIgestisGlobalVars-template.php';
require_once $igestisPath . 'includes/common_librairie.php';

Twig_Autoloader::register();

$tplDir = $modulePrefix . '/templates/';
$loader = new Twig_Loader_Filesystem(array($tplDir, $igestisPath . '/templates'));

$tmpDir = 'tmp/';
$langDir = $modulePrefix . '/lang/locale';

mkdir($tmpDir);
mkdir($langDir);


// force auto-reload to always have the latest version of the template
$twig = new Twig_Environment($loader, array(
    'cache' => $tmpDir,
    'auto_reload' => true
));
$twig->addExtension(new Twig_Extensions_Extension_I18nExtended());
$twig->addExtension(new Twig_Extensions_Extension_Url());
// configure Twig the way you want


// iterate over all your templates
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tplDir), RecursiveIteratorIterator::LEAVES_ONLY) as $file)
{
  // force compilation
  if(!is_file($file)) continue;
  echo str_replace($tplDir, '', $file)."\n";
  $twig->loadTemplate(str_replace($tplDir, '', $file));
  
}

exec("find -name \\*.php > " . $tmpDir . "phpfiles");

exec("find tmp -name \\*.php -exec sed -i 's/>dgettext/ gettext/g' {} \;");

exec("xgettext --default-domain=messages -p " . $langDir . " --from-code=UTF-8 -n --omit-header -L PHP -f " . $tmpDir . "phpfiles");

?>
