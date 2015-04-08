<?php

/**
 * This is the controller object that 
 *
 * @author Gilles Hemmerlé
 */
class IgestisController {
    
    /**
     * @var application Object that was created by new application();
     */
    var $context;
    
    /**
     * @var IgestisFormRequest Request sent by the user 
     */
    var $request;
    
    /**
     * @var \Doctrine\ORM\EntityManager Helper for the entitymanager to access to the doctrine entities
     */
    public $_em;
    
    public function __construct($context) {
        $this->context  = $context;
        $this->request = IgestisFormRequest::InitRequest();
        $this->_em = $this->context->entityManager;
    }
    
    protected function redirect($url) {
        header("location:" . $url);
        exit;
    }
}
