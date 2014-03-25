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
}

//define("IGESTIS_CORE_SERVER_ADDRESS", (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ."/igestis2.5");

define("IGESTIS_CORE_VERSION", "2.2.7");
define("IGESTIS_CORE_TEXTDOMAIN", "igestis" .  IGESTIS_CORE_VERSION);


class ConfigIgestisGlobalVars {
    private static $params;
    
    private function __construct() {
        // No instanciation for this class
    }
    
    public static function initFromIniFile() {
        
        self::$params = array_merge(
            parse_ini_file(__DIR__ . "/default-config.ini"),
            parse_ini_file(__DIR__ . "/custom-config.ini")
        );
        
        // Manage special mysql fields
        self::$params['MYSQL_HOST'] = isset(self::$params['MYSQL_HOST']) ? self::$params['MYSQL_HOST'] : IGESTIS_CORE_MYSQL_HOST;
        self::$params['MYSQL_LOGIN'] = isset(self::$params['MYSQL_LOGIN']) ? self::$params['MYSQL_LOGIN'] : IGESTIS_CORE_MYSQL_LOGIN;
        self::$params['MYSQL_PASSWORD'] = isset(self::$params['MYSQL_PASSWORD']) ? self::$params['MYSQL_PASSWORD'] : IGESTIS_CORE_MYSQL_PASSWORD;
        self::$params['MYSQL_DATABASE'] = isset(self::$params['MYSQL_DATABASE']) ? self::$params['MYSQL_DATABASE'] : IGESTIS_CORE_MYSQL_DATABASE;
    }
    
    public static function set($key, $value) {
        self::$params[$key] = $value;
    }
    
    public static function get($key) {
        return isset(self::$params[$key]) ? self::$params[$key] : null;
    }

    public static function debugMode() {
        return (bool)self::$params['DEBUG_MODE'];
    }
    
    public static function ldapAdMode() {
        return (bool)self::$params['LDAP_AD_MODE'];
    }
    
    public static function logFile() {
        return self::$params['LOG_FILE'];
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
    
    public static function ldapUris() {
        return self::$params['LDAP_URIS'];
    }
    
    public static function ldapBase() {
        return self::$params['LDAP_BASE'];
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
        return self::$params['LDAP_CUSTOM_BIND'];
    }
    
    public static function ldapCustomFind() {
        return self::$params['LDAP_CUSTOM_FIND'];
    }
    
    public static function ldapAdmin() {
        return self::$params['LDAP_ADMIN'];
    }
    
    public static function ldapPassword() {
        return self::$params['LDAP_PASSWORD'];
    }
    
    public static function ldapUsersOu() {
        return self::$params['LDAP_USERS_OU'];
    }
    
    public static function ldapUserRdn() {
        return self::$params['LDAP_USER_RDN'];
    }
    
    public static function ldapCustomersOu() {
        return self::$params['LDAP_CUSTOMERS_OU'];
    }
    
    public static function ldapSuppliersOu() {
        return self::$params['LDAP_SUPPLIERS_OU'];
    }
    
    public static function cacheFolder() {
        return self::$params['CACHE_FOLDER'];
    }      
    
    public static function minUidNumber() {
        return self::$params['MIN_UID_NUMBER'];
    }
    
    public static function theme() {
        return self::$params['THEME'];
    }
    
    public static function serverFolder() {
        return self::$params['SERVER_FOLDER'];
    }
    
    public static function appliFolder() {
        return self::$params['APPLI_FOLDER'];
    }
    
    public static function dataFolder() {
        return self::$params['DATA_FOLDER'];
    }
    
    public static function serverAddress() {
        return (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . self::$params['WEB_SUBFOLDER'];
    }
    
    public static function encryptKey() {
        return self::$params['ENCRYPT_KEY'];
    }    
    
    public static function rootFolder() {
        return self::$params['ROOT_FOLDER'];
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
}

