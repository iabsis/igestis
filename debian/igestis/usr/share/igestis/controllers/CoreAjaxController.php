<?php

/**
 * home page presentation
 *
 * @author Gilles Hemmerlé (iabsis) <giloux@gmail.com>
 */

class CoreAjaxController extends IgestisAjaxController {
    /**
     * Verify if the passed login is already in use
     * @param type $Login Login to verify
     */
    public function LoginExists($Login="") {
        $person = $this->context->entityManager->getRepository("CoreContacts")->getUserFromLogin($Login);
         if($person == null) {
             $this->AjaxRender(array("exists" =>false));
         }
         else {
             $this->AjaxRender(array("exists" =>true));
         }
    }
    
    /**
     * Update the user datas on the database to disable the quicktour bar
     */
    public function hideQuicktour() {
        $this->context->security->contact->hideQuicktour();
        
        // Apply changes and return a return the table of contacts
        try {
            $this->context->entityManager->persist($this->context->security->contact);
            $this->context->entityManager->flush();

            $this->AjaxRender();
        }
        catch (Exception $e) {
            $this->AjaxRender(array("error" => $e->getMessage()));
        }
    }

    
    public function scannersList() {
        $ajaxResponse = new Igestis\Ajax\AjaxResult();
        try {
          $scannersList = \Igestis\Utils\Scanner::scannersList();
        }
        catch (Exception $e) {
          $ajaxResponse->setError($e->getMessage());
        }
        if(count($scannersList) == 0) {
            $ajaxResponse->setError(Igestis\I18n\Translate::_("No scanner found"));            
        }
        else {
            $ajaxResponse->setSuccessful($scannersList);
            $ajaxResponse->render();
        }
    }
    
    public function uploadProgress() {
        
    }
}
