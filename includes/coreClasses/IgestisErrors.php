<?php

/**
 * @Author : Gilles Hemmerlé <giloux@gmail.com>
 */

class IgestisErrors
{
    const TYPE_MYSQL = "mysql";
    const TYPE_ANY = "any";
    
    /**
     * Create a wizz corresponding on the error passed
     * @param Exception $e
     * @param string $type Error type
     */
    public static function createWizz(Exception $e, $type=self::TYPE_MYSQL, $debugOffMessage ='An error has occured') {
        
        if(\ConfigIgestisGlobalVars::DEBUG_MODE == false) {
            new wizz($debugOffMessage, wizz::$WIZZ_ERROR);
            return;
        }
        
        switch ($type) {
            case self::TYPE_MYSQL :
                switch($e->getCode()) {
                    case "23000" :  new wizz(_("Unable to delete this row. It has some associated datas on the application"), WIZZ_ERROR); break;
                    default : new wizz($e->getMessage(), WIZZ_ERROR); break;
                }
                break;
            
            case self::TYPE_ANY :
                new wizz(nl2br($e->getMessage() . "\n" . $e->getTraceAsString()));
                break;
        }

    }

    /**
     * Show a beautifull error message
     * @static
     * @param String $errorMessage The message to show
     */
    public static function throwError($errorMessage) {
        die($errorMessage);
    }
}
