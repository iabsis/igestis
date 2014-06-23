<?php


/**
 * @Entity
 * @Table(name="MYSQL_MIGRATION")
 */
class MysqlMigration
{
    /**
     * @Id
     * @var string $module
     * @Column(type="string")
     */
    private $module;
    
    /**
     * @var string $file
     * @Id
     * @Column(type="string")
     */
    private $file;

    /**
     * @var \DateTime $file
     * @Column(type="datetime", name="imported_at")
     */
    private $importedAt;
    
    public function __construct($module, $file) {
        $this->importedAt = new DateTime;
        $this->module = $module;
        $this->file = $file;
    }
    
    /**
     * 
     * @return string
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * 
     * @return string
     */
    public function getFile() {
        return $this->file;
    }
    /**
     * 
     * @return \DateTime
     */

    public function getImportedAt() {
        return $this->importedAt;
    }

    /**
     * 
     * @param type $module
     * @return \MysqlMigration
     */
    public function setModule($module) {
        $this->module = $module;
        return $this;
    }

    /**
     * 
     * @param type $file
     * @return \MysqlMigration
     */
    public function setFile($file) {
        $this->file = $file;
        return $this;
    }

    /**
     * 
     * @param \DateTime $importedAt
     * @return \MysqlMigration
     */
    public function setImportedAt(\DateTime $importedAt) {
        $this->importedAt = $importedAt;
        return $this;
    }
}