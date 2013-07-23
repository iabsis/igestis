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

define("IGESTIS_CORE_SERVER_ADDRESS", (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ."/igestis");
define("IGESTIS_CORE_ROOT_FOLDER", __DIR__ . "/../../");
define("IGESTIS_CORE_VERSION", "2.2.7");
define("IGESTIS_CORE_TEXTDOMAIN", "igestis" .  IGESTIS_CORE_VERSION);

class ConfigIgestisGlobalVars  {

  /**
   * @var bolean Debug mode for developper only, show a message on top of the page
   * This value is now set on the index.php.
   */
  const DEBUG_MODE = DEBUG_MODE;

  /**
   * @var string log file path.
   * Specify a writeable and existing path for the apache user.
   * (www-data under debian, apache under redhat).
   */
  const LOG_FILE = "/var/log/igestis/igestis.log";


  /**
   * @var varchar used to define the admin login account. If false or not
   * defined, the login will remain root.
   */
  const IGESTIS_CORE_ADMIN = "root";


  /**
   * @var bolean if true, iGestis uses LDAP for authentication and
   * automatically add Employees, Customers and Providers in LDAP.
   */
  const USE_LDAP = true;

  /**
   * @var string Mysql Hostname
   * Replace if install by hand (example : "localhost").
   */
  const MYSQL_HOST = IGESTIS_CORE_MYSQL_HOST;

  /**
   * @var string Mysql login
   * Replace if install by hand (example : "igestis").
   */
  const MYSQL_LOGIN = IGESTIS_CORE_MYSQL_LOGIN;

  /**
   * @var string Mysql Password.
   * Replace if install by hand (example : "123456").
   */
  const MYSQL_PASSWORD = IGESTIS_CORE_MYSQL_PASSWORD;

  /**
   * @var string Mysql Database for igestis.
   * Replace if install by hand (example : "igestis").
   */
  const MYSQL_DATABASE = IGESTIS_CORE_MYSQL_DATABASE;

  /**
   * @var string Ldap URIS used for the LDAP connexion
   * Usually use ldap:/// URI or ldapi///
   */
  const LDAP_URIS = "ldap://localhost:389";

  /**
   * @var string Ldap base used for the LDAP connexion
   * This base is defined when OpenLDAP is installed.
   * If Active Directory is used, place here the domain
   * name, by example for a "domain.local" place
   * "dc=domain,dc=local".
   */
  const LDAP_BASE = "dc=example,dc=local";

  /**
   * @var string LDAP_AD_MODE Is the using Active directory
   * Specify if your directory is an Active Directory. Some
   * user attributes are different between Active Directory
   * and OpenLDAP, iGestis will adapt his configuration. Set
   * true when using Active Directory else set false.
   */
  const LDAP_AD_MODE = false;

  /**
   * @var string LDAP_VERSION Could be 2 or 3,
   * depending of the server you are using
   * Use version 3 for OpenLDAP or Active Directory.
   * Use version 2 for Samba4.
   */
  const LDAP_VERSION = 3;


  /**
   * @var string LDAP_AUTO_IMPORT_USER make igestis
   * automatically create the user in igestis.
   * If a user is present in Directory (AD or OpenLDAP)
   * but not created in iGestis, the user is automatically
   * imported in iGestis if the user use his credential in
   * iGestis.
   */
  const LDAP_AUTO_IMPORT_USER = false;

  /**
   * @var LDAP_CUSTOM_BIND define a custom bind.
   * By default, iGestis will try to login with
   * "uid=%username%" followed by LDAP_USERS_OU
   * (Usually "uid=%username%,ou=Users,dc=example,dc=local)
   * but when using Active Directory, this convention is
   * not working and the bind syntax to login is "%username@example.local"
   * In case of AD use '%u@domain.local' (where %u is the username).
   */
  const LDAP_CUSTOM_BIND = false;

  /**
   * @var LDAP_CUSTOM_FIND define a custom finder.
   * This is the search string used to find a username in OpenLDAP or
   * Active Directory.
   */
  const LDAP_CUSTOM_FIND = '(|(&(objectClass=user)(sAMAccountName=%u))(&(objectClass=posixAccount)(uid=%u)))';

  /**
   * @var string Ldap admin login used for the LDAP connexion.
   * Replace by "Administrator" login if you use Active directory.
   */
  const LDAP_ADMIN = "cn=admin,dc=example,dc=local";

  /**
   * @var string Ldap admin password used for the LDAP connexion.
   * OpenLDAP or Active Directory password for the LDAP_ADMIN account.
   * This is strongly recommand to use a user with writeable privilege
   * on all AD or OpenLDAP folder.
   */
  const LDAP_PASSWORD = "";

  /**
   *
   * @var string Orgnisation Unit where is store the Igestis Users.
   * Default location where new users will be created.
   * Change to "cn=Users,dc=example,dc=local" for Active Directory.
   */
  const LDAP_USERS_OU = "ou=Users,dc=example,dc=local";

  /**
   * @var string LDAP Custom RDN for new user.
   * When iGestis create a new user, you can choose the desired
   * RDN. By default (when set to false)
   * an employee is created with "uid=%username%"
   * with OpenLDAP and "cn=%Firstname Lastname% with Active Directory.
   * value chain can be :
   * %username% for the user name.
   * %firstname% for the first name.
   * %lastname% for the last name.
   * Example for OpenChange : LDAP_USER_RDN = "cn=%username%";
   */
  const LDAP_USER_RDN = false;

  /**
   *
   * @var string Organisation Unit where is store the Igestis Customers.
   * Default location where new customers are created in iGestis.
   */
  const LDAP_CUSTOMERS_OU = "ou=Customers,dc=example,dc=local";

  /**
   *
   * @var string Orgnisation Unit where is store the Igestis suppliers
   * Default location where new suppliers are created in iGestis.
   */
  const LDAP_SUPPLIERS_OU = "ou=Suppliers,dc=example,dc=local";


  /**
   *
   * @var String Igestis cache folder
   */
  const CACHE_FOLDER = "/usr/share/igestis/cache";

  /**
   *
   * @var Integer min uid number in LDAP.
   * Don't try to use UID number under the value defined.
   * This value is not used when LDAP_AD_MODE is true.
   */
  const MIN_UID_NUMBER = 1000;

  /**
   * @var string Theme of iGestis
   * Theme folder located under "theme" folder.
   */
  const THEME = "iabsis_v2";

  /**
   * @var string Location where is installed iGestis.
   * This path doesn't include the folder itself.
   * If iGestis is installed under /usr/share/igestis, place
   * only "/usr/share/".
   */
  const SERVER_FOLDER = "/usr/share/";

  /**
   * @var Folder name where is installed iGestis.
   * This path include only the latest folder.
   * If iGestis is installed under /usr/share/igestis, place
   * only "igestis".
   */
  const APPLI_FOLDER = "igestis";

  /**
   * @var string Location where iGestis must store the documents.
   * This folder must be writeable by apache user.
   * (usually www-data for Debian and apache for Redhat).
   */
  const DATA_FOLDER = "/usr/share/igestis/documents/";

  /**
   * @var string Server URL.
   * If you want to force the URL from where iGestis is runned.
   * If set to IGESTIS_CORE_SERVER_ADDRESS iGestis will automatically
   * detect from the webbrowser.
   */
  const SERVER_ADDRESS = IGESTIS_CORE_SERVER_ADDRESS;

  /**
   * @var string random key used for encryption.
   * Place a random key used for encrypting some sentitive data.
   */
  const ENCRYPT_KEY = "";

  /**
   * @var string Administrative mail redmine user report from.
   * Mail sender of the bug reports are sent.
   */
  const REDMINE_USER = "bug-report@iabsis.com";

  /**
   * @var string Administrative mail to send bug report to redmine.
   * Mail recipient of the bug reports are sent.
   */
  const SUPPORT_IGESTIS = "support-igestis@iabsis.com";

  /**
   * @var string PDF librairie path.
   */
  const TCPDF_DIR = "/usr/share/tcpdf/";

  /**
   * @var string The root folder of the application (ie : /usr/share/igestis/
   */
  const ROOT_FOLDER  = IGESTIS_CORE_ROOT_FOLDER;

  /**
   * @var string The textDomain used by Gettext.
   */
  const textDomain = IGESTIS_CORE_TEXTDOMAIN;

  /**
   * @var version of iGestis (only needed to avoid to clean caches).
   */
  const version = IGESTIS_CORE_VERSION;

  /**
   * Set the csrd protection mode (if true, a csrf tocken will be
   * generated and added to all form and generated urls
   */
  const AUTO_CSRF_PROTECTION = true;

}
