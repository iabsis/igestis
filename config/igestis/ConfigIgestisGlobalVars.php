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


define("IGESTIS_CORE_VERSION", file_get_contents(__DIR__ . "/../../version"));
define("IGESTIS_CORE_TEXTDOMAIN", "igestis" .  IGESTIS_CORE_VERSION);


class ConfigIgestisGlobalVars {
    private static $params;
    
    private function __construct() {
        // No instanciation for this class
    }
    
    public static function configFileFound() {
        return is_file(__DIR__ . "/config.ini") && is_readable(__DIR__ . "/config.ini");
    }
    
    private static function initPHPConfig() {
        if(self::timeZone()) {
            date_default_timezone_set(self::timeZone());
        }
    }
    
    private static function setDefaultValues() {
        if(empty(self::$params['CACHE_FOLDER'])) {
            self::$params['CACHE_FOLDER'] = __DIR__ . "/../../cache";
        }
        
        if(empty(self::$params['DATA_FOLDER'])) {
            self::$params['DATA_FOLDER'] = __DIR__ . "/../../documents";
        }
    }

    public static function initFromIniFile() {
        
        self::$params =  parse_ini_file(__DIR__ . "/default-config.ini");
        $configFileNotFound = false;
        if(!self::configFileFound()) {
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
    
    public static function timeZone() {
        return self::$params['TIMEZONE'];
    }
    
    public static function ldapEmployeeSchema() {
        return self::$params['LDAP_EMPLOYEE_SCHEMA'];
    }
    
    public static function ldapEmployeeDn() {
        return self::$params['LDAP_EMPLOYEE_DN'];
    }
    
    public static function ldapEmployeeRdn() {
        return self::$params['LDAP_EMPLOYEE_RDN'];
    }
    
    public static function ldapCustomerSchema() {
        return self::$params['LDAP_CUSTOMER_SCHEMA'];
    }
    
    public static function ldapCustomerDn() {
        return self::$params['LDAP_CUSTOMER_DN'];
    }
    
    public static function ldapCustomerRdn() {
        return self::$params['LDAP_CUSTOMER_RDN'];
    }
    
    public static function ldapSupplierSchema() {
        return self::$params['LDAP_SUPPLIER_SCHEMA'];
    }
    
    public static function ldapSupplierDn() {
        return self::$params['LDAP_SUPPLIER_DN'];
    }
    
    public static function ldapSupplierRdn() {
        return self::$params['LDAP_SUPPLIER_RDN'];
    }
    
    public static function ldapGroupSchema() {
        return self::$params['LDAP_GROUP_SCHEMA'];
    }
    
    public static function ldapGroupDn() {
        return self::$params['LDAP_GROUP_DN'];
    }
    
    public static function ldapGroupRdn() {
        return self::$params['LDAP_GROUP_RDN'];
    }
    
    
}

