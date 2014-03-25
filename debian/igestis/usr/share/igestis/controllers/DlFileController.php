<?php

/**
 * Description of DlFileController
 *
 * @author Gilles Hemmerlé
 */
class DlFileController extends IgestisController {
    
    public function downloadAction($Type, $Extra) {
        switch($Type) {
            case "company_logo" :
                // Check rights
                if(!in_array($this->context->security->module_access("CORE"), array("ADMIN", "TECH"))) {
                    $this->context->throw403error();
                }
                // Search company
                $company = $this->context->entityManager->getRepository("CoreCompanies")->find($Extra);
                // Check if exists
                if($company == null) {
                    $this->context->throw404error();
                }
                
                // Get complete file target
                $fileName = $company->getLogoFolder() . "/" . $company->getLogoFileName();
                
                if(!is_file($fileName)) {
                    $this->context->throw404error();
                }
                
                
                $this->context->renderFile($fileName);
                break;
                
            default:
                $hook = Igestis\Utils\Hook::getInstance();
                $hookParameters = new \Igestis\Types\HookParameters();
                $hookParameters->set("Type", $Type);
                $hookParameters->set('Extra', $Extra);
                $hookCatched = $hook->callHook("dlFile", $hookParameters);
                if(!$hookCatched) {
                    $this->context->throw404error();
                }
        }
    }
}
