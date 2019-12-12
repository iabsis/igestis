<?php
/**
 * This class will permitt to set all global variables of the application
 * @Author : Gilles Hemmerlé <gilles.h@iabsis.com>
 */

if (file_exists('/etc/igestis/debian-db.php')) {
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
define("IGESTIS_CORE_TEXTDOMAIN", "igestis" . IGESTIS_CORE_VERSION);

class ConfigIgestisGlobalVars
{
    private static $params;

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

    private static function setDefaultValues()
    {
        if (empty(self::$params['CACHE_FOLDER'])) {
            self::$params['CACHE_FOLDER'] = "cache";
        }

        if (empty(self::$params['DATA_FOLDER'])) {
            self::$params['DATA_FOLDER'] = "documents";
        }

        if (empty(self::$params['LOG_FILE'])) {
            self::$params['LOG_FILE'] = "logs/igestis.log";
        }
    }

    public static function initFromIniFile()
    {

        self::$params = parse_ini_file(__DIR__ . "/default-config.ini");
        $configFileNotFound = false;
        if (!self::configFileFound()) {
            $configFileNotFound = true;
        } else {
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

        if ($configFileNotFound) {
            new \wizz(\Igestis\I18n\Translate::_("The config.ini file is not found or not readable"), \wizz::WIZZ_ERROR);
        } elseif (!parse_ini_file(__DIR__ . "/config.ini")) {
            throw new \Igestis\Exceptions\ConfigException(\Igestis\I18n\Translate::_("The config.ini file contains errors"));
        }
    }

    public static function set($key, $value)
    {
        self::initConfigVars();
        self::$params[$key] = $value;
    }

    public static function get($key)
    {
        self::initConfigVars();
        return isset(self::$params[$key]) ? self::$params[$key] : null;
    }

    public static function debugMode()
    {
        self::initConfigVars();
        return empty(self::$params['DEBUG_MODE']) ? false : (bool) self::$params['DEBUG_MODE'];
    }

    public static function ldapAdMode()
    {
        self::initConfigVars();
        return (bool) self::$params['LDAP_AD_MODE'];
    }

    public static function logFile()
    {
        self::initConfigVars();
        if (substr(self::$params['LOG_FILE'], 0, 1) == "/") {
            return self::$params['LOG_FILE'];
        } else {
            return self::appliFolder() . '/' . self::$params['LOG_FILE'];
        }
    }

    public static function igestisCoreAdmin()
    {
        self::initConfigVars();
        return self::$params['IGESTIS_CORE_ADMIN'];
    }

    public static function useLdap()
    {
        self::initConfigVars();
        return (bool) self::$params['USE_LDAP'];
    }

    public static function mysqlHost()
    {
        self::initConfigVars();
        return self::$params['MYSQL_HOST'];
    }

    public static function mysqlLogin()
    {
        self::initConfigVars();
        return self::$params['MYSQL_LOGIN'];
    }

    public static function mysqlPassword()
    {
        self::initConfigVars();
        return self::$params['MYSQL_PASSWORD'];
    }

    public static function mysqlDatabase()
    {
        self::initConfigVars();
        return self::$params['MYSQL_DATABASE'];
    }

    public static function ldapUris()
    {
        self::initConfigVars();
        return self::$params['LDAP_URIS'];
    }

    public static function ldapBase()
    {
        self::initConfigVars();
        return self::$params['LDAP_BASE'];
    }

    public static function ldapActiveDirectoryMode()
    {
        self::initConfigVars();
        return self::$params['LDAP_AD_MODE'];
    }

    public static function ldapVersion()
    {
        self::initConfigVars();
        return self::$params['LDAP_VERSION'];
    }

    public static function ldapAutoImportUser()
    {
        self::initConfigVars();
        return self::$params['LDAP_AUTO_IMPORT_USER'];
    }

    public static function ldapCustomBind()
    {
        self::initConfigVars();
        return self::$params['LDAP_CUSTOM_BIND'];
    }

    public static function ldapUserFilter()
    {
        self::initConfigVars();
        return self::$params['LDAP_USER_FILTER'];
    }

    public static function ldapGroupFilter()
    {
        self::initConfigVars();
        return self::$params['LDAP_GROUP_FILTER'];
    }

    public static function ldapAdmin()
    {
        self::initConfigVars();
        return self::$params['LDAP_ADMIN'];
    }

    public static function ldapPassword()
    {
        self::initConfigVars();
        return self::$params['LDAP_PASSWORD'];
    }

    public static function ldapUsersOu()
    {
        self::initConfigVars();
        return self::$params['LDAP_USERS_OU'];
    }

    public static function ldapGroupsOu()
    {
        self::initConfigVars();
        return self::$params['LDAP_GROUPS_OU'];
    }

    public static function ldapGroupsNewRdn()
    {
        self::initConfigVars();
        return self::$params['LDAP_GROUPS_NEW_RDN'];
    }

    public static function ldapUserRdn()
    {
        self::initConfigVars();
        return self::$params['LDAP_USER_RDN'];
    }

    public static function ldapCustomersOu()
    {
        self::initConfigVars();
        return self::$params['LDAP_CUSTOMERS_OU'];
    }

    public static function ldapSuppliersOu()
    {
        self::initConfigVars();
        return self::$params['LDAP_SUPPLIERS_OU'];
    }

    public static function cacheFolder()
    {
        self::initConfigVars();
        if (substr(self::$params['CACHE_FOLDER'], 0, 1) == "/") {
            return self::$params['CACHE_FOLDER'];
        } else {
            return self::appliFolder() . '/' . self::$params['CACHE_FOLDER'];
        }
    }

    public static function doctrineProxyFolder()
    {
        self::initConfigVars();
        return self::cacheFolder() . "/proxies";
    }

    public static function minUidNumber()
    {
        self::initConfigVars();
        return self::$params['MIN_UID_NUMBER'];
    }

    public static function minGidNumber()
    {
        self::initConfigVars();
        return self::$params['MIN_GID_NUMBER'];
    }

    /**
     *
     * @return Template folder
     * @deprecated since version 2.5
     */
    public static function theme()
    {
        self::initConfigVars();
        return "";
    }
    public static function appliFolder()
    {
        self::initConfigVars();
        return realpath(dirname(__FILE__) . "/../../");
    }

    public static function dataFolder()
    {
        self::initConfigVars();
        if (substr(self::$params['DATA_FOLDER'], 0, 1) == "/") {
            return self::$params['DATA_FOLDER'];
        } else {
            return self::appliFolder() . '/' . self::$params['DATA_FOLDER'];
        }

    }

    public static function serverAddress()
    {
        self::initConfigVars();
        $url = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
        if (!empty(self::$params['WEB_SUBFOLDER'])) {
            $url .= self::$params['WEB_SUBFOLDER'];
        }
        return $url;
    }

    public static function encryptKey()
    {
        self::initConfigVars();
        return self::$params['ENCRYPT_KEY'];
    }

    public static function rootFolder()
    {
        self::initConfigVars();
        return realpath(dirname(__FILE__) . "/../../");
    }

    public static function textDomain()
    {
        self::initConfigVars();
        return IGESTIS_CORE_TEXTDOMAIN;
    }

    public static function version()
    {
        self::initConfigVars();
        return IGESTIS_CORE_VERSION;
    }

    public static function autoCsrfProtection()
    {
        self::initConfigVars();
        return true; // AUTO_CSRF_PROTECTION
    }

    public static function timeZone()
    {
        self::initConfigVars();
        return self::$params['TIMEZONE'];
    }

    public static function usernameFormat()
    {
        self::initConfigVars();
        return self::$params['USERNAME_FORMAT'];
    }

    public static function passwordFormat()
    {
        self::initConfigVars();
        return self::$params['PASSWORD_FORMAT'];
    }

    public static function disableRememberMe()
    {
        self::initConfigVars();
        return self::$params['DISABLE_REMEMBER_ME'];
    }

    public static function disableRememberPassword()
    {
        self::initConfigVars();
        return self::$params['DISABLE_REMEMBER_PASSWORD'];
    }

    public static function bruteForceMaxAttemps()
    {
        self::initConfigVars();
        return self::$params['BRUTE_FORCE_MAX_ATTEMPTS'];
    }

    public static function bruteForceLockTime()
    {
        self::initConfigVars();
        return self::$params['BRUTE_FORCE_LOCK_TIME'];
    }
}
