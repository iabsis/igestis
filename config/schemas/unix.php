<?php

/**
 * @var CoreContacts $contact Representation of the contact to be updated / created
 */

return array(
    "update" => array(
        "objectClass" => array("top", "posixAccount"),
        "cn"                      => $contact->firstname . " " . $contact->lastname,
        "uid"                     => $contact->initialLogin,
        "homeDirectory"           => "/home/" . $contact->initialLogin, // Must be compliant with the format UNIX_HOME_DIRECTORY
        "uidNumber"               => \Igestis\Utils\IgestisLdap::getNextUid(),
        "gidNumber"               => "100", // Must be UNIX_GID_NUMBER
        "loginShell"              => $loginShell, // Must be UNIX_LOGIN_SHELL
        "posixAccount"            => '{MD5}' . base64_encode(pack('H*', md5($plainPassword)))
    ),
    "new" => array (
        "userPassword"      => $this->clearPassword ? $ldap->hashPasswd($this->clearPassword) : "",
    ),
    "group" => 
);