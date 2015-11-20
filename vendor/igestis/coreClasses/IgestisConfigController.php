<?php

/**
 * Description of IgestisConfigController
 *
 * @author Gilles Hemmerlé
 */
class IgestisConfigController {
    protected static $routes = array();
    
     public static function createUrl($controller_id, $params=null) {
        $url = "";
        $controllers_list = ConfigControllers::get();
        foreach($controllers_list as $controller) {
            if($controller['id'] == $controller_id) {
                $url = "";
                if(is_array($controller['Parameters'])) {
                    if(ConfigIgestisGlobalVars::autoCsrfProtection() && isset($controller['CsrfProtection']) && $controller['CsrfProtection'] == true) {
                        \Igestis\Utils\CsrfProtection::generateToken($controller['id']);
                        $url .= "&CsrfProtection=" . \Igestis\Utils\CsrfProtection::getTokenValue($controller['id']);
                    }
                    
                    while(list($key, $value) = each($controller['Parameters'])) {
                        if(substr($value, 0, 5) == "{VAR}" && is_array($params) && isset($params[$key])) {
                            $value = $params[$key];
                        }
                        if(substr($value, 0, 5) == "{VAR}") {
                            $value = "{" . $key . "}";
                            //$value = "";
                        }
                        $url .= "&$key=$value";
                    }
                    $url = "?" . substr($url, 1);
                }
                $url = $_SERVER['SCRIPT_NAME'] . $url;
            }
        }
        
        return $url;
    }    
    
    /**
     * Return the request route
     * @param string $controllerId Id of the route
     * @return mixed The route if found or null
     */
    public static function getRoute($controllerId) {
        reset(self::$routes);
        foreach (self::$routes as $route) {
            if($route['id'] == $controllerId) return $route;
        }
        return null;
    }
    
    /**
     * Add a parameter and its value to all entities of an array
     * @param array $array A mutlidimentionnal array
     * @param array $pear An array represents a key/value pear
     * @throws Exception
     */
    protected static function addParam(&$array, $pear) {
        if(!is_array($pear))  throw new Exception("The pear parameter must be an array");        
        list($pearKey, $pearValue) = each($pear);
        if(is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key][$pearKey] = $pearValue;
            }
        }
    }

}