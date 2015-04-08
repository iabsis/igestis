<?php
namespace Igestis\Forms;

/**
 * Description of UniqueFile
 *
 * @author Gilles Hemmerlé
 */
class UniqueFile {
    private $fileInfos = array();
    
    public function __construct() {
    }
    
    public function set($key, $value) {
        $this->fileInfos[$key] = $value;
    }
    
    public function get($key) {
        return isset($this->fileInfos[$key]) ?  $this->fileInfos[$key] :  null;
    }
    
    public function getName() {
        return $this->get("name");
    }
    
    public function getType() {
        return $this->get("type");
    }
    
    public function getTmpName() {
        return $this->get("tmp_name");
    }
    
    public function getError() {
        return $this->get("error");
    }
    
    public function getSize() {
        return $this->get("size");
    }
    
     public function setName($value) {
        $this->set("name", $value);
        return $this;
    }
    
    public function setType($value) {
        $this->set("type", $value);
        return $this;
    }
    
    public function setTmpName($value) {
        $this->set("tmp_name", $value);
        return $this;
    }
    
    public function setError($value) {
        $this->set("error", $value);
        return $this;
    }
    
    public function setSize($value) {
        $this->set("size", $value);
        return $this;
    }
    
    public function getExtension() {
        $found = null;
        preg_match("/\.[A-Za-z0-9]+$/", $this->getName(), $found);
        return $found[0];
    }
    
    
}

?>
