<?php

namespace Igestis\Ajax;

/**
 * Description of AjaxResult
 *
 * @author Gilles Hemmerlé (iabsis) <giloux@gmail.com>
 */
class AjaxResult {
    private $aValues = array();
    private $hasError = null;
    
    /**
     * Constructor, initialize variables
     */
    public function __construct() {
        $this->aValues['successful'] = true;
        $this->aValues['error'] = false;
        $this->aValues['IgestisAjaxResultScript'] = array();
    }
    
    /**
     * Set a new value to transmit to ajax script
     * @param string $key
     * @param string $value
     * @return \Igestis\Ajax\AjaxResult
     */
    public function setValue($key, $value) {
        if(!trim($key)) {
            $this->renderError(_("A key is required to send a data to ajax script"));            
        }
        if($key == "successful" || $key == "error") {
            $this->renderError(_("successful and error are reserved keywords"));
        }
        $this->aValues[$key] = $value;
        return $this;
    }
    
    /**
     * Return the value of the passed key
     * @param string $key
     * @return string Setted value for the passed key
     */
    public function getValue($key) {
        return isset($this->aValues[$key]) ? $this->aValues[$key] : null;
    }
    
    /**
     * Set the render as error and send the render
     * @param string $errorString Error message to send to ajax script
     */
    public function setError($errorString, $debugInfos=null) {
        
        if(\ConfigIgestisGlobalVars::DEBUG_MODE && $debugInfos) {
            $errorString .= "<br>" . $debugInfos;
        }
        if(!trim($errorString)) {
            $this->renderError(_("Error occured during ajax statement"));
        }
        $this->hasError = $errorString;
        $this->aValues['error'] = $errorString;
        
        $this->render();
    }
    
    /**
     * 
     * @param type $successMsg
     * @return \Igestis\Ajax\AjaxResult
     */
    public function setSuccessful($successMsg) {
        $this->aValues['successful'] = $successMsg;
        return $this;
    }
    
    /**
     * 
     * @param type $url
     * @return \Igestis\Ajax\AjaxResult
     */
    public function setRedirection($url) {
        $this->aValues['redirection'] = $url;
        return $this;
    }
    
    /**
     * Send the json datas to the ajax script
     */
    public function render() {
        header('Content-type: text/plain');
        if($this->hasError) {
            $this->aValues['successful'] = false;
        }
        echo json_encode($this->aValues);
        exit;
    }
    
    /**
     * Generate a custom error and send it to the ajax script
     * @param type $errMsg
     */
    private function renderError($errMsg) {
        $values = array(
            "error" => $errMsg,
            "successful" => false
        );        
        echo json_encode($values);
        exit;
    }
    
    /**
     * Send a js script to the ajax script
     * @param string $script
     * @return self
     */
    public function addScript($script) {
        $this->aValues['IgestisAjaxResultScript'][] = $script;
        return $this;
    }
    
    /**
     * Add a wizz to the ajax response
     * @param type $text
     * @param type $type
     * @param string $target Define a jQuery selector where the wizz has to be shown
     * @return self Full object to chain some methods
     */
    public function addWizz($text, $type, $target = null) {
        $array = array("label" => $text, "type" => $type);
        if($target) $array['target'] = $target;
        
        $this->aValues['IgestisAjaxWizz'][] = $array;    
        
        return$this;
    }
    
    /**
     * 
     * @param string $id Id of the html tag to place content in
     * @param string $value Text to replace html tag content with
     * @return \Igestis\Ajax\AjaxResult Full object to chain some methods
     */
    public function addAssign($id, $value) {
        $this->aValues['IgestisAjaxReplace'][] = array("id" => $id, "value" => $value);
        return $this;
    }
    
}

?>
