<?php

/**
 * This is the controller object that 
 *
 * @author Gilles Hemmerlé
 */
class IgestisAjaxController extends IgestisController {
    /**
     * Return the json encoded datas to the ajax script
     * @param Array|AjaxResult $values Values to return to the ajax script
     */
    public function AjaxRender($values) {
        
        if($values instanceof Igestis\Ajax\AjaxResult) {
            $values->render();
        }
        if(!isset($values['error'])) $values['error'] = false;
        if(!isset($values['successful'])) $values['successful'] = false;
        echo json_encode($values);
        exit;
    }
    
}
