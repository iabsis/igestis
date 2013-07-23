<?php

if(!defined("DEBUG_MODE")) define ("DEBUG_MODE", false);

// Open and init the igestis autoloader in order to have access to the ConfigIgestisGlobalVars class
include_once dirname(__FILE__) . "/includes/autoloader.php";
IgestisAutoloader::register();

/**
 * Please don't modify this file, it's only used to ensure compatibilities with old igestis modules.
 * Please use config/igestis/\ConfigIgestisGlobalVars.php instead.
 */

define("MYSQL_HOST", \ConfigIgestisGlobalVars::MYSQL_HOST);
define("MYSQL_LOGIN", \ConfigIgestisGlobalVars::MYSQL_LOGIN);
define("MYSQL_PASSWORD", \ConfigIgestisGlobalVars::MYSQL_PASSWORD);

define("MYSQL_DATABASE", \ConfigIgestisGlobalVars::MYSQL_DATABASE);
define("MYSQL_DATABASE_ENCODAGE", "UTF8"); // UTF8 / ISO-8859-1

define("THEME", \ConfigIgestisGlobalVars::THEME);
define("SERVER_FOLDER", \ConfigIgestisGlobalVars::SERVER_FOLDER);
define("APPLI_FOLDER", \ConfigIgestisGlobalVars::APPLI_FOLDER);
define("SERVER_ADDRESS", \ConfigIgestisGlobalVars::SERVER_ADDRESS);

define("DATA_FOLDER" , \ConfigIgestisGlobalVars::DATA_FOLDER);

define("DEBUG", \ConfigIgestisGlobalVars::DEBUG_MODE);
define("ROWS_PER_PAGE", 30);


/* Some variables */
define ("MANAGIS_MESSAGE_ERROR", 1);
define ("MANAGIS_MESSAGE_INFO", 2);


/* Parametres LDAP */
define("LDAP_DISABLED", !\ConfigIgestisGlobalVars::USE_LDAP);
define("LDAP_BASE", \ConfigIgestisGlobalVars::LDAP_BASE);
define("LDAP_LOGIN", \ConfigIgestisGlobalVars::LDAP_ADMIN);
define("LDAP_PASSWORD", \ConfigIgestisGlobalVars::LDAP_PASSWORD);

/* Paramètrage des montages samba */
define("SAMBA_SERVER_NAME", "127.0.0.1");
define("SSH_ENCRYPT_KEY", \ConfigIgestisGlobalVars::ENCRYPT_KEY);

/* Log file */
// Format Date + Heure - Contenu
define("LOG_FILE", \ConfigIgestisGlobalVars::LOG_FILE);
define("SUPPORT_IGESTIS", \ConfigIgestisGlobalVars::SUPPORT_IGESTIS);
define("REDMINE_USER", \ConfigIgestisGlobalVars::REDMINE_USER);


/* Paramètres PDF */
define("TCPDF_DIR", "/usr/share/tcpdf/");


