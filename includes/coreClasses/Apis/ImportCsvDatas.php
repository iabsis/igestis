<?php

namespace Igestis\Apis;
/**
 * Allow to import any datas to igestis
 *
 * @author Gilles Hemmerlé
 */
abstract class ImportCsvDatas extends ImportDatas {
    
    /**
     *
     * @var sting Csv file target
     */
    protected $filename;
    /**
     *
     * @var string Delimiter 
     */
    protected $delimiter;
    /**
     *
     * @var string Enclosre
     */
    protected $enclosure;
    /**
     *
     * @var object Pointer for the csv file fopen
     */
    protected $csvFilePointer;
    /**
     *
     * @var array List of the columns allowed on the csv file 
     */
    protected $columnsList;
    protected $columEntities;
    /**
     * @var \Application Get the context 
     */
    protected static $context;
    
    /**
     *
     * @var \CoreCsvImport
     */
    protected $csvFileEntity;
    protected $scriptName;
    protected $lineNumber;

    /**
     * 
     * @param type $filename Emplacement of the csv file to parse
     * @param type $delimiter Charactere that separating each fields
     * @param type $enclosure Characters that surround the datas of each columns
     */
    public function __construct($scriptName, $filename, $delimiter=",", $enclosure='"') {
        
        if(trim($scriptName) == "") {
            throw new \Exception(_("The scriptName is required"));
        }
        
        $this->filename = $filename;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;     
        self::$context = \Application::getInstance();
        $this->scriptName = $scriptName;
        $this->lineNumber = -1;
    }
    
    /**
     * Open the file and save the pointer to the file
     * @throws \Exception If the file doe not exist
     * @throws \Exception if fgetcsv failed or error during opening file
     */
    protected function openCsvFile() {
        if(!is_file($this->filename) && !is_readable($this->filename)) {
            throw new \Exception(_("The csv file does not exists or is not readable"));
        }
                
        try {
            $this->csvFilePointer = fopen($this->filename, "r");
            $headerLine = $this->getNextLine();
            //\Igestis\Utils\Dump::show($headerLine); exit;
        } catch (\Exception $exc) {
            throw $exc;
        }
        
        $foundColumns = array();
        $wrongColumns = array();
        foreach ($headerLine as $key => $column) {
            $foundColumns[] = $column;
            if(!isset($this->columnsList[$column])) {
                $wrongColumns[] = "'" .$column . "'";
            }
            $this->columnsList[$column]['columnNumber'] = $key;
        }
        
        if(count($wrongColumns)) {
            throw new \Exception(_(sprintf("The column(s) %s is/are not allowed in this import section", implode(",", $wrongColumns))));
        }
        
        $missedColumns = array();
        foreach ($this->columnsList as $key =>$column) {
            if($column['required'] == true && !in_array($key, $foundColumns)) {
                $missedColumns[] = "'" . $key . "'";
            }
        }
        
        if(count($missedColumns)) {
            throw new \Exception(_(sprintf("The column(s) %s is/are required but missing", implode(",", $missedColumns))));
        }
        
        // Create the new entity and clean up old datas        
        $this->setNewCsvImportEntity();
        
    }
    
    /**
     * Set the csv entity and clean all old datas
     */
    private function setNewCsvImportEntity(){
        // Search if any existing csvReport fort the concerned scriptName
        $oldEntities = self::$context->entityManager->getRepository("CoreCsvImport")->findBy(array(
            "sessionId" => session_id(),
            "contact" => self::$context->security->contact,
            "scriptName" =>  $this->scriptName
        ));        
        
        if($oldEntities != null) {
            foreach ($oldEntities as$oldEntity) {
                self::$context->entityManager->remove($oldEntity);
                self::$context->entityManager->flush();
            }
        }

        $this->csvFileEntity = new \CoreCsvImport($this->scriptName, self::$context->security->contact);       
        
        // Drop all old columns and add the new ones ...
        $this->csvFileEntity->eraseColumns();
        foreach ($this->columnsList as $columnName => $currentColumn) {
            if($currentColumn['columnNumber'] !== null) {
                // The column has been found in the passed csv file, add it to the csv to populate DB
                $csvColumn = new \CoreCsvImportColumns($columnName, $currentColumn['columnNumber'], $currentColumn['entity']);
                $this->csvFileEntity->addColumn($csvColumn);
                self::$context->entityManager->persist($csvColumn);
                self::$context->entityManager->flush();
                $this->columEntities[$currentColumn['columnNumber']] = $csvColumn;
            }
        }
    }
    
    /**
     * Get the next csv line
     * @return mixed Array with the actual line datas or false if not anymore lines
     * @throws \Exception
     */
    protected function getNextLine() {
        try {
            $this->lineNumber++;
            if($this->enclosure) {
                return fgetcsv($this->csvFilePointer, 0, $this->delimiter, $this->enclosure);
            }
            else {
                $line = trim(fgets($this->csvFilePointer));
                if(!$line) return false;
                return explode($this->delimiter, $line);
            }
            
        } catch (\Exception $exc) {
            throw $exc;
            return false;
        }
        
    }
    
    /**
     * Parse each lines of the csv and save thems to the database tmp table
     */
    public function startParsing() {
        self::$context->entityManager->beginTransaction();        
        try {
            
            $this->openCsvFile();
            while ($currentLine = $this->getNextLine()) {
                foreach ($currentLine as $fieldKey => $fieldValue) {
                    $csvLine = new \CoreCsvImportLinesDatas($this->lineNumber, $this->columEntities[$fieldKey], $fieldValue);
                    self::$context->entityManager->persist($csvLine);
                }
            }     
            
            self::$context->entityManager->persist($this->csvFileEntity);
            self::$context->entityManager->flush();
            self::$context->entityManager->commit();
            
        } catch (Exception $exc) {
            
            self::$context->entityManager->rollback();
            throw $exc;
        }
                    
    }
    
    /**
     * Add an available column to be parsed by the csv import script
     * 
     * @param type $headerName How this field must be named on the csv file ?
     * @param type $required Is this column must appears on the csv file ?
     */
    public function addColumn($headerName, $entity, $required=false) {
        $this->columnsList[$headerName] = array(
            "required" =>$required,
            "entity" => $entity,
            "columnNumber" => null
        );
    }
    
    /**
     * Return the csv data in array format
     * @param string $scriptName
     * @return array of the csv datas retrived from the database
     * @throws \Exception If the scriptName is not specified or blank
     */
    public static function datasToArray($scriptName) {
        $bufferedDatas = array();
        
        if(trim($scriptName) == "") {
            throw new \Exception(_("The scriptName is required"));
        }
        
        if(self::$context == null) {
            self::$context  = \Application::getInstance();
        }
        
        
        // Retrieve datas from the database
        $aCsvDatas = self::$context->entityManager->getRepository("CoreCsvImportLinesDatas")->findDatasForCurrentUser($scriptName);
        $currentLine = -1;
        $firstLine = true;
        $columnList = array();
        
        foreach ($aCsvDatas as $field) {
            if($currentLine < $field[0]['order']) {
                $bufferedDatas[]  = array();
                if($currentLine != -1) $firstLine = false;
                $bufferedDatas[count($bufferedDatas) - 1]['infos'] = array(
                    "lineId" => $field[0]['order'],
                    "isNew" => true
                );                
            }
            if($firstLine) {
                $columnList[] = array("name" => $field['name'], "entity" => $field['entity']);
            }
            $currentLine = $field[0]['order'];
            
            $bufferedDatas[count($bufferedDatas) - 1]['datas'][] = $field[0]['value'];            
        }
        
        
        // Return the datas in the array format
        return  array_merge(array($columnList),  $bufferedDatas);
    }
    
   /**
    *  Validate datas and create the customers
    * @param string $scriptName Name of the concerned script
    * @param array $linesToValide Array with all columns id to validate (-1 to disable the row)
    * @throws \Exception
    */
    public static function valideDatas($scriptName, $linesToValide) {
        if(trim($scriptName) == "") {
            throw new \Exception(_("The scriptName is required"));
        }
        
        if(self::$context == null) {
            self::$context  = \Application::getInstance();
        }
        
        
        self::$context->entityManager->beginTransaction();
        
        try {
            $aAllDatas = self::datasToArray($scriptName);
            foreach ($aAllDatas as $row) {
                static::manageRow($linesToValide, $row);
            }
            self::$context->entityManager->commit();
        } catch (Exception $exc) {
            self::$context->entityManager->rollback();
            throw $exc;
        }
        
    }
    
    /**
     * 
     * @param array $linesToValide
     * @param array $row
     * @abstract
     */
    abstract public static function manageRow($linesToValide, $row);
    /**
     * Initialise the variables
     * @abstract
     */
    abstract public static function init();
    /**
     * Return the report with the update and add count
     * @return string result of the report
     * @abstract
     */
    abstract public static function report();
}