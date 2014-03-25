<?php

namespace Igestis\Forms;

/**
 * Description of UploadedFiles
 *
 * @author Gilles Hemmerlé
 */
class UploadedFiles implements \Iterator, \Countable {
    /**
     * @var \Igestis\Forms\UniqueFile[] List of uploaded files
     */
    public $filesList = array();
    
    /**
     * @var int Cursor position of the travesable array
     */
    private $position = 0;
    
    /**
     * 
     * @return @var \Igestis\Forms\UniqueFile The next uploaded file
     */
    public function current() {
        return $this->filesList[$this->position];
    }
    
    /**
     * 
     * @return @var \Igestis\Forms\UniqueFile|null The first uploaded file
     */
    public function getFirst() {
        if(!isset($this->filesList[0])) return null;
        return $this->filesList[0];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function valid() {
        return isset($this->filesList[$this->position]);
    }
    
    public function collect($dataFormItem) {
        if(is_array($dataFormItem) && isset($dataFormItem['tmp_name']) && $dataFormItem['tmp_name'] != "") {
            reset($dataFormItem);
            if(is_array($dataFormItem['tmp_name'])) {
                // It is a multiple file
                foreach ($dataFormItem['tmp_name'] as $key => $tmpName) {      
                    if(isset($dataFormItem['tmp_name'][$key]) && $dataFormItem['tmp_name'][$key] != "") {
                        $file = new \Igestis\Forms\UniqueFile();
                        $file->setError($dataFormItem['error'][$key])
                             ->setName($dataFormItem['name'][$key])
                             ->setSize($dataFormItem['size'][$key])
                             ->setTmpName($dataFormItem['tmp_name'][$key])
                             ->setType($dataFormItem['type'][$key]);
                        $this->filesList[] = $file;
                    }
                }
            }
            else {
                // It's a single file
                if(isset($dataFormItem['tmp_name']) && $dataFormItem['tmp_name'] != "") {
                    $file = new \Igestis\Forms\UniqueFile();
                    $file->setError($dataFormItem['error'])
                         ->setName($dataFormItem['name'])
                         ->setSize($dataFormItem['size'])
                         ->setTmpName($dataFormItem['tmp_name'])
                         ->setType($dataFormItem['type']);
                    $this->filesList[] = $file;     
                }                           
            }
        }
    }
    
    /**
     * Return the name of the current file
     * @return string Real name of the current file
     */
    public function getName() {
        return $this->filesList[$this->position]->getName();
    }
    
    /**
     * Return the type of the current file
     * @return string Type of the current file
     */
    public function getType() {
        return $this->filesList[$this->position]->getType();
    }
    
    /**
     * Return the tmp target of the current file
     * @return string Tmp target of the file
     */
    public function getTmpName() {
        return $this->filesList[$this->position]->getTmpName();
    }
    
    /**
     * Return error during transfert
     * @return int Error 
     */
    public function getError() {
        return $this->filesList[$this->position]->getError();
    }

    public function count() {
        return count($this->filesList);
    }
}

?>
