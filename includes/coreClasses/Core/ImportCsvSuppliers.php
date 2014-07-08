<?php

namespace Igestis\Core;
/**
 * Manage the import of the suppliers
 *
 * @author Gilles Hemmerlé (iabsis) <giloux@gmail.com>
 */

class ImportCsvSuppliers extends \Igestis\Apis\ImportCsvDatas {
    protected static $_lineNb;
    protected static $_columnsList;
    protected static $_loginKey;
    protected static $_ignoredRows;
    protected static $_updatedRows;
    protected static $_addedRows;
    /**
     * Try to import a row in the database
     * @param array $linesToValide Id de la ligne à valider
     * @param array $row Row to import in the database
     */
    public static function manageRow($linesToValide, $row) {
        self::$_lineNb++;
        
        if(self::$_lineNb == 1) {
            self::$_columnsList = $row;
            foreach ($row as $key => $value) {
                if($value['name'] == "login") self::$_loginKey = $key;
                return;
            }
            throw new Exception(_("Login column was not found"));
        }
        
        if(in_array($row['infos']['lineId'], $linesToValide)) {            
            $contact = self::$context->entityManager->getRepository("CoreContacts")->getUserFromLogin($row['datas'][self::$_loginKey]);
            if($contact == null) {
                // It is a new contact
                $user = \CoreUsers::newSupplier();
                $contact = new \CoreContacts();
                self::$_addedRows++;
            }
            else {
                // The contact already exists
                self::$_updatedRows++;
                $user = $contact->getUser();                
                if($user->getUserType() != \CoreUsers::USER_TYPE_SUPPLIER) {
                    throw new Exception(_("This login is already associated to a non supplier contact"));
                }
            }
            
            foreach ($row['datas'] as $lineNb => $fieldValue) {
                switch (self::$_columnsList[$lineNb]['entity']) {
                    case "CoreUsers" :
                        $method = "set" . ucfirst(self::$_columnsList[$lineNb]['name']);
                        \Igestis\Utils\Debug::addDump("CoreUser::set$method('" . $fieldValue . "')");
                        $user->$method($fieldValue);
                        break;
                    case "CoreContacts" :
                        $method = "set" . ucfirst(self::$_columnsList[$lineNb]['name']);
                        \Igestis\Utils\Debug::addDump("CoreContacts::set$method('" . $fieldValue . "')");
                        $contact->$method($fieldValue);
                        break;
                }
            }
            
            $user->AddOrEditContact($contact);
            \Igestis\Utils\Debug::addLog(_("Persisting the new user :"));
            \Igestis\Utils\Debug::addDump($user, "UserImported", 5);
            self::$context->entityManager->persist($user);
            self::$context->entityManager->flush();
        }
        else {
            self::$_ignoredRows++;
        }
        
    }

    /**
     * Initialise all static values
     */
    public static function init() {
        self::$_lineNb = 0;
        self::$_columnsList = array();
        self::$_loginKey = null;
        self::$_ignoredRows = 0;
        self::$_updatedRows = 0;
        self::$_addedRows = 0;
    }

    /**
     * Return the formatted string of the import result
     * @return string Formatted report string
     */
    public static function report() {
        $returnedString = "Import completed successful.\n";
        $returnedString .= sprintf(_("%d added, %d updated, %d passed"), self::$_addedRows, self::$_updatedRows, self::$_ignoredRows);
        
        return $returnedString;
    }
}