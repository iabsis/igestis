<?php

/**
 * CoreCsvImport
 *
 * @Table(name="CORE_CSV_IMPORT")
 * @Entity (repositoryClass="CoreCsvImportRepository")
 */
class CoreCsvImport
{
    /**
     * @var string $sessionId
     *
     * @Column(name="session_id", type="string", length=80)
     */
    private $sessionId;

    /**
     * @var datetime $importDate
     *
     * @Column(name="import_date", type="datetime")
     */
    private $importDate;

    /**
     * @var datetime $importExpireAt
     *
     * @Column(name="import_expire_at", type="datetime")
     */
    private $importExpireAt;

    /**
     * @var string $scriptName
     *
     * @Column(name="script_name", type="string", length=80)
     */
    private $scriptName;

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var CoreContacts
     *
     * @ManyToOne(targetEntity="CoreContacts")
     * @JoinColumns({
     *   @JoinColumn(name="contact_id", referencedColumnName="id")
     * })
     */
    private $contact;
    
    /**
     * @var integer $contactId
     *
     * @Column(name="contact_id", type="integer")
     */
    private $contactId;
    
    /**
     * @OneToMany(targetEntity="CoreCsvImportColumns", mappedBy="csvImport", cascade={"all"}, orphanRemoval=true)
     */
    private $csvColumns;
    
    /**
     * Create a new Import
     * @param string $scriptName Name of the concerned script
     * @param \CoreContacts $contact Person whose done the import process
     */
    public function __construct($scriptName, \CoreContacts $contact) {
        
        $this->scriptName = $scriptName;
        $this->contact = $contact;
        $this->sessionId = session_id();
        
        $this->csvColumns = new Doctrine\Common\Collections\ArrayCollection();
        $this->importDate = new \DateTime();
        $this->importExpireAt = new \DateTime(); 
        $this->importExpireAt->add(new DateInterval("P1D"));
    }


    /**
     * Set sessionId
     *
     * @param string $sessionId
     * @return CoreCsvImport
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set importDate
     *
     * @param datetime $importDate
     * @return CoreCsvImport
     */
    public function setImportDate($importDate)
    {
        $this->importDate = $importDate;
        return $this;
    }

    /**
     * Get importDate
     *
     * @return datetime 
     */
    public function getImportDate()
    {
        return $this->importDate;
    }

    /**
     * Set importExpireAt
     *
     * @param datetime $importExpireAt
     * @return CoreCsvImport
     */
    public function setImportExpireAt($importExpireAt)
    {
        $this->importExpireAt = $importExpireAt;
        return $this;
    }

    /**
     * Get importExpireAt
     *
     * @return datetime 
     */
    public function getImportExpireAt()
    {
        return $this->importExpireAt;
    }

    /**
     * Set scriptName
     *
     * @param string $scriptName
     * @return CoreCsvImport
     */
    public function setScriptName($scriptName)
    {
        $this->scriptName = $scriptName;
        return $this;
    }

    /**
     * Get scriptName
     *
     * @return string 
     */
    public function getScriptName()
    {
        return $this->scriptName;
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
     * Set contact
     *
     * @param CoreContacts $contact
     * @return CoreCsvImport
     */
    public function setContact(\CoreContacts $contact = null)
    {
        $this->contact = $contact;
        return $this;
    }

    /**
     * Get contact
     *
     * @return CoreContacts 
     */
    public function getContact()
    {
        return $this->contact;
    }
    
    /**
     * Add a column to csv
     * @param \CoreCsvImportColumns $column
     */
    public function addColumn(\CoreCsvImportColumns $column) {
        $column->setCsvImport($this);
        $this->csvColumns->add($column);
    }
    
    /**
     * Remove the collection of columns from the csv
     */
    public function eraseColumns() {
        $this->csvColumns->clear();
    }
}

// -----------------------------------------------------------------------------------------------------------
class CoreCsvImportRepository extends Doctrine\ORM\EntityRepository {
    
}