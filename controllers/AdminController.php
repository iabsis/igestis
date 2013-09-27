<?php

/**
 * Description of AdminController
 *
 * @author Gilles Hemmerlé
 */
class AdminController extends IgestisController {
    public function generalAction() {
        
        //$companyId = $this->context->security->user->getCompany()->getId();            
        //$webRootFolder = ConfigIgestisGlobalVars::SERVER_FOLDER . "/" . ConfigIgestisGlobalVars::APPLI_FOLDER . "/web";
        //$cssFolder = $webRootFolder . "/" . $companyId;
        //$cssFile = $cssFolder . "/style.css";
        
        if(!$this->request->IsPost()) {  
            /*
            if(!is_dir($webRootFolder)) {
                if(!mkdir($webRootFolder)) {
                    new wizz(sprintf(_("Failed to create '%s' folder."), $webRootFolder), wizz::$WIZZ_ERROR);
                }
            }
            elseif(!is_dir($cssFolder)) {
                if(!mkdir($webRootFolder)) {
                    new wizz(sprintf(_("Failed to create '%s' folder."), $cssFolder), wizz::$WIZZ_ERROR);
                }
            }           
            elseif(!is_readable($cssFolder)) {
                new wizz(sprintf(_("'%s' folder is not readable."), $cssFolder), wizz::$WIZZ_ERROR);
            }
            elseif(!is_file($cssFile) && !is_writable($cssFolder)) {
                 new wizz(sprintf(_("Igestis won't be able to write '%s' file. Its parent folder is not writable, custom stylesheet won't be saved."), $cssFile), wizz::$WIZZ_ERROR);
            }
            elseif(is_file($cssFile) && !is_readable($cssFile)) {
                new wizz(sprintf(_("'%s' folder is not readable. The custom stylesheet has not been retrieved"), $cssFolder), wizz::$WIZZ_ERROR);
            }*/
        }
        else {            
            if($this->request->getPost("clearTemplatesCache") == 1) {
                // Suppression du cache des fichiers twig
                $this->context->getTwigEnvironnement()->clearCacheFiles();
                $this->context->getTwigEnvironnement()->clearCacheFiles();
                new wizz(_("The twig cache has correctly been cleared."), wizz::$WIZZ_SUCCESS);
            }
            
            if($this->request->getPost("regenLocales") == 1) {
                // Suppresion du cache des langues
                $getTextCaching = new \Igestis\Utils\GetTextCaching();
                $getTextCaching->setCachingForAll();
                new wizz(_("The lang cache has correctly been reloaded."), wizz::$WIZZ_SUCCESS);
            }
            /*
            try {
                file_put_contents($cssFile, $this->request->getPost('cssContent'));
                new wizz(_("Custom css successfuly saved."), wizz::$WIZZ_SUCCESS);
            }
            catch (\Exception $e) {
                \IgestisErrors::createWizz($e, \IgestisErrors::TYPE_ANY, _("Unable to save the css file."));
            }*/
        }
        
        $this->context->render("pages/generalAdmin.twig", array(
            "cssContent" => is_file($cssFile) ? file_get_contents($cssFile) : ''
        ));
        
    }
}

?>
