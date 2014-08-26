<?php

/**
 * @HasLifecycleCallbacks
 * @Entity (repositoryClass="CoreContactsRepository")
 * @Table(name="CORE_CONTACTS")
 */
class CoreContacts
{
    private $initialLogin;
    /**
     * @Column(type="string", unique=true, nullable=true)
     */
    private $login;

    /**
     * @Column(type="string")
     */
    private $password;
    
    /**
     * @var string Password before MD5 (only if password changed or new Customer
     */
    private $clearPassword;

    /**
     * @Column(type="string", name="ssh_password")
     */
    private $sshPassword;

    /**
     * @Column(type="string")
     */
    private $firstname;

    /**
     * @Column(type="string")
     */
    private $lastname;

    /**
     * @Column(type="string")
     */
    private $email;
    
    
    /**
     * @Column(type="string", name="ad_sid")
     */
    private $adSid;

    /**
     * @Column(type="integer", name="nb_login_try")
     */
    private $nbLoginTry;

    /**
     * @Column(type="datetime", name="last_login_try")
     */
    private $lastLoginTry;

    /**
     * @Column(type="string", name="change_password_request_id")
     */
    private $changePasswordRequestId;

    /**
     * @Column(type="date", name="change_password_request_date")
     */
    private $changePasswordRequestDate;

    /**
     * @Column(type="string")
     */
    private $address1;

    /**
     * @Column(type="string")
     */
    private $address2;

    /**
     * @Column(type="string", name="postal_code")
     */
    private $postalCode;

    /**
     * @Column(type="string")
     */
    private $city;

    /**
     * @Column(type="string")
     */
    private $phone1;

    /**
     * @Column(type="string")
     */
    private $phone2;

    /**
     * @Column(type="string")
     */
    private $mobile;

    /**
     * @Column(type="string")
     */
    private $fax;

    /**
     * @Column(type="boolean", name="main_contact")
     */
    private $mainContact;

    /**
     * @Column(type="boolean")
     */
    private $active;

    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    private $id;

     /**
     * @Column(name="civility_code")
     * @ManyToOne(targetEntity="CoreCivilities", inversedBy="code")
     */
    private $civilityCode;

    /**
     * @Column(name="country_code")
     * @ManyToOne(targetEntity="CoreCountries", inversedBy="code")
     */
    private $countryCode;

    /**
     * @Column(name="language_code")
     * @ManyToOne(targetEntity="CoreLanguages", inversedBy="code")
     */
    private $languageCode;
    
    /**
     * @var boolean Tell if yes or not, we need to launch the postpersist so we can disable it for the login process
     */
    private $postPersistDisabled;
    


    /**
     * @var CoreUsers Associated user
     * @JoinColumn(name="user_id")
     * @ManyToOne(targetEntity="CoreUsers", inversedBy="contacts")
     */
    private $user;
    
    /**
     * @var bool Must hide quicktour from the homepage ?
     * @Column(type="boolean", name="hide_quicktour")
     */
    private $hideQuicktour;
    

    private $initialPassword;

    /**
     * Constructor - Initialize some variables
     */
    public function __construct() {
        $this->active = 1;
        $this->countryCode = null;
        $this->languageCode = null;
        $this->hideQuicktour = 0;
        $this->postPersistDisabled = false;
    }
    
    public function disablePostPersistProcess() {
        $this->postPersistDisabled = true;
    }
    
    /**
     * Set login
     *
     * @param string $login
     * @return CoreContacts
     */
    public function setLogin($login)
    {
        if($this->initialLogin == "")            $this->initialLogin = $login;
        $this->login = $login;
        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return strtolower($this->login);
    }

    /**
     * Set password
     *
     * @param string $password
     * @return CoreContacts
     */
    public function setPassword($password)
    {
        $this->clearPassword = $password;
        $this->password = $password;
        return $this;
    }
    
    /**
     * Return plain password (only if a new password has been sent) or null
     * 
     * @return mixxed Plain password (only if a new password has been sent) or null
     */
    public function getPlainPassword() {
        return $this->clearPassword;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set sshPassword
     *
     * @param blob $sshPassword
     * @return CoreContacts
     */
    public function setSshPassword($sshPassword)
    {
        $this->sshPassword = $sshPassword;
        return $this;
    }

    /**
     * Get sshPassword
     *
     * @return blob 
     */
    public function getSshPassword()
    {
        return $this->sshPassword;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return CoreContacts
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return CoreContacts
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return CoreContacts
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set nbLoginTry
     *
     * @param boolean $nbLoginTry
     * @return CoreContacts
     */
    public function setNbLoginTry($nbLoginTry)
    {
        $this->nbLoginTry = $nbLoginTry;
        return $this;
    }

    /**
     * Get nbLoginTry
     *
     * @return boolean 
     */
    public function getNbLoginTry()
    {
        return $this->nbLoginTry;
    }

    /**
     * Set lastLoginTry
     *
     * @param datetime $lastLoginTry
     * @return CoreContacts
     */
    public function setLastLoginTry($lastLoginTry)
    {
        $this->lastLoginTry = $lastLoginTry;
        return $this;
    }

    /**
     * Get lastLoginTry
     *
     * @return datetime 
     */
    public function getLastLoginTry()
    {
        return $this->lastLoginTry;
    }

    /**
     * Set changePasswordRequestId
     *
     * @param string $changePasswordRequestId
     * @return CoreContacts
     */
    public function setChangePasswordRequestId($changePasswordRequestId)
    {
        $this->changePasswordRequestId = $changePasswordRequestId;
        return $this;
    }

    /**
     * Get changePasswordRequestId
     *
     * @return string 
     */
    public function getChangePasswordRequestId()
    {
        return $this->changePasswordRequestId;
    }

    /**
     * Set changePasswordRequestDate
     *
     * @param date $changePasswordRequestDate
     * @return CoreContacts
     */
    public function setChangePasswordRequestDate($changePasswordRequestDate)
    {
        $this->changePasswordRequestDate = $changePasswordRequestDate;
        return $this;
    }

    /**
     * Get changePasswordRequestDate
     *
     * @return date 
     */
    public function getChangePasswordRequestDate()
    {
        return $this->changePasswordRequestDate;
    }

    /**
     * Set address1
     *
     * @param string $address1
     * @return CoreContacts
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * Get address1
     *
     * @return string 
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return CoreContacts
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * Get address2
     *
     * @return string 
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     * @return CoreContacts
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string 
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return CoreContacts
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }
    
    /**
     * Set Active directory SID
     *
     * @param string Active directory SID
     * @return CoreContacts
     */
    public function setAdSid($adSid)
    {
        $this->adSid = $adSid;
        return $this;
    }

    /**
     * Get Active directory SID
     *
     * @return string 
     */
    public function getAdSid()
    {
        return $this->adSid;
    }

    /**
     * Set phone1
     *
     * @param string $phone1
     * @return CoreContacts
     */
    public function setPhone1($phone1)
    {
        $this->phone1 = $phone1;
        return $this;
    }

    /**
     * Get phone1
     *
     * @return string 
     */
    public function getPhone1()
    {
        return $this->phone1;
    }

    /**
     * Set phone2
     *
     * @param string $phone2
     * @return CoreContacts
     */
    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;
        return $this;
    }

    /**
     * Get phone2
     *
     * @return string 
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return CoreContacts
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * Get mobile
     *
     * @return string 
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return CoreContacts
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
        return $this;
    }

    /**
     * Get fax
     *
     * @return string 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set mainContact
     *
     * @param boolean $mainContact
     * @return CoreContacts
     */
    public function setMainContact($mainContact)
    {
        $this->mainContact = (bool)$mainContact;
        return $this;
    }

    /**
     * Get mainContact
     *
     * @return boolean 
     */
    public function getMainContact()
    {
        return $this->mainContact;
    }

    /**
     * Return if contact is the main one or not
     * @return bool Is main contact or not
     */
    public function isMainContact() {
        return $this->getMainContact();
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return CoreContacts
     */
    public function setActive($active)
    {
        if($active == false) {
            $this->login = null;
        }
        else {
            $this->login = $this->initialLogin;
        }
        $this->active = $active;
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set civilityCode
     *
     * @param string $civilityCode
     * @return CoreContacts
     */
    public function setCivilityCode( $civilityCode = null)
    {
        $this->civilityCode = $civilityCode;
        return $this;
    }

    /**
     * Get civilityCode
     *
     * @return string 
     */
    public function getCivilityCode()
    {
        return $this->civilityCode;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     * @return CoreContacts
     */
    public function setCountryCode( $countryCode = null)
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string 
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set languageCode
     *
     * @param string $languageCode
     * @return CoreContacts
     */
    public function setLanguageCode( $languageCode = null)
    {
        $this->languageCode = $languageCode;
        return $this;
    }

    /**
     * Get languageCode
     *
     * @return string 
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * Set user
     *
     * @param CoreUsers $user
     * @return CoreContacts
     */
    public function setUser(\CoreUsers $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return CoreUsers 
     */
    public function getUser()
    {
        return $this->user;
    }
    
    
    
    public function hideQuicktour() {
        $this->hideQuicktour = true;
    }
    
    public function getQuicktourStatus() {
        return $this->hideQuicktour;
    }

    public function toArray() {
        $array = get_object_vars($this);
        $buffer = array();
        foreach($array as $key => $field) {
            if(!is_object($field)) {
                $buffer[$key] = $field;
            }
        }
        return $buffer;
    }
    
    public function __toString() {
        return $this->user->getUserLabel() . " (" . ($this->login ? $this->login : 'disabled') . ")";
    }

    /** @PostLoad */
    public function PostLoad()
    {
        $this->initialPassword = $this->password;
        $this->initialLogin = $this->login;
    }

    /**
     * @PrePersist
     * @PreUpdate
     */
    public function PrePersist() {
        if($this->password === null) {
            if(preg_match("/^[0-9a-z]{32}$/", $this->initialPassword)) {
                $this->password = $this->initialPassword;
            }
            else {
                $this->password = IgestisSecurity::generatePassword(IgestisMisc::superRandomize(20));
            }
        }
        else {
            if(!preg_match("/^[0-9a-z]{32}$/", $this->password)) {
                $this->password = IgestisSecurity::generatePassword($this->password);
            }
        }
        
        if(\ConfigIgestisGlobalVars::useLdap()) {
            $ldap = \Igestis\Utils\IgestisLdap::getConnexion();
            
            if(!$this->adSid) {
                // If the AD Sid has not already been set
                $nodesList = $ldap->find(str_replace("%u", $this->initialLogin, \ConfigIgestisGlobalVars::ldapCustomFind()));
                foreach($nodesList as $dn =>$entry){ // For each entry
                    foreach($entry as $attr => $values){ // For each attribute
                        foreach($values as $value){// For each value
                            switch (strtolower($attr)) {
                                case "objectsid" :
                                    $this->adSid = \Igestis\Utils\IgestisLdap::sidBinToString($value);
                                    break;
                            } // End switch
                        } // End for each value
                    }// End for each attribute
                }// End for each entry
            }// Endif  adSid
            else {
                // If AdSid is set, we get the login from the database
                $nodesList = $ldap->find("(objectSid=" . $this->adSid . ")");
                if(count($nodesList)) {
                    foreach($nodesList as $dn =>$entry){ // For each entry
                        foreach($entry as $attr => $values){ // For each attribute
                            foreach($values as $value){// For each value
                                switch (strtolower($attr)) {
                                    case "samaccountname" :                                    
                                        if($this->active) $this->login = $this->initialLogin =  $value;
                                        break;
                                } // End switch
                            } // End for each value
                        }// End for each attribute
                    }// End for each entry
                }
                else {
                    $this->adSid = null;
                }
                
                
            }
        }
    }
    
    /**
     * @PostPersist
     * @PostUpdate
     */
    public function PostPersist() {
        if($this->postPersistDisabled) return;
        
        if(\ConfigIgestisGlobalVars::useLdap()) {
            $ldap = \Igestis\Utils\IgestisLdap::getConnexion();
            
            $createLdapEntry = false;
            $deleteLdapEntry = false;

            if ($this->user->getUserType() == "employee") { ////////////////// Manage employee on LDAP
                
                $ldapOu =  \ConfigIgestisGlobalVars::ldapUsersOu();
                
                $nodesList = $ldap->find("(&(!(objectClass=posixAccount))(uid=" . $this->initialLogin . "))");
                if(count($nodesList)) {
                    throw new Exception(_("This person already exists in LDAP and it's not an employee"));
                    return false;
                }
                
                $userCompany = NULL;
                if($this->login == \ConfigIgestisGlobalVars::igestisCoreAdmin()) return true;
                
                // Is the row already exists on LDAP ?
                $nodesList = $ldap->find(str_replace("%u", $this->initialLogin, \ConfigIgestisGlobalVars::ldapCustomFind()));
                $createLdapEntry = !count($nodesList);
                if($this->active == 0) {
                    $this->login = null;
                    $deleteLdapEntry = true;
                    $createLdapEntry = false;
                }
                
                if(\ConfigIgestisGlobalVars::ldapAdMode()) {
                  // Include here, the fields to add for an active directory user
                  
                    $newPassword = "\"" . $this->clearPassword . "\"";
                    $len = strlen($newPassword);
                    $newPassw = "";
                    for($i=0;$i<$len;$i++) {
                        $newPassw .= "{$newPassword{$i}}\000";
                    }


                    $ldap_array['displayName'] = $this->firstname." ".$this->lastname;                    
                    $ldap_array['givenName'] = $this->firstname;
                    $ldap_array['sn'] = $this->lastname;
                    $ldap_array['mail'] = $this->email;                    
                    $ldap_array["sAMAccountName"] = $this->initialLogin;
                    if($this->clearPassword) $ldap_array["unicodePwd"] = $newPassw;
                    $ldap_array["userAccountControl"] = "544"; 
                    $ldap_array["userPrincipalName"] = str_replace("%u", $this->initialLogin, \ConfigIgestisGlobalVars::ldapCustomBind()); 
                    $ldap_array['l'] = $this->city;
                    $ldap_array['o'] = $userCompany;
                    $ldap_array['telephoneNumber'] = $this->phone1;
                    $ldap_array['homePhone'] = $this->phone2;
                    $ldap_array['mobile'] = $this->mobile;
                    $ldap_array['postalCode'] = $this->postalCode;
                    $ldap_array['facsimileTelephoneNumber'] = $this->fax;
                    $ldap_array['streetAddress'] = \IgestisMisc::unsetEmptyValues(implode("\r\n",array($this->address1, $this->address2)));
                  
                  // Extra fields for the new entry in LDAP
                  if($createLdapEntry) {
                      $ldap_array['objectClass'] = array("top","person","organizationalPerson","user");
                      $ldap_array['name'] = $this->firstname." ".$this->lastname;
                      $ldap_array['cn'] = $this->firstname." ".$this->lastname;
                  }

                  
                }
                else {
                    // Global datas to add to LDAP
                    $ldap_array = array(
                        'objectClass'       => array("top", "posixAccount", "inetOrgPerson", "organizationalPerson"),
                        'uid'               => $this->initialLogin,
                        'userPassword'      => $this->clearPassword ? $ldap->hashPasswd($this->clearPassword) : "",
                        'cn'                => $this->firstname . " " . $this->lastname,
                        'sn'                => $this->lastname,
                        'givenName'         => $this->firstname,
                        'gidNumber'         => "513",
                        'homeDirectory'     => "/home/" . $this->initialLogin,
                        'l'                 => $this->city,
                        'mail'              => $this->email,
                        'o'                 => $userCompany,
                        'telephoneNumber'   => $this->phone1,
                        'homePhone'         => $this->phone2,
                        'mobile'            => $this->mobile,
                        'postalCode'        => $this->postalCode,
                        'st'                => $this->countryCode,
                        'Fax'               => $this->fax,
                        'street'            => \IgestisMisc::unsetEmptyValues(array($this->address1, $this->address2))
                    );
                    
                    // Extra fields for the new entry in LDAP
                    if($createLdapEntry) {
                      $ldap_array['uidNumber'] = \Igestis\Utils\IgestisLdap::getNextUid();
                    }
                }

                
            } elseif ($this->user->getUserType() == "client") { ///////////////////// Manage customers in LDAP
                
                $ldapOu =  \ConfigIgestisGlobalVars::ldapCUstomersOu();
                
                $nodesList = $ldap->find("(&(objectClass=posixAccount)(uid=" . $this->login . "))");
                if(count($nodesList)) {
                    throw new Exception(_("This login already exists in LDAP as employee"));
                    return false;
                }
                
                // Is the row already exists on LDAP ?
                $nodesList = $ldap->find("(uid=" . $this->login . ")");
                $createLdapEntry = !count($nodesList);
                if($this->active == 0) {                    
                    $deleteLdapEntry = true;
                    $createLdapEntry = false;
                }

                if(\ConfigIgestisGlobalVars::ldapAdMode()) {
                    $ldap_array = array(
                        'objectClass'           => array("top", "inetOrgPerson", "organizationalPerson"),
                        'cn'                    => $this->firstname . " " . $this->lastname,
                        'sn'                    => $this->lastname,
                        'givenName'             => $this->firstname,
                        'l'                     => $this->city,
                        'mail'                  => $this->email,
                        'o'                     => $this->user->getUserLabel(),
                        'telephoneNumber'       => $this->phone1,
                        'homePhone'             => $this->phone2,
                        'mobile'                => $this->mobile,
                        'postalCode'            => $this->postalCode,
                        'st'                    => $this->countryCode,
                        'Fax'                   => $this->fax,
                        'street'                => array_filter(array($this->address1, $this->address2))
                    );
                }
                else {
                    // Global datas to add
                    $ldap_array = array(
                        'objectClass'           => array("top", "inetOrgPerson", "organizationalPerson"),
                        'uid'                   => $this->initialLogin,
                        'cn'                    => $this->firstname . " " . $this->lastname,
                        'sn'                    => $this->lastname,
                        'givenName'             => $this->firstname,
                        'l'                     => $this->city,
                        'mail'                  => $this->email,
                        'o'                     => $this->user->getUserLabel(),
                        'telephoneNumber'       => $this->phone1,
                        'homePhone'             => $this->phone2,
                        'mobile'                => $this->mobile,
                        'postalCode'            => $this->postalCode,
                        'st'                    => $this->countryCode,
                        'Fax'                   => $this->fax,
                        'street'                => array_filter(array($this->address1, $this->address2))
                    );      
                }
            } 
            elseif ($this->user->getUserType() == "supplier") { ///////////////////// Manage suppliers in LDAP
                
                $ldapOu =  \ConfigIgestisGlobalVars::ldapSuppliersOu();
                
                $nodesList = $ldap->find("(&(objectClass=posixAccount)(uid=" . $this->login . "))");
                if(count($nodesList)) {
                    throw new Exception(_("This login already exists in LDAP as employee"));
                    return false;
                }
                
                // Is the row already exists on LDAP ?
                $nodesList = $ldap->find("(uid=" . $this->login . ")");
                $createLdapEntry = !count($nodesList);
                if($this->active == 0) {                    
                    $deleteLdapEntry = true;
                    $createLdapEntry = false;
                }
                
                if(\ConfigIgestisGlobalVars::ldapAdMode()) {
                    $ldap_array = array(
                        'objectClass'           => array("top", "inetOrgPerson", "organizationalPerson"),
                        'cn'                    => $this->firstname . " " . $this->lastname,
                        'sn'                    => $this->lastname,
                        'givenName'             => $this->firstname,
                        'l'                     => $this->city,
                        'mail'                  => $this->email,
                        'o'                     => $this->user->getUserLabel(),
                        'telephoneNumber'       => $this->phone1,
                        'homePhone'             => $this->phone2,
                        'mobile'                => $this->mobile,
                        'postalCode'            => $this->postalCode,
                        'st'                    => $this->countryCode,
                        'Fax'                   => $this->fax,
                        'street'                => array_filter(array($this->address1, $this->address2))
                    );  
                }
                else {
                    // Global datas to add
                    
                    $ldap_array = array(
                        'objectClass'           => array("top", "inetOrgPerson", "organizationalPerson"),
                        'uid'                   => $this->initialLogin,
                        'cn'                    => $this->firstname . " " . $this->lastname,
                        'sn'                    => $this->lastname,
                        'givenName'             => $this->firstname,
                        'l'                     => $this->city,
                        'mail'                  => $this->email,
                        'o'                     => $this->user->getUserLabel(),
                        'telephoneNumber'       => $this->phone1,
                        'homePhone'             => $this->phone2,
                        'mobile'                => $this->mobile,
                        'postalCode'            => $this->postalCode,
                        'st'                    => $this->countryCode,
                        'Fax'                   => $this->fax,
                        'street'                => array_filter(array($this->address1, $this->address2))
                    );  
                    
                }
            }else {
                new wizz(sprintf(_("Warning : Unknown '%s' user type"), $this->user->getUserType()), \wizz::$WIZZ_WARNING);
                return false;
            }// END IF USERTYPE
            
            // Remove empty fields of the array
            $ldap_array = array_filter($ldap_array);
            
            // Start LDAP saving ..
            \Igestis\Utils\Hook::callHook("beforeContactLdapSave", new \Igestis\Types\HookParameters(array(
                "contact" => $this,
                "ldap_array" => $ldap_array,
                "ldap_object" => $ldap
            )));
            
            try {
                
                if($deleteLdapEntry) { // Delete the ldap entry
                    foreach ($nodesList as $dn => $entry) {
                        $ldap->deleteNode($dn);
                    }
                }
                elseif($createLdapEntry) { // Add to LDAP
                    foreach ($nodesList as $dn => $entry) {
                        $ldap->deleteNode($dn);
                    }

                    if(defined("\ConfigIgestisGlobalVars::ldapUserRdn()") && \ConfigIgestisGlobalVars::ldapUserRdn() !== false) {
                        // If LDAP_USER_RDN is defined and not false
                        if(\ConfigIgestisGlobalVars::ldapUserRdn() === true) {
                            // LDAP_USER_RDN shouldn't have the value : true
                            throw new Exception(_("Error : LDAP_USER_RDN cannot be set to true. Please set to false or a custom value"));
                            return false;
                        }
                        else {

                            // The LDAP_USER_RDN is correctly defined and setted
                            $rdn = str_replace(array('%username%',
                                                     '%firstname%',
                                                     '%lastname%'),
                                               array($this->login,
                                                     $this->firstname,
                                                     $this->lastname),
                                               \ConfigIgestisGlobalVars::ldapUserRdn());
                            list($param, $paramValue) = explode("=", $rdn);
                            $ldap_array[$param] = $paramValue;
                            $ldap->addNode($rdn . "," . $ldapOu, $ldap_array);
                        }
                    } else {
                        // LDAP_USER_RDN is false or undefined, use the default value
                        if(\ConfigIgestisGlobalVars::ldapAdMode()) {
                            $cn = \Igestis\Utils\IgestisLdap::createCn($this->firstname . " " . $this->lastname);
                            $ldap_array['cn'] = $cn;
                            $ldap->addNode("cn=" . $cn . "," . $ldapOu, $ldap_array);
                        }
                        else {
                            $ldap->addNode("uid=" . $this->login . "," . $ldapOu, $ldap_array);
                        }
                    }
                
                }
                else { // Edit on LDAP
                    foreach ($nodesList as $dn => $node) {
                        if(\ConfigIgestisGlobalVars::ldapAdMode()) {
                            $node->modify($ldap_array);
                        }
                        else {
                            $node->modify($ldap->mergeArrays($nodesList, $ldap_array));
                        }
                        
                    }                        
                }
                if (DEBUG) {
                    Igestis\Utils\Debug::addDump($ldap_array, "ldap_array");
                }
            } catch (Exception $e) {
                // Placer le rollback ici
                throw $e;
                return false;
            }
            
            
            \Igestis\Utils\Hook::callHook("afterContactLdapSave", new \Igestis\Types\HookParameters(array(
                "contact" => $this,
                "ldap_array" => $ldap_array,
                "ldap_object" => $ldap
            )));
            
        }// END IF LDAP        
        
        
    }
    
    /**
     * @PostRemove
     */
    public function postRemove() {
        /*
        if (\ConfigIgestisGlobalVars::useLdap()) {
            $ldap = new \LDAP(\ConfigIgestisGlobalVars::ldapUris(), \ConfigIgestisGlobalVars::ldapBase());
            $ldap->bind(\ConfigIgestisGlobalVars::ldapAdmin(), \ConfigIgestisGlobalVars::ldapPassword());

            if ($this->user->getUserType() == "employee") {
                $ldapOu = \ConfigIgestisGlobalVars::ldapUsersOu();
            } else {
                $ldapOu = \ConfigIgestisGlobalVars::ldapCUstomersOu();
            }

            try {
                $nodesList = $ldap->find("(uid=" . $this->login . ")");
                if(count($nodesList)) {
                    $ldap->deleteNode("uid=" . $this->initialLogin . "," . $ldapOu);
                }
                
            } catch (Exception $e) {
                // Placer le rollback ici
                Igestis\Utils\Debug::addDump("Delete the user " . $this->initialLogin);
                throw $e;
                return false;
            }
        }*/
    }

    /**
     * Ask a reset password and set the contact fields in order to allow it
     */
    public function resetPassword() {
        $this->changePasswordRequestDate = new \DateTime();
        $this->changePasswordRequestId = IgestisMisc::superRandomize(50);
    }

}




// -----------------------------------------------------------------------------------------------------------
class CoreContactsRepository extends Doctrine\ORM\EntityRepository {
    /**
     *
     * @param type $login
     * @param type $password
     * @return CoreContacts User that match with login and password
     */
    public function getFromLoginAndPassword ($login, $password) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c')
            ->from('CoreContacts', 'c')
            ->where('c.login = :login')
            ->andWhere('c.password = :password')
            ->andWhere("c.login != ''")
            ->setParameter('login', $login)
            ->setParameter('password', $password)
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function getCustomersList($arrayMode = true, $showDisabled = false) {        
        try {
            $userCompany = IgestisSecurity::init()->user->getCompany();
            $qb = $this->_em->createQueryBuilder();
            $qb->select("c", "u")
               ->from("CoreContacts", "c")
               ->leftJoin("c.user", "u")
               ->where("c.mainContact = 1");
            if(!$showDisabled) {
                $qb->andWhere("u.isActive=1")
                   ->andWhere("c.active=1");
            }
            $qb->andWhere("u.userType = 'client'")
               ->andWhere("u.company = :company")
               ->setParameter("company", $userCompany);
            if($arrayMode) return $qb->getQuery()->getArrayResult();
            else return $qb->getQuery ()->getResult ();
        } catch (Exception $e) {
            \IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
            return null;
        }        
    }
    
    public function getSuppliersList($arrayMode = true, $showDisabled = false) {        
        try {
            $userCompany = IgestisSecurity::init()->user->getCompany();
            $qb = $this->_em->createQueryBuilder();
            $qb->select("c", "u")
               ->from("CoreContacts", "c")
               ->leftJoin("c.user", "u")
               ->where("c.mainContact = 1");
            if(!$showDisabled) {
                $qb->andWhere("u.isActive=1")
                   ->andWhere("c.active=1");
            }
            $qb->andWhere("u.userType = 'supplier'")
               ->andWhere("u.company = :company")
               ->setParameter("company", $userCompany);
            if($arrayMode) return $qb->getQuery()->getArrayResult();
            else return $qb->getQuery ()->getResult ();
        } catch (Exception $e) {
            \IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
            return null;
        }        
    }
    
    public function getEmployeesList($arrayMode = true, $showDisabled = false) {
        try {
            $userCompany = IgestisSecurity::init()->user->getCompany();
            $qb = $this->_em->createQueryBuilder();
            $qb->select("c", "u")
               ->from("CoreContacts", "c")
               ->leftJoin("c.user", "u")
               ->where("c.mainContact = 1");
            if(!$showDisabled) {
                $qb->andWhere("u.isActive=1")
                   ->andWhere("c.active=1");
            }
            $qb->andWhere("u.userType = 'employee'")
               ->andWhere("COALESCE(c.login, :empty) != :admin")
               ->setParameter("admin", \ConfigIgestisGlobalVars::igestisCoreAdmin())
               ->setParameter("empty", "");
            if(IgestisSecurity::init()->contact->getLogin() != \ConfigIgestisGlobalVars::igestisCoreAdmin()) {
                $qb->andWhere("u.company = :company")
                   ->setParameter("company", $userCompany);
            }
            
            if($arrayMode) return $qb->getQuery()->getArrayResult();
            else return $qb->getQuery ()->getResult ();
        } catch (Exception $e) {
            \IgestisErrors::createWizz($e, IgestisErrors::TYPE_ANY);
            return null;
        }
    }
    
    public function getMainContactForUserId ($user_id) {
                 
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c')
            ->from('CoreContacts', 'c')
            ->where('c.user = :user_id')
            ->andWhere('c.mainContact = 1')
            ->setParameter('user_id', $user_id);
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function getUserFromLogin($login) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select("c")
             ->from("CoreContacts", "c")
             ->andWhere("c.active=1")
             ->andWhere("c.login = :login")
                ->setParameter("login", $login)
             ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findById($id) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select("c")
            ->from("CoreContacts", "c")
            ->andWhere("c.active=1")
            ->andWhere("c.id = :id")
            ->setParameter("id", $id)
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

}
