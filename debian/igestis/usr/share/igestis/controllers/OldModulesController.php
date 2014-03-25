<?php

/**
 * Description of OldModulesController
 *
 * @author Gilles Hemmerlé
 */
class OldModulesController extends \IgestisController {
    
    public function indexAction($moduleName) {
        
        $html =  $this->context->get_html_content("gestion_old_module.htm");
        
        $this->context->show_content($html);
    }
}
