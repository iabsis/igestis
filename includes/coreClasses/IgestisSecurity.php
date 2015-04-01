<?php


/**
 * Security management of the igestis application (can authenticate users and check rights for each application part
 *
 * @author Gilles Hemmerlé
 */
class IgestisSecurity {
    /**
     *
     * @var Boolean Is user authenticated
     */
    protected $is_loged;

    /**
     *
     * @var CoreUsers User authenticated
     */
    public $user;

    /**
     *
     * @var CoreContacts Entities reprensenting the authenticated user
     */
    public $contact;

    /**
     *
     * @var Application Context of the application
     */
    protected $context;

    /**
     *
     * @var Array Liste des droits
     */
    protected $user_rights_list;
    
    /**
     * 
     * @var IgestisSecurity Singleton of the security class
     */
    protected static $_instance;
    
    protected static $groupRights = array();

    /**
     * Initialise the security class singleton and return the object
     * @param Application $context
     * @return IgestisSecurity
     */
    public static function init(&$context=null) {
        if(self::$_instance == null) {
            if(!($context instanceof \Application))  {
                throw new Exception(_(sprintf("The first init of the '%s' class must contain a context for the initialisation", __CLASS__)));
            }
            self::$_instance =  new static($context);
        }
        return self::$_instance;
    }
    /**
     *
     * @param Application $context
     */
    protected function __construct(Application $context) {
        
        $this->context = $context;
        $this->is_loged = false;
        
        $this->contact = new CoreContacts();
        $this->user = new CoreUsers();
        
        $hook = Igestis\Utils\Hook::getInstance();
        

        if (isset($_COOKIE['sess_login'])) {
            $_SESSION['sess_login'] = $_COOKIE['sess_login'];
        }
        if (isset($_COOKIE['sess_password'])) {
            $_SESSION['sess_password'] = $_COOKIE['sess_password'];
        }

        // If the user clicks on Sign in, but without a login or a password
        if (isset($_POST['sess_login'])) {
            if ($_POST['sess_login'] == "" || $_POST['sess_password'] == "") {
                new wizz(_("Please specify a login and a password"));
            }
        }

        if (!isset($_SESSION['sess_login'])) {
            $_SESSION['sess_login'] = null;
            $_SESSION['sess_password'] = null;
        }

        if (!$this->authenticate($_SESSION['sess_login'], $_SESSION['sess_password'])) {
            // Connexion ...
            if (isset($_POST['sess_login']) && isset($_POST['sess_password']) && $_POST['sess_login'] && $_POST['sess_password']) {
                
                $hookParameters = new \Igestis\Types\HookParameters();
                $hookParameters->set("postLogin", $_POST['sess_login']);
                $hookParameters->set("postPassword", $_POST['sess_password']);                
                $hook->callHook("startAuthentication", $hookParameters);
                if ($this->authenticate($_POST['sess_login'], $_POST['sess_password'])) {  
                    $_SESSION['sess_login'] = $_POST['sess_login'];
                    $_SESSION['sess_password'] = md5($_POST['sess_password']);
                    // Sauvegarde du mot de passe crypté dans mysql
                    $td = MCRYPT_RIJNDAEL_128; // Encryption cipher (http://www.ciphersbyritter.com/glossary.htm#Cipher)
                    $iv_size = mcrypt_get_iv_size($td, MCRYPT_MODE_ECB); // Dependant on cipher/mode combination (http://www.php.net/manual/en/function.mcrypt-get-iv-size.php)
                    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); // Creates an IV (http://www.ciphersbyritter.com/glossary.htm#IV)

                    $encrypted_password = mcrypt_encrypt($td, SSH_ENCRYPT_KEY, $_POST['sess_password'], MCRYPT_MODE_ECB, $iv);

                    /*$sql = "UPDATE CORE_CONTACTS SET ssh_password='" . mysql_real_escape_string($encrypted_password) . "' WHERE login='" . mysql_real_escape_string($_POST['login']) . "'";
                    $req = mysql_query($sql) or die(mysql_error() . $sql);*/
                    $this->contact->setSshPassword($encrypted_password);
                    $this->context->entityManager->persist($this->contact);
                    $this->context->entityManager->flush();
                    

                    if ($_POST['use_cookie']) {// If we are requesting a persistant connexion :
                        setcookie("sess_login", $_SESSION['sess_login'], time() + 5000000);
                        setcookie("sess_password", $_SESSION['sess_password'], time() + 5000000);
                    } else {
                        self::unset_cookie();
                    }
                    $hookParameters = new \Igestis\Types\HookParameters();
                    $hookParameters->set("postLogin", $_POST['sess_login']);
                    $hookParameters->set("postPassword", $_POST['sess_password']);    
                    $hookParameters->set("logedContact", $this->contact);
                    $hookParameters->set("securityObject", $this);
                    $hook->callHook("loginSuccess", $hookParameters);
                } else {
                    self::unset_cookie();
                    $_SESSION['sess_login'] = $_SESSION['sess_password'] = "";

                    new wizz(_("Invalid username or password"));
                    $hookParameters = new \Igestis\Types\HookParameters();
                    $hookParameters->set("postLogin", $_POST['sess_login']);
                    $hookParameters->set("postPassword", $_POST['sess_password']);                
                    $hook->callHook("loginFailed", $hookParameters);
                }

                if ($_SESSION['sess_page_redirect']) {
                    header("location:" . $_SESSION['sess_page_redirect']);
                    exit;
                } else {
                    header("location:" . $_SERVER['SCRIPT_NAME']);
                    exit;
                }

            }
            else {
                self::unset_cookie();
                $_SESSION['sess_login'] = $_SESSION['sess_password'] = "";
            }
        }
    }

    /**
     * Delete the cookie that allows to persist the authentication
     */
    public static function unset_cookie() {
        if(!empty($_COOKIE['sess_login'])) {
            setcookie("sess_login", $_SESSION['sess_login'], time() - 1000);
            unset($_COOKIE['sess_login']);
        }
        if(!empty($_SESSION['sess_password'])) {
            setcookie("sess_password", $_SESSION['sess_password'], time() - 1000);
            unset($_COOKIE['sess_password']);
        }
        
        

    }


    public function authenticate($login, $password) {
        if(!preg_match("/[a-z0-9]{32}/", $password)) {
            $password = md5($password);
        }
        
        
        $user = $this->context->entityManager->getRepository("CoreContacts")->getFromLoginAndPassword($login, $password);
        if($user) {
           
            $this->contact = $user;
            $this->is_loged = true;
            $this->user = $this->contact->getUser();
            return true;
        }
        return false;
    }

    /**
     *
     * @return boolean Is user authenticated or not ?
     * @@deprecated Use isLoged()
     *
     */
    public function is_loged() {
        return $this->is_loged;
    }

    /**
     * @return boolean Is user authenticated or not ? 
     */
    public function isLoged() {
        return $this->is_loged;
    }

    /**
     *
     * @param string $module_name
     * @param Integer $for_user_id
     * @return mixed String Right code for the passed module_name
     */
    public function module_access($module_name, $for_user_id=NULL) {// Return the module access for the user in param2 (for the current user if $id_user is null)

        if ($for_user_id === NULL)  {
            $user_id = $this->user->getId ();
        }
        else {
            $user_id = $for_user_id;
        }
        $rights_list = $this->get_rights_list($user_id, ($for_user_id != NULL)); //, $this->context);

        return empty($rights_list[strtoupper($module_name)]) ? "" : strtoupper($rights_list[strtoupper($module_name)]);
    }

    /**
     *
     * @param Integer $user_id
     * @param Application $context
     * @return Array List of the right access for the passed user
     */
    public function get_rights_list($user_id, $forceRefresh=false) {
        if($this->user == null) return array();
        if($forceRefresh) $this->user_rights_list = NULL;
        if($user_id != $this->user->getId() || $this->user_rights_list == NULL) {
            $users_rights = $this->context->entityManager->getRepository("CoreUsersRights")->getUserRights($user_id);

            $list = null;
            if(is_array($users_rights)) {
                foreach ($users_rights as $right) {
                    
                    if($user_id != $this->user->getId()) {
                        $list[strtoupper($right['moduleName'])] = $right['rightCode'];
                    }
                    else {
                        $this->user_rights_list[strtoupper($right['moduleName'])] = $right['rightCode'];
                    }
                }
                
                $company =  $this->user->getCompany();
                if($company)  {
                    $companyRight = $this->getCompanyRightsList($company->getId());  
                    foreach ($companyRight as $appCode => $rightCode) {  
                        if($user_id == $this->user->getId()) {
                            if(empty($this->user_rights_list[$appCode])) $this->user_rights_list[$appCode] = $rightCode; 
                        }
                        else {
                            if(empty($list[$appCode])) $list[$appCode] = $rightCode; 
                        }
                    }
                }                
            }
        }        
        
        if($user_id != $this->user->getId()) {
            return $list;
        }
        else {
            return $this->user_rights_list;
        }                

    }
    
    /**
     * Get the users write without company default rights
     * @param int $userId
     * @return array
     */
    private function getUserRightList($userId) {
        if(!$userId)  return array();
        $user = $this->context->entityManager->find("CoreUsers", $userId);
        $users_rights = $this->context->entityManager->getRepository("CoreUsersRights")->getUserRights($userId);
        if(!$user || !$users_rights) return array();
        
        $list = array();
        
        if(is_array($users_rights)) {
            foreach ($users_rights as $right) {
                $list[strtoupper($right['moduleName'])] = $right['rightCode'];
            }
        }
        
        
        return $list;
        
    }
    
    /**
     * Get default company rights list
     * @param int $companyId
     * @return array
     */
    private function getCompanyRightsList($companyId) {
        $company = $this->context->entityManager->find("CoreCompanies", $companyId);
        $rightsArray = array();
        
        foreach ($company->getDefaultRightsList() as $companuRight) {
            if ($this->user->getUserType() != \CoreUsers::USER_TYPE_EMPLOYEE) {
                $rightsArray[strtoupper($companuRight->getModuleName())] = null;
            } else {
                $rightsArray[strtoupper($companuRight->getModuleName())] = $companuRight->getRightCode();
            }
            
        }

        return $rightsArray;
        
    }
    
    /**
     * Return the rights of the group
     * @param int $groupId Id of the group
     * @return array Rights of the group
     */
    public function getGroupRightList($groupId) {
        if($groupId == NULL) return array();
        if($this->user == null) return array();    
        
        if(!isset(self::$groupRights[$groupId])) {
            $groupRights = $this->context->entityManager->getRepository("CoreDepartmentsRights")->getDepartmentRights($groupId);

            $list = null;

            if(is_array($groupRights)) {
                foreach ($groupRights as $right) {
                    if($groupId != $this->user->getId()) {
                        $list[strtoupper($right['moduleName'])] = $right['rightCode'];
                    }
                    else {
                        $this->user_rights_list[strtoupper($right['moduleName'])] = $right['rightCode'];
                    }
                }
            }
            self::$groupRights[$groupId] = $list;
        }
        
        return self::$groupRights[$groupId];
    }

    /**
     * Return all the rights of the applications with the selcted info for the requested person
     * @param int $userId Id of the person
     * @return array List of the rights with all details
     */
    public function getAllModulesRights($userId=NULL) {        
        if($userId != NULL) {
            $rights_list = $this->getUserRightList($userId);
        }
        return $this->completRightsList($rights_list);   
        
    }
    
    /**
     * 
     * @param type $companyId
     * @return type
     */
    public function getDefaultCompanyRights($companyId) {
        $rightsList = null;
        if($companyId != NULL) {
            $rightsList = $this->getCompanyRightsList($companyId);
        }
        return $this->completRightsList($rightsList);  
    }
    
    /**
     * Return all the rights of the applications with the selcted info for the requested group
     * @param int $groupId Id of the group
     * @return array List of the rights with all details
     */
    public function getAllModulesGroupRights($groupId=NULL) {
        // Listing all the module folder to search all modules        

        if($groupId != NULL) {
            $rights_list = $this->getGroupRightList($groupId);
        }
        return $this->completRightsList($rights_list);        
    }
    
    /**
     * Return the formatte array with all rights and details
     * @param array $rights_list Rights to set as selected
     * @return array
     */
    private function completRightsList($rights_list) {
        $modulesList = IgestisModulesList::getInstance();
        $buffer = array();
        
        foreach ($modulesList->get()  as $module_name => $module_datas) {
            if (is_dir($module_datas['folder'])) {
                if($module_datas['igestisVersion'] == 2) {
                    // Igestis v2.0+ module managament
                    $moduleMenuConfig = "\\Igestis\\Modules\\" . $module_name . "\\ConfigInitModule";
                    if(method_exists($moduleMenuConfig, "getRightsList")) {
                        $buffer[] = $this->manageRightsListFromModuleConfig($moduleMenuConfig::getRightsList(), $rights_list);
                    }
                }
                else {
                    // Manage an old module (keep compatibility with old igestis modules
                    // Including the config file of the menu
                    
                    // If there is not a config.php, we restart the loop
                    if (!is_file($module_datas['folder'] . "/config.php"))
                        continue;
                    
                    include $module_datas['folder'] . "/config.php";
                    $appli = ${$module_name};

                    // If there are no data in the config.php, we restart the loop
                    if (!$appli) continue;

                    @reset($appli);
                    $first = true;
                    $this->context->set_language("EN");
                    $module_full_name = $this->context->translate_content($appli['module_name']);
                    if(is_array($appli)) {
                        foreach($appli['rights_list'] as $right) {
                            if($first) {
                                $buffer[] = array(
                                    "MODULE_NAME" => $module_name,
                                    "FIELD_NAME" => "right_$module_name",
                                    "MODULE_FULL_NAME" => $module_full_name,
                                    "RIGHTS_LIST" => NULL
                                );
                            }

                            $buffer[count($buffer)-1]["RIGHTS_LIST"][] = array(
                                "CODE" => $this->context->translate_content($right['RIGHT_CODE']),
                                "NAME" => $this->context->translate_content($right['RIGHT_NAME']),
                                "DESCRIPTION" => $this->context->translate_content($right['RIGHT_HELP']),
                                "SELECTED" => ($right['RIGHT_CODE'] == $rights_list[strtoupper($module_name)])
                            );

                            $first = false;
                        }
                    }
                }                
            }
        }

        // Gestion des droits du core
        if(!in_array("CORE", $buffer)) {
            $buffer[] = array(
                "MODULE_NAME" => "core",
                "FIELD_NAME" => "right_core",
                "MODULE_FULL_NAME" => "iGestis Core application",
                "RIGHTS_LIST" => array(
                    array("CODE" =>"NONE", "NAME" => _("None"), "DESCRIPTION" => _("No privileges"), "SELECTED" => ("NONE" == $rights_list["CORE"])),
                    array("CODE" =>"ADMIN", "NAME" =>_("Administrator"), "DESCRIPTION" => _("Can do everything"), "SELECTED" => ("ADMIN" == $rights_list["CORE"])),
                    array("CODE" =>"TECH", "NAME" => _("Employee"), "DESCRIPTION" => _("Can edit customers and show employees"), "SELECTED" => ("TECH" == $rights_list["CORE"]))
                )
            );
        }
        
        return $buffer;
    }

    /**
     * Allow to know if the current user can access the page he requested
     * @param $array The active route
     * @return bool Yes if access granted, No else
     */
    public function hasAccess($routeOrArray, $routeMode = true) {
        if(!$routeMode) return $this->getAccess($routeOrArray);
        if(!isset($routeOrArray) || !isset($routeOrArray['Access']) || !is_array($routeOrArray['Access'])) return false;

        reset($routeOrArray['Access']);
        return $this->getAccess($routeOrArray['Access']);
        
    }
    
    public function getAccess($rightArray) {
        reset($rightArray);
        foreach( $rightArray as $actualRoute) {
            switch($actualRoute) {
                case "ANONYMOUS" :
                    if(!$this->isLoged()) return true;
                    break;
                case "AUTHENTICATED" :
                    if($this->isLoged()) return true;
                    break;
                case "EVERYONE" :
                    return true;
                    break;
                default : // format module:right_code (IE : CORE:ADMIN)
                    if(!preg_match("/^[A-Za-z0-9_]+\:[A-Za-z0-9_]+$/", $actualRoute)) continue;
                    if($this->user == null) continue;
                    list($module, $right) = explode(":", $actualRoute);
                    if($this->module_access($module) == strtoupper($right)) return true;
                    break;
            }
        }
        return false;
    }


    public function logout() {
        $hook = Igestis\Utils\Hook::getInstance();
        $hook->callHook("beforeLogout");
        
        session_destroy();
        setcookie("sess_login", "", time() - 3600);
        setcookie("sess_password", "", time() - 3600);
        $this->is_loged = false;
        
        $hook->callHook("afterLogout");
    }

    /**
     * Checksum of the password (with salt)
     * @static
     * @param String $plainPassword Mot de passe en clair
     * @return String MD5 salted password
     * @todo Use the SALT to add more Security
     */
    public static function generatePassword($plainPassword) {
        return md5($plainPassword);
    }
    
    /**
     * Set the extra datas of the right list (not set from the module config)
     * @param Array Rights config array with all possible rules
     * @param Array List of the rights for the concerned user (to be able to set the good right as 'selected')
     * @return Array Return array with completed datas
     * @throws Exception If the passed argument is not an array
     */
    private function manageRightsListFromModuleConfig($aRightsList, $rights_list) {
        if(!is_array($aRightsList)) {
            throw new Exception("The rights configuration of the module must be an array");
        }        
        reset($aRightsList);
        
        $aRightsList['FIELD_NAME'] = "right_" . $aRightsList['MODULE_NAME'];
        
        foreach ($aRightsList['RIGHTS_LIST'] as $key => $rightValues) {
            $rightCode =$rightValues['CODE'];
            $aRightsList['RIGHTS_LIST'][$key]['SELECTED'] =  ($rightCode == $rights_list[strtoupper($aRightsList['MODULE_NAME'])]);
        }
        
        return $aRightsList;
    }
}