<?php

/**
 * @Entity (repositoryClass="CoreUsersRepository")
 * @Table(name="CORE_USERS")
 * @HasLifecycleCallbacks
 */
class CoreUsers
{
    const USER_TYPE_EMPLOYEE = "employee";
    const USER_TYPE_CUSTOMER = "client";
    const USER_TYPE_SUPPLIER = "supplier";
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany(targetEntity="CoreDepartments", inversedBy="users", cascade={"persist"})
     * @JoinTable(name="CORE_ASSOC_USERS_DEPARTMENTS",
     *   joinColumns={
     *     @JoinColumn(name="user_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @JoinColumn(name="department_id", referencedColumnName="id")
     *   }
     * )
     */
    private $departments;    
    
    /**
     * @var string $userLabel
     * @Column(type="string", name="user_label")
     */
    private $userLabel;

    /**
     * @var string $userType
     * @Column(type="string", name="user_type")
     */
    private $userType;

    /**
     * @Column(type="string", name="tva_number")
     * @var string $tvaNumber
     */
    private $tvaNumber;

    /**
     * @Column(type="boolean", name="tva_invoice")
     * @var boolean $tvaInvoice
     */
    private $tvaInvoice;

    /**
     * @Column(type="string", name="rib")
     * @var string $rib
     */
    private $rib;

    /**
     * @Column(type="string", name="account_code")
     * @var string $accountCode
     */
    private $accountCode;

    /**
     * @Column(type="boolean", name="is_active")
     * @var boolean $isActive
     */
    private $isActive;

    /**
     * @Id @Column(type="integer") @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="string", name="client_type_code")
     * @var CoreClientTypeCode
     */
    private $clientTypeCode;

    /**
     * @JoinColumn(name="company_id", referencedColumnName="id")
     * @OneToOne(targetEntity="CoreCompanies")
     * @var CoreCompanies
     */
    private $company;
    
    /**
     * @OneToMany(targetEntity="CoreUsersRights", mappedBy="user", cascade={"remove"}, indexBy="moduleName")
     */
    private $rightsList;
    
    
    /**
     * @OneToMany(targetEntity="CoreContacts", mappedBy="user",cascade={"all"}, orphanRemoval=true)
     * @OrderBy({"mainContact" = "DESC", "lastname" = "ASC", "firstname" = "ASC"})
     */
    private $contacts;

    public function __construct() {
        $this->contacts = new Doctrine\Common\Collections\ArrayCollection();
        $this->rightsList = new Doctrine\Common\Collections\ArrayCollection();
        $this->departments = new \Doctrine\Common\Collections\ArrayCollection();

        // Default values
        $this->tvaNumber = "";
        $this->tvaInvoice = true;
        $this->accountCode = "";
        $this->isActive = true;
    }
    
    /**
     * Crée un nouvel employé
     * @return CoreUsers Un CoreUser définit en tant qu'employé
     */
    public static function newEmployee() {
        $employee = new self;
        $employee->setUserType("employee");
        return $employee;
    }
    
    /**
     * Crée un nouveau client
     * @return CoreUsers Un CoreUser définit en tant que client
     */
    public static function newCustomer() {
        $customer = new self;
        $customer->setUserType("client");
        return $customer;
    }
    
    public static function newSupplier() {
        $supplier = new self;
        $supplier->setUserType("supplier");
        return $supplier;
    }

    /**
     * Set userLabel
     *
     * @param string $userLabel
     * @return CoreUsers
     */
    public function setUserLabel($userLabel)
    {
        $this->userLabel = $userLabel;
        return $this;
    }

    /**
     * Get userLabel
     *
     * @return string 
     */
    public function getUserLabel()
    {
        return $this->userLabel;
    }

    /**
     * Set userType
     *
     * @param string $userType
     * @return CoreUsers
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;
        return $this;
    }

    /**
     * Get userType
     *
     * @return string 
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * Set tvaNumber
     *
     * @param string $tvaNumber
     * @return CoreUsers
     */
    public function setTvaNumber($tvaNumber)
    {
        $this->tvaNumber = $tvaNumber;
        return $this;
    }

    /**
     * Get tvaNumber
     *
     * @return string 
     */
    public function getTvaNumber()
    {
        return $this->tvaNumber;
    }

    /**
     * Set tvaInvoice
     *
     * @param boolean $tvaInvoice
     * @return CoreUsers
     */
    public function setTvaInvoice($tvaInvoice)
    {
        $this->tvaInvoice = $tvaInvoice ? true : false;
        return $this;
    }

    /**
     * Get tvaInvoice
     *
     * @return boolean 
     */
    public function getTvaInvoice()
    {
        return $this->tvaInvoice;
    }

    /**
     * Set rib
     *
     * @param string $rib
     * @return CoreUsers
     */
    public function setRib($rib)
    {
        $this->rib = $rib;
        return $this;
    }

    /**
     * Get rib
     *
     * @return string 
     */
    public function getRib()
    {
        return $this->rib;
    }

    /**
     * Set accountCode
     *
     * @param string $accountCode
     * @return CoreUsers
     */
    public function setAccountCode($accountCode)
    {
        $this->accountCode = $accountCode;
        return $this;
    }

    /**
     * Get accountCode
     *
     * @return string 
     */
    public function getAccountCode()
    {
        return $this->accountCode;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return CoreUsers
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
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
     * Set clientTypeCode
     *
     * @param CoreClientType $clientTypeCode
     * @return CoreUsers
     */
    public function setClientTypeCode(\CoreClientType $clientTypeCode = null)
    {
        $this->clientTypeCode = $clientTypeCode;
        return $this;
    }

    /**
     * Get clientTypeCode
     *
     * @return CoreClientType 
     */
    public function getClientTypeCode()
    {
        return $this->clientTypeCode;
    }

    /**
     * Set company
     *
     * @param CoreCompanies $company
     * @return CoreUsers
     */
    public function setCompany(\CoreCompanies $company = null)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Get company
     *
     * @return CoreCompanies 
     */
    public function getCompany()
    {
        return $this->company;
    }
    
    /**
     * Get list of contacts
     * @return @return Doctrine\Common\Collections\Collection List of contacts
     */
    public function getContacts() {
        return $this->contacts;
    }   
    
    public function __toString() {
        return $this->userLabel;
    }


    /**
     * Ajoute ou modifie un contact en mettant à jour le paramètre mainContact des autres contacts
     * @param CoreContacts $contact Contact à éditer ou ajouter
     */
    public function AddOrEditContact(\CoreContacts $contact) {

        // Get the contact key, if contact exists
        $contactKey = null;
        reset($this->contacts);
        foreach ($this->contacts as $key => $existingContact) {
            if($existingContact->getId() == $contact->getId()) {
                $contactKey = $this->contacts->indexOf($existingContact);
                break;
            }
        }

        // If contact is main contact
        if ($contact->isMainContact()) {

            reset($this->contacts);

            foreach ($this->contacts as $existingContact) {
                $existingContact->setMainContact(false);
            }

            $contact->setMainContact(true);

        }
        else {
            $mainContact = true;
            reset($this->contacts);
            foreach ($this->contacts as $existingContact) {
                if ($existingContact->isMainContact()) {
                    $mainContact = false;
                    break; // no need for further checks
                }
            }
            $contact->setMainContact($mainContact);
        }


        if ($contactKey != null) {
            // Edit an existing contact

            $this->contacts->set($contactKey, $contact);
        }
        else {
            // Create a new contact
            $this->contacts->add($contact);
            $contact->setUser($this); // set the owning side of the relation too!
        }
    }

    /**
     * Delete a contact of the user
     * @param CoreContacts $contact Contact to delete
     */
    public function removeContact(\CoreContacts $contact) {

        // Must have at least one contact
        if($this->contacts->count() <= 1) return;

         $this->contacts->removeElement($contact);
         if($contact->isMainContact()) {
             foreach ($this->contacts as $existingContact) {
                 if(!$existingContact->isMainContact()) {
                     $existingContact->setMainContact(true);
                     break;
                 }
             }
         }
    }

    public function updateContact(\CoreContacts $contact) {
        $index = $this->contacts->indexOf($contact);
        $this->contacts->set($index, $contact);
    }
    
    /**
     * Liste des droits
     * @return Doctrine\Common\Collections\ArrayCollection()
     */
    public function getRightsList() {
        return $this->rightsList;
    }

    public function addRightsList(\CoreUsersRights $right = null)
    {
        $right->setUser($this);
        $this->rightsList->add($right);
        return $this;
    }
    
    public function removeRight(\CoreUsersRights $right) {
        $this->rightsList->removeElement($right);
        $right->unsetUser();
    }
    
    public function disable() {
        $this->isActive = 0;
        foreach ($this->contacts as $currentContact) {
            $currentContact->setActive(0);
        }
    }
    
    /**
     * Add department
     *
     * @param CoreDepartments $department
     * @return CoreUsers
     */
    public function addDepartment(\CoreDepartments $department)
    {
        $this->departments[] = $department;
        return $this;
    }

    /**
     * Get department
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getDepartments()
    {
        return $this->departments;
    }
    
    public function removeDepartments() {
        $this->departments->clear();
        return $this;
    }

    /**
     * @PreUpdate
     * @PrePersist
     */
    public function PreSave() {
        
        if($this->getCompany() == null) {
            $security = IgestisSecurity::init();
            $userCompany = $security->user->getCompany();
            if($userCompany != null)
            {
                $this->company  = $userCompany;
            }
        }
        
        //$mainContact = $this->getMainContact();
        
        /*if($this->getUserType() == "client" && $this->getClientTypeCode() == "PART") {
            $this->setUserLabel($mainContact->getFirstname() . " " . $mainContact->getLastname());
        }*/
    }
    
    /**
     * Return the main contact associated to this user.
     * @return \CoreContacts
     */
    public function getMainContact() {
        reset($this->contacts);
        foreach ($this->contacts as $existingContact) {
            if($existingContact->isMainContact()) {
                return $existingContact;
            }
        }
    }
    
    /**
     * @PrePersist
     * @PreUpdate
     */
    public function PrePersist() {
        $mainContact = $this->getMainContact();
        if($this->userType == "client" || $this->userType == "supplier") {
            if($this->clientTypeCode == "PART") {
                $this->userLabel = $mainContact->getFirstname() . " " . $mainContact->getLastname();
            }
            else {
                if($this->userLabel == "") {
                    $this->userLabel = $mainContact->getFirstname() . " " . $mainContact->getLastname();
                }
            }
        }
        else {
            $this->userLabel = $mainContact->getFirstname() . " " . $mainContact->getLastname();
        }
    }
    
}

// -----------------------------------------------------------------------------------------------------------
class CoreUsersRepository extends Doctrine\ORM\EntityRepository {
    public function getUserFromContactId($contactId) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select("u")
            ->from("CoreUsers", "u")
            ->leftJoin("u.contacts", "c")
            ->andWhere("c.id= :contactId")
            ->andWhere("u.isActive=1")
            ->setParameter("contactId", $contactId)
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    public function find($id, $lockMode = Doctrine\DBAL\LockMode::NONE, $lockVersion = null) {
        $user = parent::find($id, $lockMode, $lockVersion);
        if(!$user) return null;
        
        $userCompany = \IgestisSecurity::init()->user->getCompany();
        if(\IgestisSecurity::init()->contact->getLogin() != \ConfigIgestisGlobalVars::igestisCoreAdmin() && $user->getCompany()->getId() != $userCompany->getId()) {
            return null;
        }
        return $user;
    }
    
    public function findAll($includeDisabledUsers=true) {
        try {
            $userCompany = \IgestisSecurity::init()->user->getCompany();
            $qb = $this->_em->createQueryBuilder();
            $qb->select("u")
               ->from("CoreUsers", "u")
               ->where("u.company = :company")
               ->setParameter("company", $userCompany);
            if(!$includeDisabledUsers) {
                $qb->andWhere("u.isActive = 1");
            }
        }
        catch (\Exception $e) {
            throw $e;
        }
        return $qb->getQuery()->getResult();         
    }
}