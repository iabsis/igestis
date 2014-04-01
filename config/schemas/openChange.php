<?php

/**
 * @var CoreContacts $contact Representation of the contact to be updated / created
 */

return array(
    "displayname" => $this->contact->getLogin(),
    "homemdb" => "CN=Mailbox Store (" . ConfigModuleVars::serverName . "),CN=First Storage Group,CN=InformationStore,CN=" . ConfigModuleVars::serverName . ",CN=Servers,CN=First Administrative Group,CN=Administrative Groups,CN=First Organization,CN=Microsoft Exchange,CN=Services,CN=Configuration," . \ConfigIgestisGlobalVars::LDAP_BASE,
    "homemta" => "CN=Mailbox Store (" . ConfigModuleVars::serverName . "),CN=First Storage Group,CN=InformationStore,CN=" . ConfigModuleVars::serverName . ",CN=Servers,CN=First Administrative Group,CN=Administrative Groups,CN=First Organization,CN=Microsoft Exchange,CN=Services,CN=Configuration," . \ConfigIgestisGlobalVars::LDAP_BASE,
    "legacyexchangedn" => "/o=First Organization/ou=First Administrative Group/cn=Recipients/cn=" . $this->contact->getLogin(),
    "mailnickname" => $this->contact->getLogin(),
    "msexchuseraccountcontrol" => 0,
    "proxyaddresses" => array(
        "=EX:/o=First Organization/ou=First Administrative Group/cn=Recipients/cn=" . $this->contact->getLogin(),
        "smtp:" . ConfigModuleVars::postMaster,
        "X400:c=US;a= ;p=First Organizati;o=Exchange;s=" . $this->contact->getLogin(),
        "SMTP:" . $this->contact->getEmail()
    )
);