<?php

/**
 * @var CoreContacts $contact Représentation of the contact to be updated / created
 */

return array(
        "objectClass" => array("top", "inetOrgPerson"),
        "cn"                          => $this->firstname . " " . $this->lastname,
        "sn"                          => $this->lastname,
        "givenName"                   => $this->firstname,
        "streetAddress"               => array_filter(array($this->address1, $this->address2)),
        "postalCode"                  => $this->postalCode,
        "localityName"                => $this->city,
        "st"                          => $this->countryCode,
        "facsimileTelephoneNumber"    => $this->fax,
        "telephoneNumber"             => $this->phone1,
        "homePhone"                   => $this->phone2,
        "mobile"                      => $this->mobile,
        "o"                           => $this->user->getUserLabel(),
        "mail"                        => $this->email
);