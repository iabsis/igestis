<?php

namespace Igestis\Ldap;

/**
 * Description of LDAPRepositoryConfig
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */
class LDAPRepositoryConfig {
    /**
     *
     * @var array List of configured fields depending of the general ldap configuration
     */
    private static $fields;
    
    const ACCESS_READ_ONLY = "READ_ONLY";
    const ACCESS_WRITE_ONLY = "WRITE_ONLY";
    const ACCESS_READ_WRITE = "READ_WRITE";
    
    /**
     * Add a field read only ldap field
     * @param string $igestisName Name into iGestis (example : "CoreContacts.firstname")
     * @param string $ldapName (example: "firsname")
     * @param \Closure $specialClosure Special function which will transform data to put into AD
     * The closure will receive the $data in argument and would return the fixed data
     */
    public static function addReadOnlyField($igestisName, $ldapName, \Closure $specialClosure=null) {
        self::addField(self::ACCESS_READ_ONLY, $igestisName, $ldapName, $specialClosure);
    }
    
    /**
     * Add a field read only ldap field
     * @param string $igestisName Name into iGestis (example : "CoreContacts.firstname")
     * @param string $ldapName (example: "firsname")
     * @param \Closure $specialClosure Special function which will transform data to put into AD
     * The closure will receive the $data in argument and would return the fixed data
     */
    public static function addReadWriteField($igestisName, $ldapName, \Closure $specialClosure=null) {
        self::addField(self::ACCESS_READ_WRITE, $igestisName, $ldapName, $specialClosure);
    }
    
    /**
     * Add a write only ldap field
     * @param string $igestisName Name into iGestis (example : "CoreContacts.firstname")
     * @param string $ldapName (example: "firsname")
     * @param \Closure $specialClosure Special function which will transform data to put into AD
     * The closure will receive the $data in argument and would return the fixed data
     */
    public static function addWriteOnly($igestisName, $ldapName, \Closure $specialClosure=null) {
        self::addField(self::ACCESS_WRITE_ONLY, $igestisName, $ldapName, $specialClosure);
    }
    
    /**
     * Add the field into the config class
     * @param string $type
     * @param string $igestisName
     * @param string $ldapName
     * @param \Closure $specialClosure
     */
    private static function addField($type, $igestisName, $ldapName, \Closure $specialClosure=null) {
        self::$fields = array(
            "TYPE"  => $type,
            "IGESTIS_NAME" => $igestisName,
            "LDAP_NAME" => $ldapName,
            "CLOSURE" => $specialClosure
        );
    }
}
