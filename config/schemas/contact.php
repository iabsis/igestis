<?php

/**
 * @var CoreContacts $contact Representation of the contact to be updated / created
 */

return array(
    "objectClass" => array("top", "inetOrgPerson"),
    "cn"                          => $contact->firstname . " " . $this->lastname,
    "sn"                          => $contact->lastname,
    "givenName"                   => $contact->firstname,
    "streetAddress"               => array_filter(array($contact->address1, $contact->address2)),
    "postalCode"                  => $contact->postalCode,
    "localityName"                => $contact->city,
    "st"                          => $contact->countryCode,
    "facsimileTelephoneNumber"    => $contact->fax,
    "telephoneNumber"             => $contact->phone1,
    "homePhone"                   => $contact->phone2,
    "mobile"                      => $contact->mobile,
    "o"                           => $contact->user->getUserLabel(),
    "mail"                        => $contact->email
);