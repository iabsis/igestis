<?php

/**
 * Description of AdminController
 *
 * @author Gilles Hemmerlé
 */
class InstallController extends IgestisController {
    public function checkAction() {
        
        // Disable cache to avoid issue during install (if user has not set the write yet)
        $this->context->getTwigEnvironnement()->setCache(false);
        $requestUri = $_SERVER['REQUEST_URI'];
        if(preg_match("#/public/#", $requestUri)) {
            new wizz(\Igestis\I18n\Translate::_("iGestis has detected the '/public/' prefix in the current url. This is not safe, you should create a virtualhost that point directly into the '/public' folder to disallow the access to the rest of the application from the browser."), wizz::WIZZ_WARNING);
        }

        /* Initialize the DBUpdater class */
        \Igestis\Utils\DBUpdater::init($this->_em);
        
        /* Set default value for database check that will be sent to the twig template */
        $databaseWork = true;
        $dbCredentialsOk = false;
        $dbDatabaseFound = false;
        $dbTablesFound = false;

        /* Set default text error message for the ldap OU test */
        $usersOuError = \Igestis\I18n\Translate::_("Failed");
        $customersOuError = \Igestis\I18n\Translate::_("Failed");
        $suppliersOuError = \Igestis\I18n\Translate::_("Failed");

        try {
            /* Check databas connexion and table existance */
            $em = $this->context->getEntityManager();
            /* Try to connect with the given credentials */
            $connexion = $em->getConnection()->connect();
            $dbCredentialsOk = true;
            if(!$em->getConnection()->getDatabase()) {
                throw new Exception("Invalid database");
            }
            $dbDatabaseFound = true;
            // Check if tables exist
            if(!\Igestis\Utils\DBUpdater::initialTablesExist()) {
                throw new Exception("Database empty");
            }
            $dbTablesFound = true;
            
        } catch (Exception $ex) {
            /* If any exception, the database is marked as "no working" */
            $databaseWork = false;
        }

        // Test Ldap
        $ldapError = false;
        $ldapConnexion = false;

        if(ConfigIgestisGlobalVars::useLdap()) {
            /* If ldap is activated in the config.ini */
            try {
                /* Test the ldap connexion */
                $ldap = Igestis\Utils\IgestisLdap::getConnexion();
                $ldapConnexion = true;

                /* Check if users OU exists */
                $cnArrayUser = explode(",", ConfigIgestisGlobalVars::ldapUsersOu());
                $firstCnUser = $cnArrayUser[0];
                $findUserOu = $ldap->find($firstCnUser);
                foreach ($findUserOu as $currentFind) {
                    $dn = $currentFind->getDn();
                    if (mb_strtolower($dn, "UTF-8") == mb_strtolower(ConfigIgestisGlobalVars::ldapUsersOu(), "UTF-8")) {
                        $usersOuError = false;
                        break;
                    }
                }

                /* Check if suppliers OU exists */
                $cnArraySupplier = explode(",", ConfigIgestisGlobalVars::ldapSuppliersOu());
                $firstCnSupplier = $cnArraySupplier[0];
                $findSupplierOu = $ldap->find($firstCnSupplier);
                foreach ($findSupplierOu as $currentFind) {
                    $dn = $currentFind->getDn();
                    if (mb_strtolower($dn, "UTF-8") == mb_strtolower(ConfigIgestisGlobalVars::ldapSuppliersOu(), "UTF-8")) {
                        $suppliersOuError = false;
                        break;
                    }
                }

                /* Check if customers OU exists */
                $cnArrayCustomer = explode(",", ConfigIgestisGlobalVars::ldapCustomersOu());
                $firstCnCustomer = $cnArrayCustomer[0];
                $findCustomerOu = $ldap->find($firstCnCustomer);
                foreach ($findCustomerOu as $currentFind) {
                    $dn = $currentFind->getDn();
                    if (mb_strtolower($dn, "UTF-8") == mb_strtolower(ConfigIgestisGlobalVars::ldapCustomersOu(), "UTF-8")) {
                        $customersOuError = false;
                        break;
                    }
                }

            }
            catch(\Exception $e) {
                /* If any ldap error, we store the message in the ldapError variable to send to the twig template */
                $ldapError = $e->getMessage();
            }

        }



        $this->context->render("pages/checkInstall.twig", array(
            "configFileFound" => ConfigIgestisGlobalVars::configFileFound(),
            "virtualHostOk" => !preg_match("#/public/#", $requestUri),
            
            "timezone" => date_default_timezone_get(),
            "jsonExtentionFound" => function_exists("json_encode"),
            "mcryptExtentionFound" =>  function_exists("crypt"),
            "mysqlExtensionFound" => class_exists("PDO"),
            "ldapExtensionFound" => function_exists("ldap_bind"),
            
            
            "cacheFolderExists" => is_dir(ConfigIgestisGlobalVars::cacheFolder()),
            "cacheFolderWritable" => is_writable(ConfigIgestisGlobalVars::cacheFolder()),
            "documentsFolderExists" => is_dir(ConfigIgestisGlobalVars::dataFolder()),
            "documentsFolderWritable" => is_writable(ConfigIgestisGlobalVars::dataFolder()),
            "databaseWork" => $databaseWork,
            "dbDatabaseFound" => $dbDatabaseFound,
            "dbCredentialsOk" => $dbCredentialsOk,
            "dbTablesFound" => $dbTablesFound,
            "mysqlUpdatesAvailable" => ($dbDatabaseFound && $dbCredentialsOk && !$dbTablesFound) || \Igestis\Utils\DBUpdater::hasAvailableUpdates(),
            "mysqlNotLaunchable" => !$dbCredentialsOk || !class_exists("PDO"),

            /* LDAP section */
            "useLdap" => ConfigIgestisGlobalVars::useLdap(),
            "ldapConnexion" => $ldapConnexion,
            "ldapError" => $ldapError,

            "usersOuError" => $usersOuError,
            "customersOuError" => $customersOuError,
            "suppliersOuError" => $suppliersOuError,

            "ouInstallable" => $ldapConnexion && ($usersOuNotFound || $customersOuENotFound || $suppliersOuENotFound)
        ));
        
    }
    
    public function updateDbAction() {
 
        \Igestis\Utils\DBUpdater::init($this->_em);
        $importStatus = \Igestis\Utils\DBUpdater::startUpdate();
        
        if($importStatus) {
            new wizz(\Igestis\I18n\Translate::_("The mysql database has been successfully imported"), WIZZ_SUCCESS);
        }
        else {
            new wizz(\Igestis\I18n\Translate::_("An error has occured during the import process. Try to import the sql file manually."), WIZZ_SUCCESS);
        }
        
        $this->redirect(ConfigControllers::createUrl("igestis_install"));
        
    }
    
}
