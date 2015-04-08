<?php

/**
 * Account management
 *
 * @author Gilles Hemmerlé
 */
class ModulesController extends IgestisController {

    /**
     * Get a form to edit or validate it if the form is received
     */
    public function indexAction() {
        
        $modulesList = IgestisModulesList::getInstance();
        $this->context->render("pages/modulesList.twig", array(
            'modules_list' => $modulesList->get())
        );
    }
    
}
