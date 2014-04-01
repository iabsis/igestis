<?php

/**
 * @var CoreContacts $contact Representation of the contact to be updated / created
 */

return array(
    "objectClass" => array("top","person","organizationalPerson","user"),
    "displayName" => $contact->firstname." ".$this->lastname,
    "givenName" => $contact->firstname,
    "name" => $contact->firstname." ".$this->lastname,
    "sn" => $contact->lastname,
    "cn" => $contact->firstname." ".$this->lastname,
    "mail" => $contact->email,
    "sAMAccountName" => $contact->initialLogin,
    "unicodePwd" => $newPassw,
    "userAccountControl" => "544",
    "userPrincipalName" => str_replace("%u", $this->initialLogin, \ConfigIgestisGlobalVars::ldapCustomBind()),
    "l" => $contact->city,
    "o" => $userCompany,
    "telephoneNumber" => $contact->phone1,
    "homePhone" => $contact->phone2,
    "mobile" => $contact->mobile,
    "postalCode" => $contact->postalCode,
    "facsimileTelephoneNumber" => $contact->fax,
    "streetAddress" => \IgestisMisc::unsetEmptyValues(implode("\r\n",array($contact->address1, $contact->address2))),
);