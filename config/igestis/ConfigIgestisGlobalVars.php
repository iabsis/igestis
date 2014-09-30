<?php
/**
 * This class will permitt to set all global variables of the application
 * @Author : Gilles Hemmerlé <gilles.h@iabsis.com>
 */

if(file_exists('/etc/igestis/debian-db.php')) {
    include '/etc/igestis/debian-db.php';
    define("IGESTIS_CORE_MYSQL_HOST", $dbserver);
    define("IGESTIS_CORE_MYSQL_LOGIN", $dbuser);
    define("IGESTIS_CORE_MYSQL_PASSWORD", $dbpass);
    define("IGESTIS_CORE_MYSQL_DATABASE", $dbname);
} else {
    define("IGESTIS_CORE_MYSQL_HOST", '');
    define("IGESTIS_CORE_MYSQL_LOGIN", '');
    define("IGESTIS_CORE_MYSQL_PASSWORD", '');
    define("IGESTIS_CORE_MYSQL_DATABASE", '');
}


define("IGESTIS_CORE_VERSION", file_get_contents(__DIR__ . "/../../version"));
define("IGESTIS_CORE_TEXTDOMAIN", "igestis" .  IGESTIS_CORE_VERSION);


class ConfigIgestisGlobalVars {
    private static $params;
    
    public function __construct() {
        if(empty(static::$params)) {
            self::initFromIniFile();
        }


    }
    
    public function _get($configName) {
        return self::$params[$configName];
    }
    
    public static function configFileFound() {
        return is_file(__DIR__ . "/config.ini") && is_readable(__DIR__ . "/config.ini");
    }
    
    private static function initPHPConfig() {
        if(self::timeZone()) {
            date_default_timezone_set(self::timeZone());
        }
    }

    public static function initFromIniFile() {
        self::$params =  parse_ini_file(__DIR__ . "/default-config.ini");
        $configFileNotFound = false;
        if (!self::configFileFound()) {
            $configFileNotFound = true;
        }
        else {
            self::$params = array_merge(
                self::$params,
                parse_ini_file(__DIR__ . "/config.ini")
            );
        }
        // Manage special mysql fields
        self::$params['MYSQL_HOST'] = isset(self::$params['MYSQL_HOST']) ? self::$params['MYSQL_HOST'] : IGESTIS_CORE_MYSQL_HOST;
        self::$params['MYSQL_LOGIN'] = isset(self::$params['MYSQL_LOGIN']) ? self::$params['MYSQL_LOGIN'] : IGESTIS_CORE_MYSQL_LOGIN;
        self::$params['MYSQL_PASSWORD'] = isset(self::$params['MYSQL_PASSWORD']) ? self::$params['MYSQL_PASSWORD'] : IGESTIS_CORE_MYSQL_PASSWORD;
        self::$params['MYSQL_DATABASE'] = isset(self::$params['MYSQL_DATABASE']) ? self::$params['MYSQL_DATABASE'] : IGESTIS_CORE_MYSQL_DATABASE;
        
        self::setDefaultValues();
        self::initPHPConfig();
        if($configFileNotFound) {
            throw new Exception(\Igestis\I18n\Translate::_("The config.ini file is not found or not readable"));
        }

        if (!parse_ini_file(__DIR__ . "/config.ini")) {
            throw new \Igestis\Exceptions\ConfigException(\Igestis\I18n\Translate::_("The config.ini file contains errors"));
        }

        if (!empty(self::$params['LDAP_SCHEMA'])) {
            if (is_file(__DIR__ . "/ldapSchemas/" . self::$params['LDAP_SCHEMA'] . ".ini")) {
                if (!parse_ini_file(__DIR__ . "/ldapSchemas/" . self::$params['LDAP_SCHEMA'] . ".ini")) {
                    throw new \Igestis\Exceptions\ConfigException(\Igestis\I18n\Translate::_("The config.ini file contains errors"));
                }
                
                self::$params = array_merge(
                    self::$params,
                    parse_ini_file(__DIR__ . "/ldapSchemas/" . self::$params['LDAP_SCHEMA'] . ".ini")
                );
            }            
        }
    }
    
    public static function set($key, $value) {
        self::$params[$key] = $value;
    }
    
    public static function get($key) {
        return isset(self::$params[$key]) ? self::$params[$key] : null;
    }

    public static function debugMode() {
        return empty(self::$params['DEBUG_MODE']) ? false : (bool)self::$params['DEBUG_MODE'];
    }
    
    public static function ldapAdMode() {
        return (bool)self::$params['LDAP_AD_MODE'];
    }
    
    public static function logFolder() {
    	if (substr(self::$params['LOG_FOLDER'], 0, 1) == "/") {

    		return self::$params['LOG_FOLDER'];

    	} else {

    		return self::appliFolder() . '/' . self::$params['LOG_FOLDER'];

    	}
    }
    
    public static function logFile() {
        if (substr(self::$params['LOG_FOLDER'], 0, 1) == "/") {
            return self::$params['LOG_FOLDER'] . "/igestis.log";
        } else {
            return self::appliFolder() . '/' . self::$params['LOG_FOLDER']  . "/igestis.log";
        }
    }
    
    public static function igestisCoreAdmin() {
        return self::$params['IGESTIS_CORE_ADMIN'];
    }
    
    public static function useLdap() {
        return (bool)self::$params['USE_LDAP'];
    }
    
    public static function mysqlHost() {
        return self::$params['MYSQL_HOST'];
    }
    
    public static function mysqlLogin() {
        return self::$params['MYSQL_LOGIN'];
    }
    
    public static function mysqlPassword() {
        return self::$params['MYSQL_PASSWORD'];
    }
    
    public static function mysqlDatabase() {
        return self::$params['MYSQL_DATABASE'];
    }
    
    public static function ldapUri() {
        return self::$params['LDAP_URI'];
    }
    
    public static function ldapBaseDn() {
        return self::$params['LDAP_BASE_DN'];
    }
    
    public static function ldapActiveDirectoryMode() {
        return self::$params['LDAP_AD_MODE'];
    }
    
    public static function ldapVersion() {
        return self::$params['LDAP_VERSION'];
    }
    
    public static function ldapAutoImportUser() {
        return self::$params['LDAP_AUTO_IMPORT_USER'];
    }
    
    public static function ldapCustomBind() {
        return self::$params['LDAP_BIND_FORMAT'];
    }
    
    public static function ldapUserFilter() {
        return self::$params['LDAP_USER_FILTER'];
    }
    
    public static function ldapAdmin() {
        return self::$params['LDAP_ADMIN'];
    }
    
    public static function ldapPassword() {
        return self::$params['LDAP_PASSWORD'];
    }
    
    public static function ldapUsersOu() {
        return self::$params['LDAP_NEW_USERS_DN'];
    }
    
    public static function ldapUserRdn() {
        return self::$params['LDAP_NEW_USER_RDN'];
    }
    
    public static function ldapCustomersOu() {
        return self::$params['LDAP_NEW_CUSTOMERS_DN'];
    }
    
    public static function ldapSuppliersOu() {
        return self::$params['LDAP_NEW_SUPPLIERS_DN'];
    }
    
    public static function cacheFolder() {
        if (substr(self::$params['CACHE_FOLDER'], 0, 1) == "/") {
            return self::$params['CACHE_FOLDER'];
        } else {
            return self::appliFolder() . '/' . self::$params['CACHE_FOLDER'];
        }
    }      
    
    public static function doctrineProxyFolder() {
        return self::cacheFolder() . "/proxies";
    }
    
    public static function minUidNumber() {
        return self::$params['MIN_UID_NUMBER'];
    }
    
    /**
     * 
     * @return Template folder
     * @deprecated since version 2.5
     */
    public static function theme() {
        return "";
    }
    public static function appliFolder() {
        return realpath(dirname(__FILE__) . "/../../");
    }
    
    public static function dataFolder() {
        if (substr(self::$params['DATA_FOLDER'], 0, 1) == "/") {
            return self::$params['DATA_FOLDER'];
        } else {
            return self::appliFolder() . '/' . self::$params['DATA_FOLDER'];
        }
        
    }
    
    public static function serverAddress() {
        return (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . self::$params['WEB_SUBFOLDER'];
    }
    
    public static function encryptKey() {
        return self::$params['ENCRYPT_KEY'];
    }    
    
    public static function rootFolder() {
        return realpath(dirname(__FILE__) . "/../../");
    }
    
    public static function textDomain() {
        return IGESTIS_CORE_TEXTDOMAIN;
    }
    
    public static function version() {
        return IGESTIS_CORE_VERSION;
    }
    
    public static function autoCsrfProtection() {
        return true;  // AUTO_CSRF_PROTECTION
    }
    
    public static function timeZone() {
        return self::$params['TIMEZONE'];
    }
    
    public static function usernameFormat() {
        return self::$params['USERNAME_FORMAT'];
    }
}

