<?php

/*
 * Autoload the Igestis classes.
 */
/**
 * Autoloads Igestis classes.
 *
 * @package igestis
 * @author  Gilles Hemmerlé <giloux@gmail.com> (Iabsis)
 */
class IgestisAutoloader
{
    /**
     * Registers Igestis_Autoloader as an SPL autoloader.
     */
    static public function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param  string  $class  A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    static public function autoload($class)
    {
        $result = null;
        // Normal class nomation
        if(preg_match("#Igestis\\\Modules\\\([A-Za-z]{1}[A-Za-z0-9\-\_]+)\\\.*#", $class, $result)) {
            $folder = preg_replace("#Igestis\\\Modules\\\([A-Za-z]{1}[A-Za-z0-9\-\_]+)\\\#", "", $class);
            $folder = str_replace("\\", "/", $folder);
            $file = dirname(__FILE__) . "/../../modules/" . $result[1] . "/classes/" . $folder . '.php';
            if(is_file($file)) {
               require $file;
               return;
           }
        }
        elseif(preg_match("#^Igestis\\\#", $class)) {
            
            $folder = preg_replace("#^Igestis\\\#", "", $class);
            $folder = str_replace("\\", "/", $folder);
            $file = dirname(__FILE__) . "/coreClasses/" . $folder . ".php";
            if(is_file($file)) {
                require $file;
                return;
            }
            $file = dirname(__FILE__) . "/" . $folder . ".php";
            if(is_file($file)) {
                require $file;
                return;
            }
        }
        
        
        // The modules config files (must start with Config)
        if(preg_match("#Igestis\\\Modules\\\([A-Za-z]{1}[A-Za-z0-9\-\_]+)\\\(Config[A-Za-z0-9\-\_]+)#", $class, $result)) {
            $file = dirname(__FILE__) . "/../../modules/" . $result[1] . "/config/" . $result[2] . '.php';
            if (is_file($file)) {
                require $file;
                return;
            }
        }
        
        
        // The modules controllers files (must finish with Controller
        if(preg_match("#Igestis\\\Modules\\\([A-Za-z]{1}[A-Za-z0-9\-\_]+)\\\([A-Za-z0-9\-\_]+Controller)#", $class, $result)) {
            $file = dirname(__FILE__) . "/../../modules/" . $result[1] . "/controllers/" . $result[2] . '.php';
            if (is_file($file)) {
                require $file;
                return;
            }
        }

        /** Gestion des controleurs **/
        if (is_file($file = dirname(__FILE__) . "/../../controllers/" . $class . '.php')) {
            require $file;
            return;
        }


        if (is_file($file = dirname(__FILE__) . "/coreClasses/" . $class . '.php')) {
            require $file;
            return;
        }
        
        if (is_file($file = dirname(__FILE__) . "/../../config/igestis/" . $class . '.php')) {
            require $file;
            return;
        }
        
        // Entities autoload
        if (is_file($file = dirname(__FILE__) . "/../../entities/" . $class . '.php')) {
            require $file;
            return;
        }
        
        // Module entities autoload
        $modulesList = IgestisModulesList::getInstance();
        $aModulesList = $modulesList->get();
        foreach ($aModulesList as $module) {
            $file = $module['folder'] . "/entities/" . $class . '.php';
            if (is_file($file)) {
                require $file;
                return;
            }
        }
        
        
        // Oldaphp autoload - The LDAP object
        if (is_file($file = dirname(__FILE__) . "/Oldaphp/" . $class . '.php')) {
            require $file;
            return;
        }     
    }   
}
