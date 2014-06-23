<?php


/**
 * CoreCsvImportLinesDatas
 *
 * @Table(name="CORE_CSV_IMPORT_LINES_DATAS")
 * @Entity(repositoryClass="CoreCsvImportLinesDatasRepository")
 */
class CoreCsvImportLinesDatas
{
    /**
     * @var text $value
     *
     * @Column(name="value", type="text")
     */
    private $value;

    /**
     * @var string $order
     *
     * @Column(name="`order`", type="string")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $order;

    /**
     * @var integer $columnId
     *
     * @Column(name="column_id", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $columnId;

    /**
     * @var CoreCsvImportColumns
     *
     * @ManyToOne(targetEntity="CoreCsvImportColumns")
     * @JoinColumns({
     *   @JoinColumn(name="column_id", referencedColumnName="id")
     * })
     */
    private $column;
    
    /**
     * Create a new field on the csv representation  in the DB
     * @param int $lineNumber Line number of the csv
     * @param \CoreCsvImportColumns $column Associated column
     * @param string $value Value to add on this field
     */
    public  function __construct($lineNumber, \CoreCsvImportColumns $column, $value) {
        $this->order = $lineNumber;
        $this->column = $column;
        $this->columnId = $column->getId();
        $this->value = $value;
    }

    /**
     * Set value
     *
     * @param text $value
     * @return CoreCsvImportLinesDatas
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return text 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set order
     *
     * @param string $order
     * @return CoreCsvImportLinesDatas
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
     * Set columnId
     *
     * @param integer $columnId
     * @return CoreCsvImportLinesDatas
     */
    public function setColumnId($columnId)
    {
        $this->columnId = $columnId;
        return $this;
    }

    /**
     * Get columnId
     *
     * @return integer 
     */
    public function getColumnId()
    {
        return $this->columnId;
    }

    /**
     * Set column
     *
     * @param CoreCsvImportColumns $column
     * @return CoreCsvImportLinesDatas
     */
    public function setColumn(\CoreCsvImportColumns $column = null)
    {
        $this->column = $column;
        return $this;
    }

    /**
     * Get column
     *
     * @return CoreCsvImportColumns 
     */
    public function getColumn()
    {
        return $this->column;
    }
}



// -----------------------------------------------------------------------------------------------------------
class CoreCsvImportLinesDatasRepository extends Doctrine\ORM\EntityRepository {
    public function findDatasForCurrentUser($scriptName) {
        
        $context = \Application::getInstance();
        
        $qb = $this->_em->createQueryBuilder();
        $qb->select("COLUMN.name", "DATAS", "COLUMN.entity")
             ->from("CoreCsvImportLinesDatas", "DATAS")
             ->leftJoin("DATAS.column", "COLUMN")
             ->leftJoin("COLUMN.csvImport", "CSV", "CSV.id=COLUMN.csvImport")
             ->where("CSV.sessionId = :session_id")
             ->andWhere("CSV.scriptName = :scriptName")
             ->andWhere("CSV.contactId = :contactId")
             ->setParameter("session_id", session_id())
             ->setParameter("scriptName", $scriptName)
             ->setParameter("contactId", $context->security->contact->getId())
             ->orderBy("COLUMN.order")
             ->orderBy("DATAS.order");

        return $qb->getQuery()->getArrayResult();
    }
}


