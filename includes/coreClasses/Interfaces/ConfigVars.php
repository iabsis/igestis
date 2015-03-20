<?php
namespace Igestis\Interfaces;

/**
* Abstraction for the ConfigModule classes
*/
abstract class ConfigVars
{
    protected static $params;
    
    public function __construct()
    {
        self::initConfigVars();
    }

    public static function initConfigVars()
    {
        if (empty(static::$params)) {
            self::initFromIniFile();
        }
    }
    
    public function _get($configName)
    {
        return self::$params[$configName];
    }
    
    public static function configFileFound()
    {
        return is_file(__DIR__ . "/config.ini") && is_readable(__DIR__ . "/config.ini");
    }
    
    private static function initPHPConfig()
    {
        if (self::timeZone()) {
            date_default_timezone_set(self::timeZone());
        }
    }


}
