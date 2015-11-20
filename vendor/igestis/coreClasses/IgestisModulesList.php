<?php

/**
 * Create an array listing all module and the igestion version compatibility
 *
 * @author Gilles Hemmerlé
 */
class IgestisModulesList {
    
    /**
     *
     * @var Array List of modules
     */
    private $modulesList;

    /**
     * @var IgestisModulesList the singleton instance
     */
    private static $_instance;

    /**
     * 
     * @return self
     */
    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new self;
        }
        
        return self::$_instance;
    }
    
    private function __construct() {
        $this->modulesList = array();
    }

    /**
     * Return the list of modules with the igestis version
     * @return Array List of modules
     */
    public function get() {
        $this->createList();
        return $this->modulesList;
    }
    
    /**
     * Check if a module exists nor not
     * @param string $moduleName
     * @return boolean
     */
    public function moduleExists($moduleName) {
        if(isset($this->modulesList[$moduleName])) {
            return true;
        }
        return false;
    }
    
    /**
     * Get the module true folder
     * @param string $moduleName Module name
     * @return string Module folder or null if module does not exist
     */
    public function getFolder($moduleName) {
        if($moduleName == "core") {
            return (__DIR__ . "/../../");
        }
        
        if(!$this->moduleExists($moduleName)) {
            return null;
        }
        
        return $this->modulesList[$moduleName]['folder'];
    }
    
    /**
     * 
     * @return Array List of modules
     */
    private function createList() {
        if(count($this->modulesList)) {
            return $this->modulesList;  
        }
        
        $directory_to_search = \ConfigIgestisGlobalVars::appliFolder() . "/modules/";
        $dir = opendir($directory_to_search);
        while ($module_name = readdir($dir)) {
            if ($module_name == "." || $module_name == ".."){
                continue;
            }
            
            if (!is_file($directory_to_search . $module_name . "/config/ConfigModuleVars.php")) {
                continue;
            }
            $this->modulesList[$module_name] = array(
                "name" => $module_name,
                "folder" => $directory_to_search . $module_name,
                "igestisVersion" => 2
            );            
        }
        
        if($dir) {
            closedir($dir);
        }
        
        return $this->modulesList;
    }
}