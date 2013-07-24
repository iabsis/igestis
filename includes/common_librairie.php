<?php

if (!defined("DEBUG_MODE"))
    define("DEBUG_MODE", false);

header('Content-Type: text/html; charset=utf-8');
// Inclusion des librairies
include_once dirname(__FILE__) . "/wizz_librairie.php";
include_once dirname(__FILE__) . "/smb_librairie.php";
include_once dirname(__FILE__) . "/errors_handler.php";
include_once dirname(__FILE__) . "/mysql_management.php";
include_once dirname(__FILE__) . "/autoloader.php";
require_once dirname(__FILE__) . '/Twig/Autoloader.php';
Twig_Autoloader::register();
IgestisAutoloader::register();
Igestis\Utils\Debug::getInstance();
// For old modules compatibility
require __DIR__ . "/../config.php";


if (\ConfigIgestisGlobalVars::DEBUG_MODE) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

if (defined("\ConfigIgestisGlobalVars::IGESTIS_CORE_ADMIN") && "\ConfigIgestisGlobalVars::IGESTIS_CORE_ADMIN" !== false) {
    define('CORE_ADMIN', strtolower(\ConfigIgestisGlobalVars::IGESTIS_CORE_ADMIN));
} else {
    define('CORE_ADMIN', strtolower("root"));
}

require_once dirname(__FILE__) . '/Doctrine/Doctrine/ORM/Tools/Setup.php';

$lib = __DIR__ . "/Doctrine/";
Doctrine\ORM\Tools\Setup::registerAutoloadDirectory($lib);

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;

#### On s'occupe des appostrohes dans les tableaux _GET et _POST ####
$magic_quotes_enabled = get_magic_quotes_gpc();

if ($magic_quotes_enabled) {

    $_GET = _stripslashes($_GET);
    $_POST = _stripslashes($_POST);
    $_COOKIE = _stripslashes($_COOKIE);
} ########################################################################
// Manage the HTTP_REFERER without the navigator

class checkScriptEnd {

    public function __destruct() {
        $context = Application::getInstance();
        if ($context->is403error || $context->is404error)
            return;
        $_SESSION['_PREVIOUS_URL'] = $_SERVER['REQUEST_URI'];
    }

}

$scriptEnd = new checkScriptEnd();

function _mysql_real_escape_string($var, $exclude = NULL) {
    if ($var == NULL)
        return NULL;
    if (!$exclude)
        $exclude = array();

    if (is_array($var)) {
        while (list($key, $value) = each($var)) {
            if (in_array($key, $exclude))
                $var[$key] = $var[$key];
            else
                $var[$key] = _mysql_real_escape_string($var[$key]);
        }
        return $var;
    }
    else
        return mysql_real_escape_string($var);
}

function encode_for_table(&$var, $exclude = NULL) {
    if ($var == NULL)
        return NULL;
    if (!$exclude)
        $exclude = array();

    if (is_array($var)) {
        while (list($key, $value) = each($var)) {
            if (in_array($key, $exclude))
                $var[$key] = $var[$key];
            else {
                $var[$key] = @htmlentities((string) $var[$key], ENT_NOQUOTES, "UTF-8");
            }
        }
    }
    else
        $var = @htmlentities($var, ENT_NOQUOTES, "UTF-8");
}

function encode_for_form(&$var, $exclude = NULL) {
    if ($var == NULL)
        return NULL;
    if (!$exclude)
        $exclude = array();

    if (is_array($var)) {
        while (list($key, $value) = each($var)) {
            if (in_array($key, $exclude))
                $var[$key] = $var[$key];
            else
                $var[$key] = @htmlentities((string) $var[$key], ENT_COMPAT, "UTF-8");
        }
    }
    else
        $var = @htmlentities($var, ENT_COMPAT, "UTF-8");
}

function _stripslashes(&$var) {
    if ($var == NULL)
        return NULL;

    if (is_array($var)) {
        while (list($key, $value) = each($var)) {
            $var[$key] = _stripslashes($var[$key]);
        }
        return $var;
    }
    else
        return stripslashes($var);
}

function encrypt_string($string) {
    // Sauvegarde du mot de passe crypté dans mysql
    $td = MCRYPT_RIJNDAEL_128; // Encryption cipher (http://www.ciphersbyritter.com/glossary.htm#Cipher)
    $iv_size = mcrypt_get_iv_size($td, MCRYPT_MODE_ECB); // Dependant on cipher/mode combination (http://www.php.net/manual/en/function.mcrypt-get-iv-size.php)
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); // Creates an IV (http://www.ciphersbyritter.com/glossary.htm#IV)

    $encrypted_string = mcrypt_encrypt($td, \ConfigIgestisGlobalVars::ENCRYPT_KEY, $string, MCRYPT_MODE_ECB, $iv);
    $return = "";
    for ($i = 0; $i < strlen($encrypted_string); $i++) {
        $hex = dechex(ord(substr($encrypted_string, $i, 1)));
        while (strlen($hex) < 2)
            $hex = "0" . $hex;
        $return .= $hex;
    }

    return $return;
}

function decrypt_string($string) {
    // Montage du dossier perso
    $td = MCRYPT_RIJNDAEL_128; // Encryption cipher (http://www.ciphersbyritter.com/glossary.htm#Cipher)
    $iv_size = mcrypt_get_iv_size($td, MCRYPT_MODE_ECB); // Dependant on cipher/mode combination (http://www.php.net/manual/en/function.mcrypt-get-iv-size.php)
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); // Creates an IV (http://www.ciphersbyritter.com/glossary.htm#IV)
    return trim(mcrypt_decrypt($td, \ConfigIgestisGlobalVars::ENCRYPT_KEY, $string, MCRYPT_MODE_ECB, $iv));
}

function unhex($string) {
    $crypted_datas = "";
    // Les données sont en hexa, on la transforme en chaine de caractere
    for ($i = 0; $i < strlen($string); $i+=2) {
        $crypted_datas .= ( chr(hexdec(substr($string, $i, 2))));
    }
    return $crypted_datas;
}

function api_auth_by_string($string) {
    $crypted_datas = "";
    // Les données sont en hexa, on la transforme en chaine de caractere
    for ($i = 0; $i < strlen($string); $i+=2) {
        $crypted_datas .= ( chr(hexdec(substr($string, $i, 2))));
    }

    $string = decrypt_string($crypted_datas);
    $string = explode("#", $string);

    // On calcul si le lien a été fabriqué dans les 5 dernieres minutes
    $day = (int) substr($string[2], 8, 2);
    $month = (int) substr($string[2], 5, 2);
    $year = (int) substr($string[2], 0, 4);
    $hour = (int) substr($string[2], 11, 2);
    $minutes = (int) substr($string[2], 14, 2);
    $secondes = (int) substr($string[2], 17, 2);

    // On crée un timestamp de cette heure
    $passed_time = mktime($hour, $minutes, $secondes, $month, $day, $year);
    // Heure actuelle du serveur GMT
    $time_now = mktime((int) date("H"), (int) date("i"), (int) date("s"), (int) date("m"), (int) date("d"), (int) date("Y"));

    $diff = abs($passed_time - $time_now);
    //if ($diff > 3600)  api_xml_error_die("Session expired");

    $_SESSION['sess_login'] = $string[0];
    $_SESSION['sess_password'] = $string[1];
    unset($_COOKIE['sess_login']);
    unset($_COOKIE['sess_password']);
}

function api_xml_error_die($error) {
    $xml = "<?php
      ml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xml .= "<errors>";
    $xml .= "<message>$error</message>";
    $xml .= "</errors>";

    header("Content-Type:text/xml; charset=UTF-8");
    die($xml);
}

class _readdir {

    var $folder_list = array();
    var $current_row = 0;
    var $folder_ref;

    public function __construct($filename) {
        $this->open_dir($filename);
    }

    function open_dir($filename) {
        $this->filename = $filename;
        $this->current_row = 0;

        $this->folder_ref = opendir($this->filename);
        while ($file = readdir($this->folder_ref)) {
            $this->folder_list[] = $file;
        }

        if (!is_array($this->folder_list))
            return false;
        sort($this->folder_list);
    }

    function next() {
        if (isset($this->folder_list[$this->current_row])) {
            $this->current_row++;
            return $this->folder_list[$this->current_row - 1];
        }
        return false;
    }

}


class Application {

    /**
     *
     * @var Array Reflect users datas from database
     * @deprecated You have now to use object Application::security->contact and Application::security->user to retrieve datas
     */
    public $userprefs;

    /**
     * @deprecated Use application::security->is_loged()
     * @var boolean Know if a user is loged or not
     */
    public $is_loged;
    private $block_list = NULL;
    public $LANG = NULL;
    public $installed_modules = NULL;
    public $modules_config = NULL;
    private $twig_loader = NULL;
    private $wizzMessages = array();
    public $is404error = false;
    public $is403error = false;

    /**
     *
     * @var Twig_Environment Twig environnement
     */
    private $twig_env = NULL;
    private $string_twig_env = NULL;
    public $modulesList = NULL;

    /**
     *
     * @var IgestisSecurity Object that defines the access to the application
     */
    public $security = NULL;

    /**
     *
     * @var EntityManager store the entitymanager to access to the doctrine entities
     */
    public $entityManager = NULL;

    /**
     *
     * @var Igestis\Utils\Debug Object used for the igestis debugging
     */
    public $debugger;

    /**
     * @var Doctrine\DBAL\Logging\DebugStack Logger for doctrine
     */
    private static $doctrineLogger;

    /**
     *
     * @var \Application
     */
    private static $_instance;

    /**
     *
     * @var EntityManager store the entitymanager to access to the doctrine entities
     */
    private static $_entityManager;

    /**
     * Get the single entityManager
     * @return EntityManager
     */
    public static function getEntityMaanger() {
        if (self::$_entityManager === null) {
            self::$_entityManager = self::config_doctrine();            
        }
        return self::$_entityManager;
    }

    /**
     * Return the instance singleton
     * @return \Application
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * Return the twig environnement
     * @return Twig_Environment
     */
    public function getTwigEnvironnement() {
        return $this->twig_env;
    }

    function force_login($login, $password) {
        /*
          $sql = "SELECT  USERS.user_type, CONTACTS.* FROM USERS, CONTACTS WHERE USERS.id=CONTACTS.user_id AND login !='' AND password!='' AND login = '" . mysql_real_escape_string($login) . "' AND (password='" . mysql_real_escape_string(md5($password)) . "' OR password='" . mysql_real_escape_string($password) . "')";
          $req = mysql_query($sql) or $application->message_die("{LANG_Mysql_Error}<br />" . mysql_error());
          if(mysql_num_rows($req)) {
          $this->userprefs = mysql_fetch_array($req);
          $this->set_language($this->userprefs['language']);
          $this->is_loged = true;
          } */
        if (\ConfigIgestisGlobalVars::USE_LDAP) {
            $this->security->authenticate($login, $password);
        } else {
            $this->security->authenticate($login, $password);
        };

        if ($this->security->is_loged()) {
            $this->is_loged = $this->security->is_loged();
            $company = $this->security->user->getCompany();
            $this->userprefs = array(
                "user_label" => $this->security->user->getUserLabel(),
                "user_type" => $this->security->user->getUserType(),
                "company_id" => ($company ? $company->getId() : 0), /* Parameter to fix later */
                "id" => $this->security->contact->getId(),
                "contact_id" => $this->security->contact->getId(),
                "user_id" => $this->security->user->getId(),
                "id_user" => $this->security->user->getId(),
                "login" => $this->security->contact->getLogin(),
                "password" => $this->security->contact->getPassword(),
                "ssh_password" => $this->security->contact->getSshPassword(),
                "civility_code" => $this->security->contact->getCivilityCode(), /* Parameter to fix later */
                "firstname" => $this->security->contact->getFirstname(),
                "lastname" => $this->security->contact->getLastname(),
                "email" => $this->security->contact->getEmail(),
                "language_code" => $this->security->contact->getLanguageCode() /* Parameter to fix later */
            );
        }
    }

    /**
     * Create an object of type EntityManager to work with the doctrine entities
     * @return EntityManager
     */
    public static function config_doctrine() {
        if (\ConfigIgestisGlobalVars::DEBUG_MODE == true) {
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        } else {
            $cache = new \Doctrine\Common\Cache\ApcCache;
        }


        $config = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/../entities"), \ConfigIgestisGlobalVars::DEBUG_MODE);
        $config->setAutoGenerateProxyClasses(true);
        //$logger = new \Doctrine\DBAL\Logging\EchoSQLLogger;

        if (\ConfigIgestisGlobalVars::DEBUG_MODE == true) {
            self::$doctrineLogger = new Doctrine\DBAL\Logging\DebugStack;
            $config->setSQLLogger(self::$doctrineLogger);
        }


        $connectionOptions = array(
            'dbname' => \ConfigIgestisGlobalVars::MYSQL_DATABASE,
            'user' => \ConfigIgestisGlobalVars::MYSQL_LOGIN,
            'password' => \ConfigIgestisGlobalVars::MYSQL_PASSWORD,
            'host' => \ConfigIgestisGlobalVars::MYSQL_HOST,
            'driver' => 'pdo_mysql',
            'charset' => 'utf8',
            'driverOptions' => array(
                1002 => 'SET NAMES utf8'
            )
        );
        
        $entityManager = EntityManager::create($connectionOptions, $config);
                
        $hook = Igestis\Utils\Hook::getInstance();
        $hookParameters = new \Igestis\Types\HookParameters();
        $hookParameters->set('entityManager', $entityManager);
        $hook->callHook("entityManagerInitialized", $hookParameters);

        return $entityManager;
    }

    // ------------------------------------------------------


    function application() {

        $oModulesList = IgestisModulesList::getInstance();
        $this->modulesList = $oModulesList->get();

        self::$doctrineLogger = null;

        $this->debugger = Igestis\Utils\Debug::getInstance();

        $this->userprefs = NULL;
        //$this->is_loged = true;

        $this->entityManager = application::config_doctrine();
        self::$_entityManager = $this->entityManager;


        $templateFoldersList = array(dirname(__FILE__) . "/../templates/");
        $modulesList = IgestisModulesList::getInstance();
        foreach ($modulesList->get() as $module_name => $module) {
            if ($module['igestisVersion'] == 2) {
                if (is_dir($module['folder'] . "/templates/"))
                    $templateFoldersList[] = $module['folder'] . "/templates/";
            }
        }

        $this->twig_loader = new Twig_Loader_Filesystem($templateFoldersList);

        //$this->twig_env = new Twig_Extensions_Extension_I18n();

        $this->twig_env = new Twig_Environment($this->twig_loader, array(
                    'cache' => \ConfigIgestisGlobalVars::DEBUG_MODE ? false : \ConfigIgestisGlobalVars::CACHE_FOLDER . "/twig",
                    'debug' => \ConfigIgestisGlobalVars::DEBUG_MODE
                ));
        $this->twig_env->addExtension(new Twig_Extensions_Extension_I18nExtended());
        $this->twig_env->addExtension(new Twig_Extensions_Extension_Url());
        $this->twig_env->getExtension('core')->setNumberFormat(3, '.', "'");
        $this->twig_env->addFunction(new Twig_SimpleFunction('pad', 'str_pad'));
        if (\ConfigIgestisGlobalVars::DEBUG_MODE) {
            $this->twig_env->addExtension(new Twig_Extension_Debug());
            $this->twig_env->clearCacheFiles();
        }
        
        $this->string_twig_env = clone $this->twig_env;
        $this->string_twig_env->setLoader(new \Twig_Loader_String());
        $this->string_twig_env->getExtension('core')->setNumberFormat(2, '.', "");

        self::$_instance = $this;

        @mysql_connect(\ConfigIgestisGlobalVars::MYSQL_HOST, \ConfigIgestisGlobalVars::MYSQL_LOGIN, \ConfigIgestisGlobalVars::MYSQL_PASSWORD) or $this->message_die(_("Unable to logon on the database"));
        @mysql_select_db(MYSQL_DATABASE) or $this->message_die(_("Unable to connect to the database") . " " . \ConfigIgestisGlobalVars::MYSQL_HOST);
        mysql_query("SET NAMES 'utf8'");

        if (\ConfigIgestisGlobalVars::USE_LDAP) {
            $this->security = \IgestisSecurityLdap::init($this);
        } else {
            $this->security = \IgestisSecurity::init($this);
        };

        $this->is_loged = $this->security->is_loged();

        // This variable is set only to keep compatibility with the old module
        if ($this->security->is_loged()) {
            $company = $this->security->user->getCompany();
            $this->userprefs = array(
                "user_label" => $this->security->user->getUserLabel(),
                "user_type" => $this->security->user->getUserType(),
                "company_id" => ($company ? $company->getId() : 0), /* Parameter to fix later */
                "id" => $this->security->contact->getId(),
                "contact_id" => $this->security->contact->getId(),
                "user_id" => $this->security->user->getId(),
                "id_user" => $this->security->user->getId(),
                "login" => $this->security->contact->getLogin(),
                "password" => $this->security->contact->getPassword(),
                "ssh_password" => $this->security->contact->getSshPassword(),
                "civility_code" => $this->security->contact->getCivilityCode(), /* Parameter to fix later */
                "firstname" => $this->security->contact->getFirstname(),
                "lastname" => $this->security->contact->getLastname(),
                "email" => $this->security->contact->getEmail(),
                "language_code" => $this->security->contact->getLanguageCode() /* Parameter to fix later */
            );
        }


        // Create the language_code pack
        if (is_file(SERVER_FOLDER . "/" . APPLI_FOLDER . "/lang/" . $this->userprefs['language_code'] . ".php")) {// If requested language exists, we open the language_code ...
            require SERVER_FOLDER . "/" . APPLI_FOLDER . "/lang/" . $this->userprefs['language_code'] . ".php";
        } else {// Else, we open the french language as default
            require SERVER_FOLDER . "/" . APPLI_FOLDER . "/lang/FR.php";
        }

        // Listing all the module folder to search all modules
        $directory_to_search = SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/";
        //$dir = opendir($directory_to_search);
        $dir = new _readdir($directory_to_search);

        while ($module_name = $dir->next()) {
            if (is_dir($directory_to_search . $module_name)) {
                // If thats not a good folder, we restart the loop
                if ($module_name == "." || $module_name == "..")
                    continue;
                // If there are not a config.php, we restart the loop
                if (!is_file($directory_to_search . $module_name . "/config.php"))
                    continue;

                // Including the config file of the menu
                include $directory_to_search . $module_name . "/config.php";

                if (!isset(${
                                $module_name}))
                    continue;
                $appli = ${
                        $module_name};
                // If there are no data in the config.php, we restart the loop
                if (!$appli)
                    continue;
                $this->installed_modules[$module_name] = true;
                $this->modules_config[$module_name] = $appli;
            }
        }

        $this->set_language($this->security->contact->getLanguageCode());

        self::$_instance = $this;
    }

    // ------------------------------------------------------

    function is_redirectable($link) {
        $unredirectable_scripts = array(
            "updatedb.php", "common_librairie.php", "applet_librairie.php", "index_common.php", "index_request.php"
        );

        foreach ($unredirectable_scripts as $unredirectable) {
            if (eregi($unredirectable, $link)) {
                return false;
            }
        }

        return true;
    }

    // ------------------------------------------------------


    function set_language($lang) {
        // Create the language_code pack
        if (is_file(SERVER_FOLDER . "/" . APPLI_FOLDER . "/lang/" . $lang . ".php")) {// If requested language_code exists, we open the language_code ...
            require SERVER_FOLDER . "/" . APPLI_FOLDER . "/lang/" . $lang . ".php";
            $this->USED_LANG = $lang;
        } else {// Else, we open the french language_code as default
            require SERVER_FOLDER . "/" . APPLI_FOLDER . "/lang/FR.php";
            $this->USED_LANG = "FR";
        }

        if (isset($lang) == false || $lang == "") {
            $langs = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            list($http_lang, $country) = explode("-", $langs[0]);
            $lang = strtoupper($http_lang);
        }

        // This is used by getext from Twig plugin
        // Set language to French
        $langString = strtolower($lang) . "_" . strtoupper($lang);
        putenv("LC_ALL=$langString");
        setlocale(LC_ALL, $langString);
        setlocale(LC_CTYPE, $langString . '.utf8');
        setlocale(LC_COLLATE, $langString . '.utf8');
        setlocale(LC_MESSAGES, $langString . '.utf8');
        setlocale(LC_NUMERIC, "en_US.utf8");
        // Manage modules locales
        if (\ConfigIgestisGlobalVars::DEBUG_MODE) {
            $getTextCaching = new \Igestis\Utils\GetTextCaching();
            $getTextCaching->setCachingFor("CORE");
        }

        // Auto refresh cache everytin when DEBUG_MODE is activated (for production server, a button will be available for admin to reset the cache)
        reset($this->modulesList);
        foreach ($this->modulesList as $moduleName => $moduleDatas) {
            if ($moduleDatas['igestisVersion'] == 2 && is_dir($moduleDatas['folder'])) {
                // Caching the mo file
                if (\ConfigIgestisGlobalVars::DEBUG_MODE)
                    $getTextCaching->setCachingFor($moduleDatas);
                $configClass = "\\Igestis\\Modules\\" . $moduleDatas['name'] . "\\ConfigModuleVars";
                bindtextdomain($configClass::textDomain, \ConfigIgestisGlobalVars::CACHE_FOLDER . '/lang/locale');
                bind_textdomain_codeset($configClass::textDomain, 'UTF-8');
            }
        }

        // Specify the location of the translation tables
        bindtextdomain(\ConfigIgestisGlobalVars::textDomain, \ConfigIgestisGlobalVars::CACHE_FOLDER . '/lang/locale');
        bind_textdomain_codeset(\ConfigIgestisGlobalVars::textDomain, 'UTF-8');
        // Choose domain
        textdomain(\ConfigIgestisGlobalVars::textDomain);

        // Listing all the module folder to search all modules
        $directory_to_search = SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/";
        $dir = opendir($directory_to_search);

        while ($module_name = readdir($dir)) {
            if (is_dir($directory_to_search . $module_name)) {
                // If thats not a good folder, we restart the loop
                if ($module_name == "." || $module_name == "..")
                    continue;
                // If there are not a config.php, we restart the loop
                if (!is_file($directory_to_search . $module_name . "/config.php"))
                    continue;

                if (is_file($directory_to_search . $module_name . "/lang/" . $lang . ".php")) {// Trying to open the prefered langage to the loged user
                    require $directory_to_search . $module_name . "/lang/" . $lang . ".php";
                } else {// If language_code don't exists, we look for the french default language_code pack
                    if (is_file($directory_to_search . $module_name . "/lang/FR.php")) {
                        require $directory_to_search . $module_name . "/lang/FR.php";
                    }
                }
            }
        }

        $this->LANG = $LANG;
    }

    // ------------------------------------------------------

    function translate_content($text) {
        preg_match_all("(\{(LANG_[A-Za-z]{1}[A-Za-z0-9\_]+)\})", $text, $array);
        for ($i = 0; $i < count($array[0]); $i++) {
            $text = str_replace($array[0][$i], $this->LANG[$array[1][$i]], $text);
        }

        return $text;
    }

    function getCustomCss() {
        if (!$this->security->user->getCompany())
            return false;
        $companyId = $this->security->user->getCompany()->getId();
        $webRootFolder = ConfigIgestisGlobalVars::SERVER_FOLDER . "/" . ConfigIgestisGlobalVars::APPLI_FOLDER . "/web";
        $cssFolder = $webRootFolder . "/" . $companyId;
        $cssFile = $cssFolder . "/style.css";

        if (is_file($cssFile)) {
            return ConfigIgestisGlobalVars::SERVER_ADDRESS . "/web/$companyId/style.css";
        }

        return false;
    }

    // ------------------------------------------------------

    function message_die($message, $is_popup = false, $type = 0) {
        if ($is_popup) {
            $structure = $this->get_html_content("popup_error_message.htm");
            if (!$structure)
                die($this->LANG['LANG_Unable_to_find_the_structure_page']);
        }
        else {
            
            $this->render("pages/messageDie.twig", array("errorMessage" => $message));
            
            /*$structure = $this->get_html_content("error_message.htm");
            if (!$structure)
                die($this->LANG['LANG_Unable_to_find_the_structure_page']);*/
        }

        $CONTENT = $message;
        $TEMPLATE_FOLDER = $this->get_template_folder();
        $replace = array("CONTENT" => $CONTENT, "TEMPLATE_URL" => $this->get_template_url());
        if ($this->is_loged)
            $this->add_var("MENU", $this->generate_menu());

        switch ($type) {
            case MANAGIS_MESSAGE_INFO :
                $replace["MANAGIS_MESSAGE_INFO"] = true;
                $this->set_page_title("{LANG_Notification}");
                break;
            default :
                $replace["MANAGIS_MESSAGE_ERROR"] = true;
                $this->set_page_title("{LANG_Error}");
                break;
        }
        $this->add_vars($replace);
        $this->show_content($structure);
    }

    // ------------------------------------------------------


    function login_form() {// Display the login form and stop the application
        $var = array();
        if ($_GET['redirect'])
            $var['redirect'] = $_GET['redirect'];
        elseif ($_POST['redirect'])
            $var['redirect'] = $_POST['redirect'];
        else
            $var['redirect'] = urlencode($_SERVER["REQUEST_URI"]);


        $this->render("pages/homeLogin.twig", $var);
    }

    function get_template_folder() {
        return SERVER_FOLDER . "/" . APPLI_FOLDER . "/theme/" . \ConfigIgestisGlobalVars::THEME . "/";
    }

    function invalid_form($reason = "Unknown reason") {
        $this->render("pages/invalidForm.twig", array("reason" => $reason));
    }

    // ------------------------------------------------------


    public static function get_template_url() {
        return SERVER_ADDRESS . "/theme/" . \ConfigIgestisGlobalVars::THEME . "/";
    }

    // ------------------------------------------------------

    public static function get_module_url($module_name = "") {
        if (!$module_name)
            $module_name = $_GET['module_name'];
        return SERVER_ADDRESS . "/modules/" . $module_name . "/";
    }

    // ------------------------------------------------------


    function create_content(&$file_to_parse, $occurrences_to_replace) {
        // On commence par remplacer les données générales
        $file_to_parse = str_replace("{TEMPLATE_URL}", $this->get_template_url(), $file_to_parse);

        if (is_array($occurrences_to_replace)) {
            @reset($occurrences_to_replace);
            while (list($key, $value) = each($occurrences_to_replace)) {
                $file_to_parse = str_replace("{" . $key . "}", $value, $file_to_parse);
            }
        }
    }

    // ------------------------------------------------------


    function get_lang_img_folder() {
        return $this->USED_LANG;
    }

    private function get_sidebar() {
        if (!$this->security || !$this->security->isLoged())
            return "";

        $sidebar = new IgestisSidebar($this);

        // Listing all the module folder to search all modules
        $activeRoute = IgestisParseRequest::getActiveRoute();
        $module_name = $activeRoute['Module'];
        $moduleMenuConfig = '\Igestis\Modules\\' . $module_name . '\ConfigInitModule';
        if (class_exists($moduleMenuConfig) && method_exists($moduleMenuConfig, "sidebarSet")) {
            $moduleMenuConfig::sidebarSet($this, $sidebar);
        }

        return $sidebar->get_array();
    }

    private function get_menu() {
        if (!$this->security || !$this->security->isLoged())
            return "";
        $user_rights = $this->security->module_access("CORE", $this->security->user->getId());

        $menu = new IgestisMenu($this);
        if ($this->security->user->getUserType() == "employee" && ($user_rights == "ADMIN" || $user_rights == "TECH")) {
            // Affichage de l'onglet contacts pour les employés Admin ou Tech
            $menu->addItem(_("Contacts"), _("Employees"), "employees_list");
            if ($this->security->contact->getLogin() != CORE_ADMIN)
                $menu->addItem(_("Contacts"), _("Customers"), "customers_list");
            if ($this->security->contact->getLogin() != CORE_ADMIN)
                $menu->addItem(_("Contacts"), _("Suppliers"), "suppliers_list");
            $menu->addItem(_("Administration"), _("My companies"), "companies_list");
            $menu->addItem(_("Administration"), _("Departments"), "departments_list");
            $menu->addItem(_("Administration"), _("Modules and addons"), "modules_list");
            $menu->addItem(_("Administration"), _("Maintenance"), "admin");
        }
        // Listing all the module folder to search all modules
        $modulesList = IgestisModulesList::getInstance();
        $aModulesList = $modulesList->get();
        foreach ($aModulesList as $module_name => $module_datas) {
            if (is_dir($module_datas['folder'])) {
                // First check if this is a module for igestis v2+
                if ($module_datas['igestisVersion'] == 2) {
                    // New modules management
                    $moduleMenuConfig = '\Igestis\Modules\\' . $module_name . '\ConfigInitModule';
                    if (class_exists($moduleMenuConfig)) {
                        $moduleMenuConfig::menuSet($this, $menu);
                    }
                } else {
                    // Old modules management
                    // If there is not a config.php, we restart the loop
                    if (!is_file($module_datas['folder'] . "/config.php"))
                        continue;

                    // Including the config file of the menu
                    include $module_datas['folder'] . "/config.php";

                    if (!isset(${
                                    $module_name}))
                        continue;
                    $appli = ${
                            $module_name};

                    // If there are no data in the config.php, we restart the loop
                    if (!$appli)
                        continue;

                    // Counting menus for this module :
                    $nb_menu = count($appli['module_menu_name']['title']);

                    $link_to_use = -1;
                    $admin_links = false;

                    for ($i = 0; $i < $nb_menu; $i++) {
                        // If not accessible for the loged user type, we restart the loop
                        if ($this->security->user->getUserType() == "client" && (!isset($appli['module_menu_name']['client_access'][$i]) || !$appli['module_menu_name']['client_access'][$i]))
                            continue;

                        // If admin is loged, we will look if he have the required privileges for viewing this page
                        if ($this->security->user->getUserType() == "employee" && (!isset($appli['module_menu_name']['employee_access'][$i]) || !is_array($appli['module_menu_name']['employee_access'][$i])))
                            continue;

                        if ($this->security->user->getUserType() == "employee" && !in_array($this->module_access($module_name), $appli['module_menu_name']['employee_access'][$i]))
                            continue;

                        if (!isset($appli['module_menu_name']['administration_section'][$i]) || !$appli['module_menu_name']['administration_section'][$i]) {
                            $link_to_use = $i;
                            break;
                        } else {
                            $admin_links = true;
                        }
                    }
                    $access = $this->security->module_access($module_name);
                    if ($link_to_use == -1) {
                        if ($admin_links) {
                            $menu->addItem(_("Administration"), $this->translate_content($appli["module_name"]), IgestisConfigController::createUrl("old_module_with_only_admin", array("moduleName" => $module_name)));
                        }
                        continue;
                    }
                    if ($this->security->user->getUserType() == "employee" && !in_array($access, $appli['module_menu_name']['employee_access'][$link_to_use]))
                        continue;

                    //$menu->addItem("Applications", $this->translate_content($appli['module_menu_name']['title'][$link_to_use]), SERVER_ADDRESS . "/index.php?Page=module&module_name=" . $module_name . "&rubrique=" . $link_to_use);
                    $menu->addItem("Applications", $this->translate_content($appli["module_name"]), SERVER_ADDRESS . "/index.php?Page=module&module_name=" . $module_name . "&rubrique=" . $link_to_use);
                }
            }
        }
        return $menu->get_array();
    }

    function generate_menu() {
        // Listing all the module folder to search all modules
        $aMenu = array();
        if (isset($_GET['moduleName'])) {
            $module_name = $_GET['moduleName'];
        } elseif ($_GET['Page'] == "module") {
            $module_name = $_GET['module_name'];
        } else {
            $results = null;
            preg_match("#modules/(.*)/#", $_SERVER['SCRIPT_NAME'], $results);
            $module_name = $results[1];
        }
        $configFile = SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/" . $module_name . "/config.php";

        try {
            include $configFile;
        } catch (Exception $exc) {
            $this->message_die(_("Module not found"));
        }

        $menuOptions = ${$module_name}['module_menu_name'];
        
        if(!$this->security) return array();

        $right = $this->security->module_access($module_name);

        for ($i = 0; $i < count($menuOptions['title']); $i++) {
            if ($this->security->user->getUserType() == "client" && !isset($menuOptions['client_access'][$i]))
                continue;
            //if($this->security->user->getUserType() != "client" && (!isset($menuOptions['client_access'][$i]) || $menuOptions['client_access'][$i] == true)) continue;
            if ($this->security->user->getUserType() == "client" && $menuOptions['client_access'][$i] == false)
                continue;
            if ($this->security->user->getUserType() != "client" && !in_array($right, $menuOptions['employee_access'][$i]))
                continue;
            $aMenu[] = array(
                "active" => $module_name == $_GET['module_name'] && $i == $_GET['rubrique'],
                "link" => SERVER_ADDRESS . "/index.php?Page=module&module_name=" . $module_name . "&rubrique=" . $i,
                "name" => $this->translate_content($menuOptions['title'][$i])
            );
        }
        return $aMenu;
    }

    function set_page_title($title) {
        $this->add_var("GENERAL_TITLE", "Igestis - " . $title);
    }

    function add_block($block, $table = NULL) {
        if (!is_array($table))
            return false;
        @reset($table);
        while (list($key, $value) = each($table)) {
            if (strpos($table[$key], "{") !== false)
                $table[$key] = preg_replace("/\{([a-zA-Z0-9\_]+)\}/e", '$this->LANG["$1"]', $table[$key]);
        }
        $this->block_list[$block][] = $table;
    }

    function add_var($var, $value = true) {
        if ($var == "MENU")
            return;
        if (strpos($value, "{") !== false)
            $value = preg_replace("/\{([a-zA-Z0-9\_]+)\}/e", '$this->LANG["$1"]', $value);
        $this->block_list[$var] = $value;
    }

    function add_vars($value, $auto_replace = true) {
        if (!is_array($value))
            return false;
        @reset($value);
        while (list($key, $data) = each($value)) {
            if ($key == "MENU")
                $data = "";
            if (strpos($data, "{") !== false && $auto_replace) {
                $data = preg_replace("/\{(LANG_[a-zA-Z0-9\_]+)\}/e", '$this->LANG["$1"]', $data);
            }
            $this->block_list[$key] = $data;
        }
    }

    function get_html_content($template_file) {// Read the content of the requested template file and parse the includes into them
        If ($template_file == "overall_header.htm") {
            $this->oldModuleIsMainPage = true;
        }
        $content = @file_get_contents($this->get_template_folder() . $template_file);
        preg_match_all("(<!--[ ]?INCLUDE[ ]?([\w\.\/]+)[ ]?-->)", $content, $array);
        for ($i = 0; $i < count($array[0]); $i++) {
            $content = str_replace($array[0][$i], $this->get_html_content($array[1][$i]), $content);
        }

        return $content;
    }
    
    function rederContent($content, $filename="your_file", $ctype="application/force-download") {
        //Begin writing headers
        header_remove();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        //
        //Use the switch-generated Content-Type
        header("Content-Type: $ctype");
        if ($ctype == "application/force-download") {
            header("Content-Description: File Transfer");
            $header = "Content-Disposition: attachment; filename=" . $filename . ";";
        }

        //Force the download
        header($header);
        header("Content-Transfer-Encoding: binary");
        die($content);
    }

    function renderFile($file, $forceDownload = 0, $customName = '') {
        //First, see if the file exists

        if ((int) $forceDownload != 1)
            $forceDownload = 0;

        if (!is_file($file)) {
            $this->throw404error();
        }

        //Gather relevent info about file
        $len = filesize($file);
        $filename = basename($file);
        $file_extension = strtolower(substr(strrchr($filename, "."), 1));
        if ($forceDownload == 1)
            $ctype = "application/force-download";
        else {
            //This will set the Content-Type to the appropriate setting for the file
            switch ($file_extension) {
                case "pdf": $ctype = "application/pdf";
                    break;
                case "exe": $ctype = "application/octet-stream";
                    break;
                case "zip": $ctype = "application/zip";
                    break;
                case "doc": $ctype = "application/msword";
                    break;
                case "xls": $ctype = "application/vnd.ms-excel";
                    break;
                case "ppt": $ctype = "application/vnd.ms-powerpoint";
                    break;
                case "gif": $ctype = "image/gif";
                    break;
                case "png": $ctype = "image/png";
                    break;
                case "jpeg":
                case "jpg": $ctype = "image/jpg";
                    break;
                case "mp3": $ctype = "audio/mpeg";
                    break;
                case "wav": $ctype = "audio/x-wav";
                    break;
                case "mpeg":
                case "mpg":
                case "mpe": $ctype = "video/mpeg";
                    break;
                case "mov": $ctype = "video/quicktime";
                    break;
                case "avi": $ctype = "video/x-msvideo";
                    break;

                //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
                case "php":
                case "htm":
                case "html":
                    $this->throw403error();
                    break;

                default: $ctype = "application/force-download";
            }
        }
        $replacement['CUSTOM_CSS'] = $this->getCustomCss();

        if ($customName != "")
            $filename = $customName;

        //Begin writing headers
        header_remove();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        //
        //Use the switch-generated Content-Type
        header("Content-Type: $ctype");
        if ($ctype == "application/force-download") {
            header("Content-Description: File Transfer");
            $header = "Content-Disposition: attachment; filename=" . $filename . ";";
        }

        //Force the download
        header($header);
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $len);
        @readfile($file);
        exit;
    }

    /**
     * Méthode permettant d'afficher un template twig
     *
     * @param String $twig_file Fichier twig à utiliser
     * @param Array $replacement Tableau clé / valeur représentant les informations à envoyer au template
     * @param Boolean $return True -> Renvoie le contenu HTML, False -> Affiche le contenu et stop le script (false par défaut)
     * @return String Renvoie le contenu HTML si $return=truc
     */
    function render($twig_file, $replacement, $return = false, $forceDebugToolbar = false) {
        $this->debugger->addLog("Start rendering " . $twig_file);
        $replacement['DEBUG_MODE'] = \ConfigIgestisGlobalVars::DEBUG_MODE;

        $replacement['_get'] = $_GET;
        $replacement['_post'] = $_POST;

        $replacement['TEMPLATE_URL'] = $this->get_template_url();
        $replacement['SERVER_ADDRESS'] = SERVER_ADDRESS;
        $replacement['USER'] = $this->security->user;
        $replacement['CONTACT'] = $this->security->contact;
        $replacement['ADMIN_ACCOUNT'] = strtolower(CORE_ADMIN);

        $newWizz = wizz::show_twig_messages();
        if (is_array($newWizz) && count($newWizz)) {
            $this->wizzMessages = array_merge($this->wizzMessages, $newWizz);
        }

        $replacement['WIZZ'] = $this->wizzMessages;
        $replacement['CURRENT_URL'] = IgestisParseRequest::getActiveRouteUrl();

        $replacement['RIGHTS_LIST'] = NULL;
        $replacement['CORE_VERSION'] = \ConfigIgestisGlobalVars::version;

        // Replace modules version variables in the template
        reset($this->modulesList);
        foreach ($this->modulesList as $moduleName => $moduleDatas) {
            if ($moduleDatas['igestisVersion'] == 2 && is_dir($moduleDatas['folder'])) {
                $configClass = "\\Igestis\\Modules\\" . $moduleDatas['name'] . "\\ConfigModuleVars";
                $replacement[strtoupper($moduleName) . "_VERSION"] = $configClass::version;
            }
        }

        if ($this->security && $this->security->is_loged()) {
            $replacement['RIGHTS_LIST'] = $this->security->get_rights_list($this->security->user->getId());
        }

        $menu = $this->get_menu();

        if ($menu) $replacement['menu_top'] = $menu;
        $sidebar = $this->get_sidebar();        
        if ($sidebar) $replacement['MODULE_SIDEBAR'] = $sidebar;

        if ($this->is_loged)
            $replacement['username'] = strtolower($this->userprefs['login']);


        if ($forceDebugToolbar || preg_match("/debugToolbar.twig/", $twig_file) || $return == false) {
            if (\ConfigIgestisGlobalVars::DEBUG_MODE) {
                foreach (self::$doctrineLogger->queries as $query) {
                    $this->debugger->addDoctrineLog(
                            $query['sql'], $query['params'], $query['types'], $query['executionMS']
                    );
                }

                $this->debugger->addDump($_GET, "_GET");
                $this->debugger->addDump($_POST, "_POST");
                $this->debugger->addDump($_SERVER, "_SERVER");
                $this->debugger->addDump($_REQUEST, "_REQUEST");
                $this->debugger->addDump($_SESSION, "_SESSION");

                $this->twig_env->render($twig_file, $replacement);
                $endScript = $this->debugger->getScriptTime();
                $this->debugger->addLog("View $twig_file is rendered");
                $replacement['EXECUTION_TIME'] = substr($endScript, 0, 5);
                $replacement['DEBUGGING_VARS'] = $this->debugger->getEvents();
            }
        }
        

        if ($return) {
            return $this->twig_env->render($twig_file, $replacement);
        } else {
            $hook = Igestis\Utils\Hook::getInstance();
            $hookParameters = new \Igestis\Types\HookParameters();
            $hookParameters->set('replacements', $replacement);
            $hook->callHook("finalRendering", $hookParameters);
            $replacement = $hookParameters->get("replacements");
            
            die($this->twig_env->render($twig_file, $replacement));
        }
    }
    
    function renderFromString($string, $variables) {
        
        return $this->string_twig_env->render(
          $string,
          $variables
        );
    }

    function show_content($content, $return = false) {
        // Affichage des wizz s'il y en a
        //wizz::show_messages();
        $_SERVER['HTTP_REFERER'] = $_SESSION["_PREVIOUS_URL"];
        $_SESSION["_PREVIOUS_URL"] = $_SERVER['REQUEST_URI'];
        /*
          $igestisv2Menu = $this->render("menu.twig", "", true);
          $content = str_replace("{IGESTISV2_MENU}", $igestisv2Menu, $content);
          if(\ConfigIgestisGlobalVars::DEBUG_MODE) {
          $content = str_replace("{IGESTISV2_DEBUG_TOOLBAR}", $this->render("debugToolbar.twig", null, true), $content);
          }
          else {
          $content = str_replace("{IGESTISV2_DEBUG_TOOLBAR}", "", $content);
          } */

        // Gestion des blocks de modules
        preg_match_all("/<!--[ ]?IF_MODULE_INSTALLED[ ]+([\W\w]*) -->([\W\w]*)<!-- END_MODULE -->/U", $content, $array);
        for ($i = 0; $i < count($array[0]); $i++) {
            if ($this->is_module_installed($array[1][$i]))
                $content = str_replace($array[0][$i], $array[2][$i], $content);
            else
                $content = str_replace($array[0][$i], "", $content);
        }

        // Gestion du header
        /*
          if (function_exists("is_header_set_has_mini"))
          $this->add_var("IS_MINI_HEADER", is_header_set_has_mini());
          else
          $this->add_var("IS_MINI_HEADER", false);
         */

        $this->add_var("DEBUG", \ConfigIgestisGlobalVars::DEBUG_MODE);
        $content = str_replace('"', '\"', $content);

        $tmp_var = "tmpvar_" . super_randomize(10);
        global ${
        $tmp_var};
        ${
                $tmp_var} = clone $this;

        $content = 'global $' . $tmp_var . ";" . "echo \"" . $content . "\";";
        $content = preg_replace("(<!--[ ]?IF[ ]?([\w]+)[ ]?-->)", '";if($' . $tmp_var . '->block_list[\'$1\']) { echo "', $content);
        $content = preg_replace("(<!--[ ]?ENDIF[ ]?([\w]+)[ ]?-->)", '";} echo "', $content);

        // Gestion des blocs
        $content = preg_replace("(<!--[ ]?BEGIN[ ]?([\w]+)[ ]?-->)", '"; for($${1}_i=0;$${1}_i<count($' . $tmp_var . '->block_list[\'$1\']);$${1}_i++) { echo "', $content);
        $content = preg_replace("(<!--[ ]?ELSE[ ]?([\w]+)?[ ]?-->)", '";} else { echo "', $content);
        $content = preg_replace("(<!--[ ]?END[^_][ ]?([\w]+)?[ ]?-->)", '";} echo "', $content);

        $content = preg_replace("/\{([A-Za-z0-9\_]+)\.([A-Za-z0-9\_]+)\}/", '";echo $' . $tmp_var . '->block_list[\'${1}\'][$${1}_i][\'${2}\']; echo "', $content);
        $content = preg_replace("(<!--[ ]?IF[ ]?([A-Za-z0-9\_]+)\.([A-Za-z0-9\_]+)[ ]?-->)", '";if($' . $tmp_var . '->block_list[\'$1\'][$${1}_i][\'${2}\']) { echo "', $content);

        // Global vars to replace
        $content = str_replace("{PAGE_URL}", urlencode($_SERVER["REQUEST_URI"]), $content);
        $content = str_replace("{TEMPLATE_URL}", $this->get_template_url(), $content);
        $content = str_replace("{SHOW_USER_NAME}", $this->userprefs["user_label"], $content);
        $content = str_replace("{MODULE_FOLDER_URL}", $this->get_module_url(), $content);
        $content = str_replace("{SERVER_ADDRESS}", SERVER_ADDRESS, $content);
        $content = str_replace("{RUBR_ID}", $_GET['rubrique'], $content);
        $content = str_replace("{LANG_IMG_FOLDER}", $this->USED_LANG, $content);
        $content = str_replace("{TRANSLATIONS}", str_replace("\'", "'", addslashes($this->render("translations.twig", array(), true))), $content);


        // Affichage du numéro de version
        /*
          exec('dpkg -l | grep igestis-ldap | awk \'{print $3}\'', $output);
          $content = str_replace("{CORE_VERSION}", $output[0], $content);
         */

        global $xajax;
        if ($xajax) {
            if (is_array($this->installed_modules)) {
                while (list($key, $value) = each($this->installed_modules)) {
                    if (is_file(SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/" . $key . "/index_common.php")) {
                        require_once SERVER_FOLDER . "/" . APPLI_FOLDER . "/modules/" . $key . "/index_common.php";
                    }
                }
            }
            ob_start();
            $xajax->printJavascript(SERVER_ADDRESS . "/includes/xajax/");
            $script_xajax = ob_get_clean();
            $content = str_replace("{SCRIPT_AJAX}", str_replace("\'", "'", addslashes($script_xajax)), $content);
        }



        // Gestion des variables
        //$content = preg_replace("(\{([A-Za-z]{1}[A-Za-z0-9\_]+)\})", '";echo $application->block_list[\'$1\']; echo "', $content);
        preg_match_all("(\{([A-Za-z]{1}[A-Za-z0-9\_]+)\})", $content, $array);
        for ($i = 0; $i < count($array[0]); $i++) {
            if (substr($array[0][$i], 0, 6) != "{LANG_")
                $content = str_replace($array[0][$i], '";echo $' . $tmp_var . '->block_list[\'' . $array[1][$i] . '\']; echo "', $content);
        }
        // Gestion des language_codes
        $content = preg_replace("(\{(LANG_[A-Za-z0-9\_]+)\})", '"; if(isset($' . $tmp_var . '->LANG[\'${1}\'])) echo str_replace(\'{TEMPLATE_URL}\',$' . $tmp_var . '->get_template_url(), $' . $tmp_var . '->LANG[\'${1}\']); else echo ${1}; echo "', $content);

        ob_start();
        eval($content);
        $result = ob_get_clean();
        if ($return) {
            return $result;
        } else {
            $newWizz = wizz::show_twig_messages();
            if (is_array($newWizz) && count($newWizz)) {
                $this->wizzMessages = array_merge($this->wizzMessages, $newWizz);
            }

            $values = null;
            preg_match("#<body.*>([\w\W]+)</body>#", $result, $values);
            if ($this->oldModuleIsMainPage) {
                $content = $this->render("pages/oldModule.twig", array(
                    "PAGE_CONTENT" => $values[1],
                    "SCRIPT_XAJAX" => $script_xajax,
                    "SIDEBAR_MENU" => $this->generate_menu(),
                    "WIZZ" => $this->wizzMessages
                        ), true, true);
                die($content);
            } else {
                die($result);
            }
        }

        /* if ($return) {
          ob_start();
          eval($content);
          return ob_get_clean();
          } else {
          //preg_match("#<body.*>(.*)</body>", $content)
          if($this->oldModuleIsMainPage) die("old page");
          eval($content);
          die();
          } */
    }

    function is_module_installed($module) {// Check if the $module is installed or not, return true or false
        if ($this->installed_modules[$module])
            return true;
        return false;
    }

    function module_access($module_name, $user_id = NULL) {// Return the module access for the user in param2 (for the current user if $id_user is null)
        if ($user_id === NULL)
            $user_id = $this->userprefs['user_id'];

        $sql = "SELECT right_code FROM CORE_USERS_RIGHTS WHERE module_name LIKE '" . $module_name . "' AND user_id='" . $user_id . "'";
        $req = mysql_query($sql) or die(mysql_error() . $sql);
        $data = mysql_fetch_array($req);

        return strtoupper($data['right_code']);
    }

    function enable_pagination($nb_enreg) {
        $prefix = "";
        $arguments = explode("&", $_SERVER["QUERY_STRING"]);
        for ($i = 0; $i < count($arguments); $i++) {
            list($key, $value) = explode("=", $arguments[$i]);
            if ($key != "page_num")
                $prefix .= "&" . $key . "=" . $value;
        }
        $prefix = "index.php?" . substr($prefix, 1);

        if ($nb_enreg > ROWS_PER_PAGE) {

            $pages_count = (int) ($nb_enreg / ROWS_PER_PAGE);
            if ($nb_enreg % ROWS_PER_PAGE)
                $pages_count++;

            for ($i = 0; $i < $pages_count; $i++) {
                $replace = array("page_link" => $prefix . "&page_num=" . $i, "link_name" => (string) ($i + 1));
                if ($i == (int) $_GET['page_num'])
                    $replace['selected'] = "class=\"selected\"";
                $this->add_block("PAGINATION", $replace);
            }
        }

        return array("start" => $_GET['page_num'] * ROWS_PER_PAGE, "rows" => ROWS_PER_PAGE);
    }

    /**
     * Generate a 403 error
     */
    public function throw403error() {
        header("HTTP/1.0 403 Forbidden");
        $this->is403error = true;
        $controller = new HomePageController($this);
        $controller->error403();
        exit;
    }

    /**
     * Generate a 404 error
     */
    public function throw404error() {
        $this->is404error = true;
        header("HTTP/1.0 404 Not Found");
        $controller = new HomePageController($this);
        $controller->error404();
        exit;
    }

    public function throw401error() {
        $this->is401error = true;
        header('HTTP/1.1 401 Unauthorized');
        //$controller = new HomePageController($this);
        //$controller->error404();
        exit;
    }

}

function get_client_type_code_list($code = "") {
    $return = "";
    $selected = " selected=\"selected\"";
    $sql = "SELECT * FROM CORE_CLIENT_TYPE ORDER BY code";
    $req = mysql_query($sql) or die(mysql_error() . $sql);
    while ($data = mysql_fetch_array($req)) {
        $return .= "<option value=\"" . $data['code'] . "\"";
        if ($data['code'] == $code) {
            $return .= " selected=\"selected\"";
            $selected = "";
        }
        $return .= ">{LANG_CLIENT_TYPE_" . $data['code'] . "}</option>";
    }
    $return = "<option value=\"\" $selected>-- {LANG_CLIENT_TYPE_SELECT} --</option>" . $return;

    return $return;
}

###################################################################################################

function get_country_list($code = "") {
    $return = "";
    $selected = " selected=\"selected\"";
    $sql = "SELECT * FROM CORE_COUNTRIES";
    $req = mysql_query($sql) or die(mysql_error() . $sql);
    while ($data = mysql_fetch_array($req)) {
        $return .= "<option value=\"" . $data['code'] . "\"";
        if ($data['code'] == $code) {
            $return .= " selected=\"selected\"";
            $selected = "";
        }
        $return .= ">{LANG_COUNTRY_" . $data['code'] . "}</option>";
    }
    $return = "<option value=\"\" $selected>-- {LANG_COUNTRY_SELECT} --</option>" . $return;

    return $return;
}

###################################################################################################

function get_language_list($code = "") {
    $return = "";
    $selected = " selected=\"selected\"";
    $sql = "SELECT * FROM CORE_LANGUAGES";
    $req = mysql_query($sql) or die(mysql_error() . $sql);
    while ($data = mysql_fetch_array($req)) {
        $return .= "<option value=\"" . $data['code'] . "\"";
        if ($data['code'] == $code) {
            $return .= " selected=\"selected\"";
            $selected = "";
        }
        $return .= ">{LANG_LANGUAGE_" . $data['code'] . "}</option>";
    }
    $return = "<option value=\"\" $selected>-- {LANG_LANGUAGE_SELECT} --</option>" . $return;
    return $return;
}

###################################################################################################

function get_civility_code_list($code = "") {
    $return = "";
    $selected = " selected=\"selected\"";
    $sql = "SELECT * FROM CORE_CIVILITIES ORDER BY code";
    $req = mysql_query($sql) or die(mysql_error() . $sql);
    while ($data = mysql_fetch_array($req)) {
        $return .= "<option value=\"" . $data['code'] . "\"";
        if ($data['code'] == $code) {
            $return .= " selected=\"selected\"";
            $selected = "";
        }
        $return .= ">{LANG_MARITAL_STATUS_" . $data['code'] . "}</option>";
    }
    $return = "<option value=\"\" $selected>-- {LANG_MARITAL_STATUS_SELECT} --</option>" . $return;

    return $return;
}

###################################################################################################

function get_company_list($company_id = "") {
    $return = "";
    $nb_companies = 0;

    $sql = "SELECT id, name FROM CORE_COMPANIES ORDER BY name";
    $req = mysql_query($sql) or die(mysql_error() . $sql);
    while ($data = mysql_fetch_array($req)) {
        $nb_companies++;
        $return .= "<option value=\"" . $data['id'] . "\"";
        if ($data['id'] == $company_id)
            $return .= " selected=\"selected\"";
        $return .= ">" . $data['name'] . "</option>";
    }
    return array($nb_companies, $return);
}

###################################################################################################

function convert_minutes_and_hours($temp) {
    $negatif = false;
    if ($temp < 0)
        $negatif = true;
    $temp = abs($temp);

    $minute = $temp % 60;
    $heure = ($temp - $minute) / 60;

    if ($heure < 10)
        $heure = "0" . $heure;
    if ($minute < 10)
        $minute = "0" . $minute;
    if ($negatif)
        return "-" . $heure . ":" . $minute;
    else
        return $heure . ":" . $minute;
}

###################################################################################################

function convert_hours_to_secondes($period) {// Get a period as (-)h:s expression and return the number of minutes
    if (!ereg("^[\-]?[0-9]{1,3}[\:]{1}[0-9]{1,2}$", $period))
        return 0;

    $negatif = false;

    list($h, $m) = explode(":", $period);

    if (substr($h, 0, 1) == "-") {
        $negatif = true;
        $h = str_replace("-", "", $h);
    }

    delete_first_caract($h, "0");
    delete_first_caract($m, "0");

    $minutes = ($h * 60) + $m;

    if ($negatif)
        return -1 * $minutes;
    else
        return $minutes;
}

###################################################################################################

function delete_first_caract(&$var, $caract) {
    if (isset($var) && !is_array($var)) {
        for ($i = 0; $i < strlen($var); $i++) {
            if (substr($var, 0, 1) == $caract)
                $var = substr($var, 1);
            else
                break;
            if ($var == "")
                break;
        }
    }
}

###################################################################################################

function convert_date_en_to_fr($date) {
    // Cette fonction convertir une date qui est au format anglais au format français ...
    $date = str_replace('-', '/', $date);
    $tb = explode("/", $date);
    return $tb[2] . "/" . $tb[1] . "/" . $tb[0];
}

###################################################################################################

function convert_date_fr_to_en($date) {
    // Cette fonction convertir une date qui est au format français au format anglais ...
    $date = str_replace('-', '/', $date);
    $tb = explode("/", $date);

    return $tb[2] . "-" . $tb[1] . "-" . $tb[0];
}

###################################################################################################

function cut_sentence($Texte, $nbcar = 0) {
    /*
      Fonction de découpage d'une chaîne et affichage de "..." à la fin du texte retourné
      afin de pouvoir afficher un "résumé" du texte dans un tableau ...
     */
    if (strlen($Texte) > $nbcar && (0 != $nbcar)) {
        $Tmp_Tb = explode(' ', $Texte);
        $Tmp_Count = 0;
        $Tmp_O = '';

        while (list(, $v) = each($Tmp_Tb)) {
            if (strlen($Tmp_O) >= $nbcar)
                break;
            $Tmp_O .= $v . ' ';
        }

        $Tmp_O = substr($Tmp_O, 0, strlen($Tmp_O) - 1);
        if (count($Tmp_Tb) > 1)
            $Tmp_O .= '...';
    }
    else
        $Tmp_O = $Texte;

    return $Tmp_O;
}

######################################################################################################

function scanner_list() {// Retourne le code HTML du menu déroulant listant les scanners du serveur
    unset($data);
    exec("scanimage -L", $data);
    if ($data[0] == "" || ereg("No scanners were identified", $data[0]))
        $liste_scanner = "";
    else {
        $liste_scanner = "";

        for ($i = 0; $i < count($data); $i++) {
            // Recherche de la chaine entre les ``
            $c = "";
            $j = 0;
            $adresse_scanner = "";
            $label_scanner = "";
            $chaine_complete = $data[$i];
            // Déplacement jusqu'au début de la chaine definissant le scanner
            while ($c != "`" && $j <= strlen($chaine_complete)) {
                $c = $chaine_complete[$j];
                $j++;
            }
            $c = "";
            // Enregistrement de la chaine de l'adresse de scanner et deplacement jusqu'à la fin de la chaine
            while ($c != "'" && $j <= strlen($chaine_complete)) {
                $c = $chaine_complete[$j];
                if ($c != "'")
                    $adresse_scanner .= $c;
                $j++;
            }
            $j+=6;
            if (strlen($chaine_complete) > $j)
                $label_scanner = substr($chaine_complete, $j);
            else
                $label_scanner = "No label";

            // On recupère le nom du serveur
            $tmp = str_replace("net:", "", $adresse_scanner);
            $serveur = "";
            $i = 0;
            while ($tmp[$i] != ":" && $i < strlen($tmp)) {
                $serveur .= $tmp[$i];
                $i++;
            }

            $liste_scanner .= "<option value=\"" . urlencode($adresse_scanner) . "\">" . cut_sentence($label_scanner, 25) . " (" . $serveur . ")</option>\n";
        }
    }
    return $liste_scanner;
}

######################################################################################################

function scanner($adresse_scanner, $emplacement, $nom_fichier, $format = "") {
    $return_var = NULL;
    $is_smb = false;

    if (eregi("^smb://", $emplacement))
        $is_smb = true;

    // On initialise $format sur A4 si la valeur n'est pas initialisé.
    if ($format == "")
        $format = "A4";

    // Suivant le format, on adapte les coordonnées de scan.
    switch ($format) {
        case "A4":
            $sheet = "-l 0 -t 0 -x 215 -y 297";
            break;
        case "check":
            $sheet = "-l 2 -t 2 -x 175 -y 78";
            break;
        default:
            $sheet = "-l 0 -t 0 -x 215 -y 297";
            break;
    };


    if ($is_smb) {
        $real_folder = $emplacement;
        $real_filename = $nom_fichier;

        $emplacement = "/tmp/";

        do {
            $nom_fichier = super_randomize(15);
        } while (is_file($emplacement . $nom_fichier));
    }

    $script = "scanimage --device=" . escapeshellarg(urldecode($adresse_scanner)) . " --source \"ADF Front\" --format=pnm --mode=Gray --resolution=100 " . $sheet . "| cjpeg > " . escapeshellarg($emplacement . "/" . $nom_fichier);
    system($script, $return_var);

    if ($return_var) {
        $script = "scanimage --device=" . escapeshellarg(urldecode($adresse_scanner)) . " --source \"Flatbed\" --format=pnm --mode=Gray --resolution=100 " . $sheet . "| cjpeg > " . escapeshellarg($emplacement . "/" . $nom_fichier);
        system($script, $return_var);
    }

    if ($is_smb) {
        @copy($emplacement . $nom_fichier, $real_folder . "/" . $real_filename);
        @unlink($emplacement . $nom_fichier);
    }

    if (($is_smb && !smb::is_file($real_folder . "/" . $real_filename)) || !is_file($emplacement . "/" . $nom_fichier)) {
        new wizz("La numérisation a échoué", WIZZ_ERROR, "", 5);
    }
    else
        new wizz("La numération est terminée", WIZZ_SUCCESS, "", 5);

    if ($return_var)
        return false;
    else
        return true;
}

######################################################################################################

function super_randomize($nb_caract) {
    $super_randomize_sting = "";
    srand((float) microtime() * 1000000);

    $arr = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

    for ($i = 0; $i < $nb_caract; $i++) {
        $randval = round(rand(1, 2));

        switch ($randval) {
            case '1':
                $val = round(rand(0, 25));
                $super_randomize_string .= $arr[$val];
                break;
            case'2':
                $val = round(rand(0, 9));
                $super_randomize_string .= $val;
                break;
        }
    }

    return $super_randomize_string;
}

######################################################################################################

function verify_file_type($target, $file_type = "image") {
    $mime_type = mime_content_type($target);
    $accepted_image_mime_type = array("image/jpeg");

    switch ($file_type) {
        case "image" : if (!in_array($mime_type, $accepted_image_mime_type))
                return false; break;
        default : return false;
            break;
    }

    return true;
}

######################################################################################################

if (!function_exists('mime_content_type')) {

    function mime_content_type($filename) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mimetype;
    }

} ######################################################################################################

function dl_file($file) {
    //First, see if the file exists

    if (!is_file($file)) {
        die("<b>404 File not found!</b>");
    }

    //Gather relevent info about file
    $len = filesize($file);
    $filename = basename($file);
    $file_extension = strtolower(substr(strrchr($filename, "."), 1));

    //This will set the Content-Type to the appropriate setting for the file
    switch ($file_extension) {
        case "pdf": $ctype = "application/pdf";
            break;
        case "exe": $ctype = "application/octet-stream";
            break;
        case "zip": $ctype = "application/zip";
            break;
        case "doc": $ctype = "application/msword";
            break;
        case "xls": $ctype = "application/vnd.ms-excel";
            break;
        case "ppt": $ctype = "application/vnd.ms-powerpoint";
            break;
        case "gif": $ctype = "image/gif";
            break;
        case "png": $ctype = "image/png";
            break;
        case "jpeg":
        case "jpg": $ctype = "image/jpg";
            break;
        case "mp3": $ctype = "audio/mpeg";
            break;
        case "wav": $ctype = "audio/x-wav";
            break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype = "video/mpeg";
            break;
        case "mov": $ctype = "video/quicktime";
            break;
        case "avi": $ctype = "video/x-msvideo";
            break;

        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html":
            die("<b>Cannot be used for " . $file_extension . " files!</b>");
            break;

        default: $ctype = "application/force-download";
    }

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");

    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");

    //Force the download
    $header = "Content-Disposition: attachment; filename=" . $filename . ";";
    header($header);
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . $len);

    @readfile($file);
    exit;
}

######################################################################################################

function is_email($email) {
    /* Check email format */
    if (preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#', $email))
        return true;
    else
        return false;
}

######################################################################################################
// simple class that encapsulates mail() with addition of mime file attachment.

class CMailFile {

    var $subject;
    var $addr_to;
    var $text_body;
    var $text_encoded;
    var $mime_headers;
    var $mime_boundary = "--==================_846811060==_";
    var $smtp_headers;

    function CMailFile($subject, $to, $from, $msg, $filename, $mimetype = "application/octet-stream", $mime_filename = false, $extra_headers = null) {
        $this->subject = $subject;
        $this->addr_to = $to;
        $this->smtp_headers = $this->write_smtpheaders($from, $extra_headers);
        $this->text_body = $this->write_body($msg);
        $this->text_encoded = $this->attach_file($filename, $mimetype, $mime_filename);
        $this->mime_headers = $this->write_mimeheaders($filename, $mime_filename);
    }

    function attach_file($filename, $mimetype, $mime_filename) {
        $encoded = $this->encode_file($filename);
        if ($mime_filename)
            $filename = $mime_filename;
        $out = "--" . $this->mime_boundary . "\n";
        $out = $out . "Content-type: " . $mimetype . "; name=\"$filename\";\n";
        $out = $out . "Content-Transfer-Encoding: base64\n";
        $out = $out . "Content-disposition: attachment; filename=\"$filename\"\n\n";
        $out = $out . $encoded . "\n";
        $out = $out . "--" . $this->mime_boundary . "--" . "\n";
        return $out;
        // added -- to notify email client attachment is done
    }

    function encode_file($sourcefile) {
        if (is_readable($sourcefile)) {
            $fd = fopen($sourcefile, "r");
            $contents = fread($fd, filesize($sourcefile));
            $encoded = my_chunk_split(base64_encode($contents));
            fclose($fd);
        }
        return $encoded;
    }

    function sendfile() {
        $headers = $this->smtp_headers . $this->mime_headers;
        $message = $this->text_body . $this->text_encoded;
        mail($this->addr_to, $this->subject, $message, $headers);
    }

    function write_body($msgtext) {
        $out = "--" . $this->mime_boundary . "\n";
        $out = $out . "Content-Type: text/plain; charset=\"UTF-8\"\n\n";
        $out = $out . $msgtext . "\n";
        return $out;
    }

    function write_mimeheaders($filename, $mime_filename) {
        if ($mime_filename)
            $filename = $mime_filename;
        $out = "MIME-version: 1.0\n";
        $out = $out . "Content-type: multipart/mixed; charset=\"UTF-8\"; ";
        $out = $out . "boundary=\"$this->mime_boundary\"\n";
        $out = $out . "Content-transfer-encoding: 7BIT\n";
        $out = $out . "X-attachments: $filename;\n\n";
        return $out;
    }

    function write_smtpheaders($addr_from, $extra_headers) {
        $out = "From: $addr_from\n";
        $out = $out . "Reply-To: $addr_from\n";
        $out = $out . "X-Mailer: PHP3\n";
        $out = $out . "X-Sender: $addr_from\n";
        if (is_array($extra_headers)) {
            foreach ($extra_headers as $header) {
                if (trim($headers))
                    $out = $out . $headers . "\n";
            }
        }
        elseif (trim($extra_headers)) {
            $out = $out . $extra_headers . "\n";
        }
        return $out;
    }

}

// usage - mimetype example "image/gif"
// $mailfile = new CMailFile($subject,$sendto,$replyto,$message,$filename,$mimetype);
// $mailfile->sendfile();
// Splits a string by RFC2045 semantics (76 chars per line, end with \r\n).
// This is not in all PHP versions so I define one here manuall.
function my_chunk_split($str) {

    $len = strlen($str);
    $out = "";
    $start = 1;
    for ($i = 0; $i < $len; $i+= 76) {
        $out .= substr($str, $i, 76) . "\r\n";
    }

    return $out;
}

function amount_format($amount) {
    $amount = round((float) $amount, 2);
    $amount = (string) $amount;
    if (ereg("^-?[0-9]+$", $amount))
        $amount .= ".00";
    elseif (ereg("^-?[0-9]+\.[0-9]{1}$", $amount))
        $amount .= "0";

    return $amount;
}

// end script

function days_count($date1, $date2) {// Retourne le nombre de jours entre 2 dates au format YYYY-MM-DD
    $nb_jours1 = $nb_jours2 = 0;

    if ($date1 < $date2) {
        $premier_jour = $date1;
        $dernier_jour = $date2;
    } else {
        $premier_jour = $date2;
        $dernier_jour = $date1;
    }

    list($premier_an, $premier_mois, $premier_jour) = explode("-", $premier_jour);
    list($dernier_an, $dernier_mois, $dernier_jour) = explode("-", $dernier_jour);

    // Calcul du nombre de jours depuis le debut d'annee de la premiere date
    for ($i = 1; $i < $premier_mois; $i++)
        $nb_jours1 += jours_dans_le_mois($i, $premier_an);
    $nb_jours1 += $premier_jour;

    // Calcul du nombre de jours depuis le debut de la deuxieme date
    for ($i = $premier_an; $i < $dernier_an; $i++)
        $nb_jours2 += nb_jours_annee($i);
    for ($i = 1; $i < $dernier_mois; $i++)
        $nb_jours2 += jours_dans_le_mois($i, $dernier_an);
    $nb_jours2 += $dernier_jour;

    return ($nb_jours2 - $nb_jours1 + 1);
}

function jours_dans_le_mois($mois, $annee) {
    $nb_jours = array('31', '28', '31', '30', '31', '30', '31', '31', '30', '31', '30', '31');
    if ($annee % 4 == 0)
        $nb_jours[1] = '29';

    return $nb_jours[$mois - 1];
}

#####################################################################################################

function nb_jours_annee($annee) {
    $nb = 0;
    for ($i = 1; $i <= 12; $i++) {
        $nb += jours_dans_le_mois($i, $annee);
    }
    return $nb;
}

function mount_user_folder($type, $ajax_instance = NULL) {
    // $type = main | popup | ajax
    global $application;

    /* $pwd = decrypt_string($application->userprefs['ssh_password']);
      if(!is_dir("/var/home/" . $application->userprefs['login'])) mkdir("/var/home/" . $application->userprefs['login']);
      exec("smbmount //`hostname`.local/" . $application->userprefs['login']  . " /var/home/" . $application->userprefs['login']  . "/ -o username=\"" . str_replace('"', '\\"', $application->userprefs['login'])  . "\",password=\"" . str_replace('"', '\\"', $pwd) . "\"", $string, $error_code);
      if($error_code != 0) {
      if($type == "main") $application->message_die(implode("<br />", $string));
      elseif(($type == "popup")) $application->message_die(implode("<br />", $string), true);
      else die("Unable to mount the folder");
      }
      return "/var/home/" . $application->userprefs['login']; */
}

function umount_user_folder($user) {
    /* $cpt = 0;
      do {
      $string = NULL;
      $error_code = NULL;
      @exec('mount | grep "/var/home/' . str_replace('"', '\\"', $user) . ' type"', $string, $error_code);
      @exec("smbumount /var/home/" . $user  . "/");
      $cpt++;
      } while(trim($string[0]) && $cpt < 10); */
}

function create_smb_url($folder = NULL) {
    // Renvoie l'url du dossier demandé au format smb://user:password@nom_du_dossier
    global $application;
    if ($folder === NULL)
        $folder = $application->userprefs['login'];
    $pwd = decrypt_string($application->userprefs['ssh_password']);

    return "smb://" . $application->userprefs['login'] . ":" . $pwd . "@" . SAMBA_SERVER_NAME . "/" . $folder;
}

function numeric_format($string) {
    $string = str_replace(",", ".", $string);
    $string = trim($string);
    if (strlen($string) > 0 && $string[0] == "-") {
        $negative = true;
    } else {
        $negative = false;
    }

    $string = preg_replace("/[^0-9\.]/i", "", $string);
    if ($negative)
        $string = "-" . $string;
    return $string;
}

function send_html_mail_file($from, $to, $title, $html_code, $occurrences_to_replace = NULL) {
    if (is_array($occurrences_to_replace)) {
        @reset($occurrences_to_replace);
        while (list($key, $value) = each($occurrences_to_replace)) {
            $html_code = str_replace("{" . $key . "}", $value, $html_code);
        }
    }

    mail($to, $title, $html_code, "From: $from\r\n" .
            "Reply-To: $from\r\n" .
            "Content-Type: text/html; charset=\"utf-8\"\r\n");
}

function igestis_logger($log) {
    global $application;
    $login = "Unknown";
    if ($application instanceof application) {
        if (isset($application->userprefs['login']))
            $login = $application->userprefs['login'];
        else
            $login = "Anonymous";
    }
    if (ConfigIgestisGlobalVars::LOG_FILE) {
        @file_put_contents(LOG_FILE, date("Y-m-d H:i:s") . " - " . $login . " - " . $log . "\n", FILE_APPEND);
    }
}

function igestis_error_get_last() {
    if (isset($_SESSION['IGESTIS_ERRORS']) && is_array($_SESSION['IGESTIS_ERRORS'])) {
        return $_SESSION['IGESTIS_ERRORS'][count($_SESSION['IGESTIS_ERRORS']) - 1];
    }
}

function logged_exec(&$command, &$output, &$return_var) {
    // This function allow to log the exec command error.

    exec($command, $output, $return_var);
    if ($return_var != 0) {
        new wizz($output);
        igestis_logger("Exec command return -> " . $output);
    }
}

function count_days($date1, $date2) {// Retourne le nombre de jours entre 2 dates au format YYYY-MM-DD
    if (!preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $date1))
        return 0;
    if (!preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $date2))
        return 0;

    if ($date1 < $date2) {
        $premier_jour = $date1;
        $dernier_jour = $date2;
    } else {
        $premier_jour = $date2;
        $dernier_jour = $date1;
    }


    list($premier_an, $premier_mois, $premier_jour) = explode("-", $premier_jour);
    list($dernier_an, $dernier_mois, $dernier_jour) = explode("-", $dernier_jour);

    // Calcul du nombre de jours depuis le debut d'annee de la premiere date
    for ($i = 1; $i < $premier_mois; $i++)
        $nb_jours1 += days_on_month($i, $premier_an);
    $nb_jours1 += $premier_jour;

    // Calcul du nombre de jours depuis le debut de la deuxieme date
    for ($i = $premier_an; $i < $dernier_an; $i++)
        $nb_jours2 += days_on_year($i);
    for ($i = 1; $i < $dernier_mois; $i++)
        $nb_jours2 += days_on_month($i, $dernier_an);
    $nb_jours2 += $dernier_jour;

    return ($nb_jours2 - $nb_jours1);
}

function days_on_month($month, $year) {
    return date("t", mktime(0, 0, 0, (int) $month, 0, (int) $year));
}

function days_on_year($year) {
    if ($year % 4)
        return 365;
    else
        return 366;
}