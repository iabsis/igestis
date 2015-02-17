<?php

namespace Igestis\Utils;

/**
 * Description of DBUpdater
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */
class DBUpdater
{
    /**
     *
     * @var \Doctrine\ORM\EntityManager $doctrine
     */
    private static $doctrine = null;
    
    /**
     * Set the entitymanager in order to use the updater classes
     * @param \Doctrine\ORM\EntityManager $doctrine
     */
    public static function init(\Doctrine\ORM\EntityManager $doctrine)
    {
        self::$doctrine = $doctrine;
    }
    public static function hasAvailableUpdates()
    {
        $databaseInstalled = self::initialTablesExist();
        
        if (!$databaseInstalled) {
            return true;
        }
        
        if (count(self::getListOfSqlFilesToImport())) {
            return true;
        }
        
        return false;
    }
    
    public static function initialTablesExist()
    {
        $connexion = self::$doctrine->getConnection();
        try {
            $connexion->executeQuery("DESC MYSQL_MIGRATION");
            $databaseInstalled = true;
    
        } catch (\Exception $ex) {
            
            $databaseInstalled = false;
        }
        
        return $databaseInstalled;
    }
    private static function getListOfSqlFilesToImport()
    {
        $initialTablesExist = self::initialTablesExist();
        // Get the core sql folder
        $filesList = array();
        $sqlFolder = __DIR__ . "/../../../install/database/mysql/";
        if (is_dir($sqlFolder)) {
            if ($dh = opendir($sqlFolder)) {
                while (($file = readdir($dh)) !== false) {
                    if (is_file($sqlFolder . $file)) {
                        if ($initialTablesExist) {
                            $fileAlreadyImported = self::$doctrine->getRepository("MysqlMigration")->find(array("module" => "CORE", "file" => $file));
                            if ($fileAlreadyImported) {
                                continue;
                            }
                        }
                        $filesList[$sqlFolder . $file] = array(
                            "fileTarget" =>$sqlFolder . $file,
                            "filename" => $file,
                            "module" => "CORE"
                        );
                    }
                }
                closedir($dh);
            }
        }
        asort($filesList);

        $modulesList = \IgestisModulesList::getInstance();
        $aModulesList = $modulesList->get();
        foreach ($aModulesList as $module_name => $module_datas) {
            $sqlFolder = __DIR__ . "/../../../install/database/$module_name/mysql/";
            $moduleSqlFileList = array();
            if (is_dir($sqlFolder)) {
                if ($dh = opendir($sqlFolder)) {
                    while (($file = readdir($dh)) !== false) {
                        if (is_file($sqlFolder . $file)) {
                            if ($initialTablesExist) {
                                $fileAlreadyImported = self::$doctrine->getRepository("MysqlMigration")->find(array("module" => $module_name, "file" => $file));
                                if ($fileAlreadyImported) {
                                    continue;
                                }
                            }
                            $moduleSqlFileList[$sqlFolder . $file] = array(
                                "fileTarget" =>$sqlFolder . $file,
                                "filename" => $file,
                                "module" => $module_name
                            );
                        }
                    }
                    closedir($dh);
                }
            }

            asort($moduleSqlFileList);
            $filesList = array_merge($filesList, $moduleSqlFileList);
        }
        
        return $filesList;
    }
    
    public static function startUpdate()
    {
        if (!self::hasAvailableUpdates()) {
            return true;
        }
        
        $filesList = self::getListOfSqlFilesToImport();
        $connexion = self::$doctrine->getConnection();
        $connexion->beginTransaction();
        
        try {

            foreach ($filesList as $currentSqlFile => $fileInfos) {
                
                $migration = new \MysqlMigration($fileInfos['module'], $fileInfos['filename']);
                
                $sql = file_get_contents($currentSqlFile);
                $connexion->executeQuery($sql);
                
                self::$doctrine->persist($migration);
                self::$doctrine->flush();
            }
            
            $connexion->commit();
            
        } catch (\Exception $exc) {
            $connexion->rollback();
            return false;
        }
        
        return true;
            
        
        
    }
}
