<?php
require_once __DIR__ . '/../Doctrine/Common/ClassLoader.php';
require_once __DIR__ . '/../../../config.php';

$classLoader = new \Doctrine\Common\ClassLoader('Entities', __DIR__);
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Proxies', __DIR__);
$classLoader->register();

$config = new \Doctrine\ORM\Configuration();
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
//$driverImpl = $config->newDefaultAnnotationDriver(realpath(__DIR__));
//$config->setMetadataDriverImpl($driverImpl);
$driver = new \Doctrine\ORM\Mapping\Driver\XmlDriver(array(
    __DIR__ . '/tmp'             
));
$config->setMetadataDriverImpl($driver);

$config->setProxyDir(__DIR__ . '/Proxies');
$config->setProxyNamespace('Proxies');

$connectionOptions = array(
    'dbname' => "tmp",
    'user' => 'root',
    'password' => '********',
    'host' => \ConfigIgestisGlobalVars::MYSQL_HOST,
    'driver' => 'pdo_mysql',
);

$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
$platform = $em->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));
