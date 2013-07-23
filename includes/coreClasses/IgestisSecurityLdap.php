<?php

/**
 * Security management of the igestis application (can authenticate users and check rights for each application part
 *
 * @author Gilles Hemmerlé
 */
class IgestisSecurityLdap extends IgestisSecurity {

    /**
     * Authentication method overwritten to synch password from LDAP before to try authentication from mysql database
     * @param string $login
     * @param string $password 
     */
    public function authenticate($login, $password) {
        if (isset($_POST['sess_login']) && isset($_POST['sess_password']) && trim($_POST['sess_login']) && trim($_POST['sess_password'])) {
            $contact = $this->context->entityManager->getRepository("CoreContacts")->findOneBy(array("login" => $_POST['sess_login']));            
            if (!$contact) {
                if(\ConfigIgestisGlobalVars::LDAP_AUTO_IMPORT_USER) {
                    // Search user on LDAP, and try to import data from ldap
                    if(!Igestis\Utils\IgestisLdap::importUser($_POST['sess_login'], $_POST['sess_password'])) return false;
                    $contact = $this->context->entityManager->getRepository("CoreContacts")->findOneBy(array("login" => $_POST['sess_login']));
                    if(!$contact) return false;
                }
                else {
                    return false;
                }
            }
            
            if (!\Igestis\Utils\IgestisLdap::ldapSynchDatas($contact, $_POST['sess_password']))  return false;
            
            // Save the possible new ldap password to the local mysql databas.
            if ($contact->getUser()->getUserType() == "employee") {
                try {
                    $contact->disablePostPersistProcess();
                    $this->context->entityManager->persist($contact);
                    $this->context->entityManager->flush();
                } catch (\Exception $e) {
                    IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
                    return false;
                }
            }
        }
        return parent::authenticate($login, $password);
    }

}