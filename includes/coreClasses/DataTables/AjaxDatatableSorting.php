<?php

namespace Igestis\DataTables;

class AjaxDatatableSorting {
    private $fieldsList;
    
    /**
     * Constructor, could take a key/value pear array to automatticaly set the field list
     * @param array $fieldsList
     * @throws \Exception
     */
    public function __construct(array $fieldsList=null) {
        if($fieldsList !== null) {
            foreach($fieldsList as $value) {
                if(!is_string($value)) {
                    throw new \Exception("The field name must be a string");
                }
                
                $this->fieldsList[] = $value;
            }
        }
    }
    
    /**
     * Add a field to the searchable list
     * @param string $fieldName
     * @throws \Exception
     */
    public function addField($fieldName) {
        if(!is_string($fieldName)) {
             throw new \Exception("The field name must be a string");
        }
        
        $this->fieldsList[] = $fieldName;
    }
    
    /**
     * 
     * @param int $id
     * @return string field name
     */
    public function getField($id) {
        return isset($this->fieldsList[$id]) ? $this->fieldsList[$id] : null;
    }

}