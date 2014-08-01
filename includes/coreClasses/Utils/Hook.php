<?php

/**
 * This class allow modules to create some hooks
 *
 * @author Gilles Hemmerlé (iabsis) <giloux@gmail.com>
 * @package igestis
 */

namespace Igestis\Utils;


class Hook {
    /**
     *
     * @var Hook Singleton
     */
    private static $_instance;
    
    /**
     * Constructor
     */
    private function __construct() {
        ;
    }
    
    /**
     * Allow to instanciate class, and create an unique instance
     * @return Hook Get the unique instance of the class
     */
    public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new self;
        }
        
        return self::$_instance;
    }
    
    /**
     * Call all hooks listeners
     * @param string $hookName Name of the called hook
     * @param \Igestis\Types\HookParameters $params Hook parameters
     */
    public static function callHook($hookName, \Igestis\Types\HookParameters $params = null) {
        $oModulesList = \IgestisModulesList::getInstance();
        if($params == null) $params = new \Igestis\Types\HookParameters;
        foreach ($oModulesList->get() as $moduleName => $moduleDatas) {
            $hookListener = "\\Igestis\\Modules\\" . $moduleName . "\\ConfigHookListener";
            if(class_exists($hookListener)) {
                try {
                    $return = $hookListener::listen($hookName, $params);
                }
                catch(\Exception $e) {
                }
                
                if($return == true) {
                    Debug::addLog("$hookListener::listen($hookName) has been parsed with success");
                }

            }
        }
    }
    
}