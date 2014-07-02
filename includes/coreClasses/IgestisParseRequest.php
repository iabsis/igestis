<?php

/**
 * Parse Html Requests
 *
 * @author Gilles Hemmerlé
 */
class IgestisParseRequest {
    
    /**
     *
     * @var Array Configuration des routes pour rediriger vers le bon controleur
     */
    var $config;
    /**
     *
     * @var Application Contient l'objet application pour créer les rendus et accéder aux parametres de l'application
     */
    var $context;
    
    /**
     *
     * @param Application $context Contient l'objet application pour créer les rendus et accéder aux parametres de l'application (ie : new applicatino())
     */
    public function __construct(&$context) {
        $this->context = $context;
        $this->LaunchAction();  
    }
    
    
    public static function getActiveRoute() {
        $config = ConfigControllers::get();
        if (is_array($config)) {
            foreach ($config as $route) {
                $route_found = true;
                if (count($route['Parameters']) == 0) {
                    // If a route without parameter has been set on the ConfigControllers and if we want to get it ...
                    if ((!isset($_GET) || count($_GET) == 0)) {
                        return $route;
                    } else {
                        $route_found = false;
                    }
                } else {
                    // For routes with parameters     
                    $var_list = NULL;
                    while (list($key, $value) = each($route['Parameters'])) {
                        if (!isset($_GET) || !isset($_GET[$key])) {
                            $route_found = false;
                            break;
                        }
                        $is_var = false;
                        if (substr($value, 0, 5) == "{VAR}") {
                            $value = substr($value, 5);
                            $is_var = true;
                        }
                        else {
                            if($value != $_GET[$key]) {
                                $route_found = false;
                                break;
                            }
                        }

                        $return = null;
                        if (preg_match('/^' . $value . '$/', $_GET[$key], $return)) {
                            if ($is_var) {
                                $var_list[] = $return[0];
                            }
                        } else {
                            $route_found = false;
                            break;
                        }
                    }
                    if($route_found) {
                        $route['var_list'] = $var_list;
                        return $route;
                    }
                }
            }
        }
        return null;
    }
    
    public static function getActiveRouteUrl() {
        $route = self::getActiveRoute();
        $routeParameters = $route['Parameters'];
        $params = array();
        foreach ($routeParameters as $parameterKey => $parameterValue) {
            $params[$parameterKey] = $_GET[$parameterKey];
        }
        return IgestisConfigController::createUrl($route['id'], $params);
    }
    
    public static function getControllerId() {
        $route = IgestisParseRequest::getActiveRoute();
        return $route['id'];
    }
    
    /**
     * Lance le bon controlleur en fonction de la requête http.
     */
    private function LaunchAction() {
        $route = self::getActiveRoute();
        if($route == null && !preg_match("/module/", $_SERVER['REQUEST_URI'])) {
            /* Allow the url containing the "module" keyword to keep compatibility with old modules */
            IgestisErrors::throwError(_("Route not known for url :" . $_SERVER['REQUEST_URI']));
        }
        // Another hack to permitt the launch old plugin format
        if(!$route) return;
        Igestis\Utils\Debug::addLog("Route found, check now rights to access this route ...");
        // Gestion des droits
        if(!in_array("EVERYONE", $route['Access']) && !$this->context->security->hasAccess($route)) {
            //new wizz(_("You have not the rights to access this page. You can login again with another account below."), wizz::$WIZZ_ERROR);
            $_SESSION['sess_page_redirect'] = $_SERVER['REQUEST_URI'];
            if($this->context->security->isLoged()) {                
                header("HTTP/1.0 403 Not Found"); 
                $controller = new HomePageController($this->context);
                $controller->error403();
                exit;
            }
            else {
                header("location:" . ConfigControllers::createUrl("login_form", array("Force" => 0))); exit;
            }
            
        }

        Igestis\Utils\Debug::addLog("Route selected, rights ok, launch the controller ...");
        
        if ($route) {
            Igestis\Utils\Debug::addDump($route, "Actual route");
            if (!class_exists($route['Controller'])) {
                $this->context->dieMessage("Controller " . $route['Controller'] . " does not exist");
            }            
            

            $controller = new $route['Controller']($this->context);
            
            if(ConfigIgestisGlobalVars::autoCsrfProtection()) {
                if(!empty($route['CsrfProtection']) && $route['CsrfProtection']) {
                    if(\Igestis\Utils\CsrfProtection::getTokenValue($route['id']) != IgestisFormRequest::getFromPostOrGet("CsrfProtection")) {
                        if($_SERVER['HTTP_REFERER']) {
                            new \wizz("The token is invalid", \wizz::$WIZZ_ERROR);
                            header("location:" . $_SERVER['HTTP_REFERER']); exit;
                        }
                        $this->context->dieMessage("The token is invalid");
                    }
                }
            }
            switch (count($route['var_list'])) {
                case 0: $controller->$route['Action']();
                    break;
                case 1: $controller->$route['Action']($route['var_list'][0]);
                    break;
                case 2: $controller->$route['Action']($route['var_list'][0], $route['var_list'][1]);
                    break;
                case 3: $controller->$route['Action']($route['var_list'][0], $route['var_list'][1], $route['var_list'][2]);
                    break;
                case 4: $controller->$route['Action']($route['var_lvist'][0], $route['var_list'][1], $route['var_list'][2]);
                    break;
                case 5: $controller->$route['Action']($route['var_list'][0], $route['var_list'][1], $route['var_list'][2], $route['var_list'][3]);
                    break;
                case 6: $controller->$route['Action']($route['var_list'][0], $route['var_list'][1], $route['var_list'][2], $route['var_list'][3], $route['var_list'][4]);
                    break;
                case 7: $controller->$route['Action']($route['var_list'][0], $route['var_list'][1], $route['var_list'][2], $route['var_list'][3], $route['var_list'][4], $route['var_list'][5]);
                    break;
                default: die("Too many parameters in your url");
            }
            Igestis\Utils\Debug::addLog("GET Request parsed");
            exit;
        }
    }

}

