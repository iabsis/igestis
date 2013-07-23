<?php


/**
 * CoreCsvImportColumns
 *
 * @Table(name="CORE_CSV_IMPORT_COLUMNS")
 * @Entity
 */
class CoreCsvImportColumns
{
    /**
     * @var string $name
     *
     * @Column(name="name", type="string", length=45)
     */
    private $name;
    
    /**
     * @var string $entity
     *
     * @Column(name="entity", type="string", length=90)
     */
    private $entity;

    /**
     * @var string $order
     *
     * @Column(name="`order`", type="integer")
     */
    private $order;

    /**
     * @var integer $id
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var CoreCsvImport
     *
     * @ManyToOne(targetEntity="CoreCsvImport", cascade={"all"})
     * @JoinColumns({
     *   @JoinColumn(name="csv_import_id", referencedColumnName="id")
     * })
     */
    private $csvImport;
    
    public function __construct($name, $order, $entity) {
        $this->name = $name;
        $this->order = $order;
        $this->entity = $entity;
    }


    /**
     * Set name
     *
     * @param string $name
     * @return CoreCsvImportColumns
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set entity
     *
     * @param string $entity
     * @return CoreCsvImportColumns
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Get entity
     *
     * @return string 
     */
    public function getEntity()
    {
        return $this->entity;
    }

    
    /**
     * Set order
     *
     * @param string $order
     * @return CoreCsvImportColumns
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return string 
     */
    public function getOrder()
    {
        return $this->order;
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
     * Set csvImport
     *
     * @param CoreCsvImport $csvImport
     * @return CoreCsvImportColumns
     */
    public function setCsvImport(\CoreCsvImport $csvImport = null)
    {
        $this->csvImport = $csvImport;
        return $this;
    }

    /**
     * Get csvImport
     *
     * @return CoreCsvImport 
     */
    public function getCsvImport()
    {
        return $this->csvImport;
    }
}