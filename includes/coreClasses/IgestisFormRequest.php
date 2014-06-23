<?php

/**
 * Description of IgestisFormRequest
 *
 * @author Gilles Hemmerlé
 */
class IgestisFormRequest {
    private $isPost = false;
    private $isGet = false;
    private $post = array();
    private $get = array();
    
    
    /**
     * Init the IgestisFormRequest with only post form management
     * @return self
     */
    public static function InitPostRequest() {
        if(isset ($_POST) && count($_POST) > 0) {
            return new self(true, false);
        }
        return null;
    }
    
    /**
     *Init the IgestisFormRequest with only get form management
     * @return self 
     */
    public static function InitGetRequest() {
        if(isset ($_GET) && count($_GET) > 0) {
            return new self(true, false);
        }
        return null;
    }
    
    /**
     * Init the IgestisFormRequest with full form management
     * @return self 
     */
    public static function InitRequest() {
        $isGet = $isPost = false;
         if(isset ($_POST) && count($_POST) > 0) {
            $isPost = true;
        }
        if(isset ($_GET) && count($_GET) > 0) {
            $isGet = true;
        }
        
        return new self($isPost, $isGet);
    }
    
    /**
     * Constructor 
     * 
     * @param boolean $isPost Determine if there is a post request
     * @param boolean $isGet Determine if there is a get request
     */
    public function __construct($isPost, $isGet) {
        if($isPost) {
            $this->isPost = $isPost;
            $this->post = $_POST;
        }
        
        if($isGet) {
            $this->isGet = $isGet;
            $this->get = $_GET;
        }
        
    }
    
    /**
     * Return if there is an actual get request
     * @return boolean Is the request a get request ?
     */
    public function isGet() {
        return $this->isGet;
    }
    
    /**
     * Return if there is an actual post request
     * @return boolean Is the request a post request ?
     */
    public function IsPost() {
        return $this->isPost;
    }
    
    /**
     * Return a $_GET value
     * @param String $key Key of the $_GET array
     * @return String Value of the $_GET parameter
     */
    public function getGEt($key) {
        return (isset($this->get[$key]) ? $this->get[$key] : "");
    }
    
    /**
     * Return a $_POST value
     * @param String $key Key of the $_POST array
     * @return String Value of the $_POST parameter
     */
    public function getPost($key) {
        return (isset($this->post[$key]) ? $this->post[$key] : "");
    }
    
    /**
     * Search the _POST and _GET var and return the value assigned to the passed key
     * @param string $key
     * @return mixed The value if found, null else
     */
    public static function getFromPostOrGet($key) {
        if(isset($_POST[$key])) return $_POST[$key];
        if(isset($_GET[$key])) return $_GET[$key];
        return null;
    }
    
    /**
     * Return all the files uploaded from the passed file field
     * @param string $fileId Name of the file field
     * @return \Igestis\Forms\UploadedFiles|null
     */
    public function getUploadedFiles($fileId) {
        $filesCollection = new Igestis\Forms\UploadedFiles();
        if(isset($_FILES[$fileId])) {            
            $filesCollection->collect($_FILES[$fileId]);

        }        
        return $filesCollection;
    }
}