<?php

class Application {    
    /**
     *
     * @var array List of wizz to show 
     */
    private $wizzMessages = array();
    /**
     *
     * @var bool True if the application is under 404 error, false else 
     */
    public $is404error = false;
    /**
     *
     * @var bool True if the application is under 403 error, false else 
     */
    public $is403error = false;

    /**
     *
     * @var Twig_Environment Normal twig environnement
     */
    private $twigEnv = NULL;
    /**
     *
     * @var Twig_Environment Twig environment to render content without file
     */
    private $stringTwigEnv = NULL;
    
    /**
     *
     * @var \IgestisModulesList List of modules
     */
    public $modulesList = NULL;

    /**
     *
     * @var IgestisSecurity Object that defines the access to the application
     */
    public $security = NULL;

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
     * @var \Doctrine\ORM\EntityManager store the entitymanager to access to the doctrine entities
     */
    private static $_entityManager;
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager Helper for the entitymanager to access to the doctrine entities
     */
    public $entityManager;
    
        /**
     * Constructor is private. Use getInstance to create the singleton
     */
    private function __construct() {
        // Initialise application configuration
        
        $installScript = $this->checkInstallScript();
        

        // Initialize modules list
        $oModulesList = IgestisModulesList::getInstance();
        $this->modulesList = $oModulesList->get();

        // Initialize default values
        self::$doctrineLogger = null;
        $this->debugger = Igestis\Utils\Debug::getInstance();
        self::$_entityManager = self::configDoctrine();
        $this->entityManager = self::$_entityManager;


        $templateFoldersList = array(dirname(__FILE__) . "/../../templates/");
        $modulesList = IgestisModulesList::getInstance();
        foreach ($modulesList->get() as $module_name => $module) {
            if ($module['igestisVersion'] == 2) {
                if (is_dir($module['folder'] . "/templates/"))
                    $templateFoldersList[] = $module['folder'] . "/templates/";
            }
        }

        $twigLoader = new Twig_Loader_Filesystem($templateFoldersList);

        //$this->twigEnv = new Twig_Extensions_Extension_I18n();

        $this->twigEnv = new Twig_Environment($twigLoader, array(
                    'cache' => \ConfigIgestisGlobalVars::debugMode() ? false : \ConfigIgestisGlobalVars::cacheFolder() . "/twig",
                    'debug' => \ConfigIgestisGlobalVars::debugMode()
                ));
        $this->twigEnv->addExtension(new Twig_Extensions_Extension_I18nExtended());
        $this->twigEnv->addExtension(new Twig_Extensions_Extension_Url());
        $this->twigEnv->getExtension('core')->setNumberFormat(3, '.', "'");
        $this->twigEnv->addFunction(new Twig_SimpleFunction('pad', 'str_pad'));
        if (\ConfigIgestisGlobalVars::debugMode()) {
            $this->twigEnv->addExtension(new Twig_Extension_Debug());
            $this->twigEnv->clearCacheFiles();
        }
        
        $this->stringTwigEnv = clone $this->twigEnv;
        $this->stringTwigEnv->setLoader(new \Twig_Loader_String());
        $this->stringTwigEnv->getExtension('core')->setNumberFormat(2, '.', "");


        if(!$installScript) {
            if (\ConfigIgestisGlobalVars::useLdap()) {
                $this->security = \IgestisSecurityLdap::init($this);
            } else {
                $this->security = \IgestisSecurity::init($this);
            }

            $this->setLanguage($this->security->contact->getLanguageCode());
        }
        else {
            $this->setLanguage("EN");
        }
        

        self::$_instance = $this;
    }
    
    private function checkInstallScript() {
        // If install folder nor longer exist and we are on the install script, then we redirect to the login page
        $installScript = false;
        if(filter_input(INPUT_GET, "Page") == "install-check") {
            $installScript = true;
        }
        
        if(is_dir(__DIR__ . "/../../install")) {
            $this->installScript();
        }
        else {
            if($installScript) {
                header("location:" . ConfigControllers::createUrl("home_page")); exit;
            }
        }
        
        
        try {
            ConfigIgestisGlobalVars::initFromIniFile();
        } catch (Exception $ex) {
            $this->installScript();
        }

        return $installScript;
        
    }
    private function installScript() {
        if(filter_input(INPUT_GET, "Page") != "install-check") {
            header("location:?Page=install-check"); exit;
        }
    }

    /**
     * Set debug mode Set true to show debug bar and disable caches
     * @param type $debugMode
     */
    public static function setDebugMode($debugMode) {
        ConfigIgestisGlobalVars::set("DEBUG_MODE", (bool)$debugMode);
        if(!defined("DEBUG_MODE")) define("DEBUG_MODE", (bool)$debugMode);
    }
    
    /**
     * Get the single entityManager
     * @return EntityManager
     */
    public static function getEntityMaanger() {
        if (self::$_entityManager === null) {
            self::$_entityManager = self::configDoctrine();
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
        return $this->twigEnv;
    }

    /**
     * Allow to force an authentication by passing the login and password
     * @param string $login
     * @param string $password
     */
    function forceLogin($login, $password) {

        if (\ConfigIgestisGlobalVars::useLdap()) {
            $this->security->authenticate($login, $password);
        } else {
            $this->security->authenticate($login, $password);
        }

        if ($this->security->is_loged()) {
            $this->is_loged = $this->security->is_loged();
            $company = $this->security->user->getCompany();
            $this->userprefs = array(
                "user_label" => $this->security->user->getUserLabel(),
                "user_type" => $this->security->user->getUserType(),
                "company_id" => ($company ? $company->getId() : 0), 
                "id" => $this->security->contact->getId(),
                "contact_id" => $this->security->contact->getId(),
                "user_id" => $this->security->user->getId(),
                "id_user" => $this->security->user->getId(),
                "login" => $this->security->contact->getLogin(),
                "password" => $this->security->contact->getPassword(),
                "ssh_password" => $this->security->contact->getSshPassword(),
                "civility_code" => $this->security->contact->getCivilityCode(), 
                "firstname" => $this->security->contact->getFirstname(),
                "lastname" => $this->security->contact->getLastname(),
                "email" => $this->security->contact->getEmail(),
                "language_code" => $this->security->contact->getLanguageCode()
            );
        }
    }

    /**
     * Create an object of type EntityManager to work with the doctrine entities
     * @return \Doctrine\ORM\EntityManager
     */
    public static function configDoctrine() {
        if (\ConfigIgestisGlobalVars::debugMode() == true) {
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        } else {
            $cache = new \Doctrine\Common\Cache\ApcCache;
        }


        $config = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/../entities"), \ConfigIgestisGlobalVars::debugMode());
        $config->setAutoGenerateProxyClasses(true);
        //$logger = new \Doctrine\DBAL\Logging\EchoSQLLogger;

        if (\ConfigIgestisGlobalVars::debugMode() == true) {
            self::$doctrineLogger = new Doctrine\DBAL\Logging\DebugStack;
            $config->setSQLLogger(self::$doctrineLogger);
        }


        $connectionOptions = array(
            'dbname' => \ConfigIgestisGlobalVars::mysqlDatabase(),
            'user' => \ConfigIgestisGlobalVars::mysqlLogin(),
            'password' => \ConfigIgestisGlobalVars::mysqlPassword(),
            'host' => \ConfigIgestisGlobalVars::mysqlHost(),
            'driver' => 'pdo_mysql',
            'charset' => 'utf8',
            'driverOptions' => array(
                1002 => 'SET NAMES utf8'
            )
        );

        $entityManager = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
                
        $hook = Igestis\Utils\Hook::getInstance();
        $hookParameters = new \Igestis\Types\HookParameters();
        $hookParameters->set('entityManager', $entityManager);
        $hook->callHook("entityManagerInitialized", $hookParameters);

        return $entityManager;
    }



    /**
     * Define the language to enable
     * @param type $lang
     */
    function setLanguage($lang) {

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
        if (\ConfigIgestisGlobalVars::debugMode()) {
            $getTextCaching = new \Igestis\Utils\GetTextCaching();
            $getTextCaching->setCachingFor("CORE");
        }

        // Auto refresh cache everytin when DEBUG_MODE is activated (for production server, a button will be available for admin to reset the cache)
        reset($this->modulesList);
        foreach ($this->modulesList as $moduleName => $moduleDatas) {
            if ($moduleDatas['igestisVersion'] == 2 && is_dir($moduleDatas['folder'])) {
                // Caching the mo file
                if (\ConfigIgestisGlobalVars::debugMode())
                    $getTextCaching->setCachingFor($moduleDatas);
                $configClass = "\\Igestis\\Modules\\" . $moduleDatas['name'] . "\\ConfigModuleVars";
                bindtextdomain($configClass::textDomain, \ConfigIgestisGlobalVars::cacheFolder() . '/lang/locale');
                bind_textdomain_codeset($configClass::textDomain, 'UTF-8');
            }
        }

        // Specify the location of the translation tables
        bindtextdomain(\ConfigIgestisGlobalVars::textDomain(), \ConfigIgestisGlobalVars::cacheFolder() . '/lang/locale');
        bind_textdomain_codeset(\ConfigIgestisGlobalVars::textDomain(), 'UTF-8');
        // Choose domain
        textdomain(\ConfigIgestisGlobalVars::textDomain());

    }

    /**
     * Get template local folder
     * @return type
     */
    function getTemplateFolder() {
        return \ConfigIgestisGlobalVars::serverFolder() . "/" . \ConfigIgestisGlobalVars::appliFolder() . "/theme/" . \ConfigIgestisGlobalVars::theme() . "/";
    }
    
    /**
     * Return puvlic url of the theme
     * @return string
     */
    public static function getTemplateUrl() {
        return "theme/" . \ConfigIgestisGlobalVars::theme() . "/";
        //return \ConfigIgestisGlobalVars::serverAddress() . "/theme/" . \ConfigIgestisGlobalVars::theme() . "/";
    }

    /**
     * Show an error with the incorrect form content
     * @param string $reason
     */
    function invalidForm($reason = "Unknown reason") {
        $this->render("pages/invalidForm.twig", array("reason" => $reason));
    }
   
    /**
     * Return the array for the sidebar
     * @return array
     */
    private function getSidebar() {
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

    /**
     * Return the array of the menu
     * @return array
     */
    private function getMenu() {
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
                $moduleMenuConfig = '\Igestis\Modules\\' . $module_name . '\ConfigInitModule';
                if (class_exists($moduleMenuConfig)) {
                    $moduleMenuConfig::menuSet($this, $menu);
                }
            }
        }
        return $menu->get_array();
    }

    /**
     * Start a file rendering
     * @param string $file File to upload to the user
     * @param bool $forceDownload True to force download, false to try to display file inline in the browser
     * @param string $customName Filename to show to the user
     */
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
     * Method that allow to render a twig file
     *
     * @param String $twigFile Twig file to use
     * @param Array $replacement Array of pear key/value to pass to the twig template
     * @param Boolean $return true to return html content or false (default) to show content to the user
     * @param Boolean $forceDebugToolbar True to force the debug toolbar to show
     * @return String Return the content if $return == true
     */
    function render($twigFile, $replacement, $return = false, $forceDebugToolbar = false) {
        $this->debugger->addLog("Start rendering " . $twigFile);
        $replacement['DEBUG_MODE'] = \ConfigIgestisGlobalVars::debugMode();

        $replacement['_get'] = $_GET;
        $replacement['_post'] = $_POST;

        $replacement['TEMPLATE_URL'] = $this->getTemplateUrl();
        $replacement['SERVER_ADDRESS'] = \ConfigIgestisGlobalVars::serverAddress();
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
        $replacement['CORE_VERSION'] = \ConfigIgestisGlobalVars::version();

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

        $menu = $this->getMenu();

        if ($menu) $replacement['menu_top'] = $menu;
        $sidebar = $this->getSidebar();        
        if ($sidebar) $replacement['MODULE_SIDEBAR'] = $sidebar;

        if ($this->is_loged)
            $replacement['username'] = strtolower($this->userprefs['login']);


        if ($forceDebugToolbar || preg_match("/debugToolbar.twig/", $twigFile) || $return == false) {
            if (\ConfigIgestisGlobalVars::debugMode()) {
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

                $this->twigEnv->render($twigFile, $replacement);
                $endScript = $this->debugger->getScriptTime();
                $this->debugger->addLog("View $twigFile is rendered");
                $replacement['EXECUTION_TIME'] = substr($endScript, 0, 5);
                $replacement['DEBUGGING_VARS'] = $this->debugger->getEvents();
            }
        }
        

        if ($return) {
            return $this->twigEnv->render($twigFile, $replacement);
        } else {
            $hook = Igestis\Utils\Hook::getInstance();
            $hookParameters = new \Igestis\Types\HookParameters();
            $hookParameters->set('replacements', $replacement);
            $hook->callHook("finalRendering", $hookParameters);
            $replacement = $hookParameters->get("replacements");
            
            die($this->twigEnv->render($twigFile, $replacement));
        }
    }
    
    /**
     * Render a twig content from variable
     * @param string $string Twig code
     * @param array $variables Array of pear key/value to pass to the twig template
     * @return string Rendered content
     */
    function renderFromString($string, $variables) {
        
        return $this->stringTwigEnv->render(
          $string,
          $variables
        );
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

    /**
     * Generate a 401 error
     */
    public function throw401error() {
        $this->is401error = true;
        header('HTTP/1.1 401 Unauthorized');
        //$controller = new HomePageController($this);
        //$controller->error404();
        exit;
    }
    
    public function dieMessage($msg) {
        die($msg);
    }

}
