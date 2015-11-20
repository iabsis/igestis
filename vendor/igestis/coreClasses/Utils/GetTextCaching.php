<?php

namespace Igestis\Utils;

/**
 * This class allow to caching the MO file in the igestis cache folder to avoid the necessity to reload apache after a mo file update
 *
 * @author Gilles Hemmerlé
 */
class GetTextCaching {
    //put your code here   

    /**
     * Caching all mo language file of the module passed on argument.
     * @param Mixed This is a row of the array returned by the IgestisModuleList->get() method.
     *               Should be a string with "CORE" value in order to caching the core languages files
     */
    public function setCachingFor($module) {
        
        if($module ==  "CORE") {
            $moduleDatas = array(
                "name" => "igestis",
                "folder" => \ConfigIgestisGlobalVars::rootFolder()
            );
            $configClass = "\ConfigIgestisGlobalVars";
        }
        else {
            $moduleDatas = $module;
            $configClass = "\\Igestis\\Modules\\" . $moduleDatas['name'] . "\\ConfigModuleVars";
        }
        
        $cacheLangDir = \ConfigIgestisGlobalVars::cacheFolder() . "/lang";
        
        if(!is_dir(\ConfigIgestisGlobalVars::cacheFolder() . "/lang")) {
            try {
                mkdir(\ConfigIgestisGlobalVars::cacheFolder() . "/lang");
                mkdir(\ConfigIgestisGlobalVars::cacheFolder() . "/lang/locale");
            }
            catch (Exception $e) {
                die($e);
            }
        }
            
        // Start to browse languages folder
        $moduleLangFolder = $moduleDatas['folder'] . "/lang/locale";
        if(!is_dir($moduleLangFolder)) {
            return;
        }
        
        $dir = opendir($moduleLangFolder);
        while ($dirName = readdir($dir)) {
            if ($dirName == "." || $dirName == ".."){
                continue;
            }            
            
            $currentFolder = $moduleLangFolder . "/" . $dirName;
            $currentFile = $currentFolder . "/LC_MESSAGES/" . $moduleDatas['name'] . ".mo";
            if(!is_file($currentFile)) {
                continue;
            }            
            
            
            if(is_dir($currentFolder)) {
                $cacheCurrentFolder = $cacheLangDir . "/locale/" . $dirName;
                if(!is_dir($cacheCurrentFolder)) {
                    mkdir('/home/vagrant/sites/igestis3/cache/lang/locale/bla/bla', 0777, true);
                    mkdir($cacheCurrentFolder . "/LC_MESSAGES", 0755, true);
                }                

                $cacheCurrentFile = $cacheCurrentFolder . "/LC_MESSAGES/" . (method_exists($configClass, "textDomain") ? $configClass::textDomain() : $configClass::textDomain) . ".mo";
                if(!is_file($cacheCurrentFile)) {
                    
                    if($module == "CORE") {
                        $this->emptyFolder("igestis", $cacheCurrentFolder . "/LC_MESSAGES/");
                    }
                    else {
                        $this->emptyFolder((method_exists($configClass, "moduleName") ? $configClass::moduleName() : $configClass::moduleName), $cacheCurrentFolder . "/LC_MESSAGES/");
                    }
                    
                    copy($currentFile, $cacheCurrentFile);
                }
            }
        }        
        closedir($dir);        
    }
    
    /**
     * Create the cache for every modules of the application
     */
    public function setCachingForAll() {        
        // Manage modules locales
        $oModulesList = \IgestisModulesList::getInstance();
        
        $this->setCachingFor("CORE");
        foreach ($oModulesList->get() as $moduleName => $moduleDatas) {
            if($moduleDatas['igestisVersion'] == 2 && is_dir($moduleDatas['folder'])) {                
                // Caching the mo file
                $this->setCachingFor($moduleDatas);
            }
        }        
    }
    
    /**
     * Delete all files contained on a passed folder (only files, not the folders).
     * @param String Folder target
     */
    private function emptyFolder($module, $folder) {
        $dir = opendir($folder);
        while ($fileName = readdir($dir)) {
            if ($fileName == "." || $fileName == ".."){
                continue;
            }
            
            $currentFile = $folder . "/" . $fileName;
            if(is_file($currentFile) && preg_match("#^" . $module . "#", $fileName)) {
                @unlink($currentFile);
            }
        }
        closedir($dir);
    }
}