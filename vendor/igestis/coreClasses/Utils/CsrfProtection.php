<?php

namespace Igestis\Utils;
/**
 * Description of CsrfProtection
 *
 * @author Gilles Hemmerlé
 */
class CsrfProtection {
    
    public static function generateToken($tokenName) {
        if($tokenName == "")  throw new Exception("The token name cannot be null");
        
        $_SESSION['CSRF_TOKEN'][$tokenName] = md5(microtime());
    }
    
    public static function getTokenValue($tokenName) {
        if(isset($_SESSION['CSRF_TOKEN'][$tokenName])) {
            return $_SESSION['CSRF_TOKEN'][$tokenName];
        }
        
        return null;
    }
}