<?php


/**
 * Description of IgestisMenu
 *
 * @author Gilles Hemmerlé
 */
class IgestisMenu {
    
    /**
     *
     * @var Array Content of the menu to pass to Twig
     */
    private $menu_array = null;
    /**
     *
     * @var Application $context
     */
    private $context = null;
    
    public function __construct(&$context) {
        $this->context = $context;
    }
    
    /**
     * Add an item to the general menu
     * 
     * @param String $place_in Where to place this option ( $place_in = "root" to add it to the root menu)
     * @param String $name Name to show in the menu
     * @param String $url Url to get when click on the menu
     * @param Boolean $is_active Tell if the menu has to be overlined
     * @return boolean Yes if added, No else 
     */
    public function addItem($place_in, $name, $urlOrRouteId, $is_active=false) {
        $route = IgestisConfigController::getRoute($urlOrRouteId);
        if($route) {
            $url  = IgestisConfigController::createUrl($urlOrRouteId);
            if(!$this->context->security->hasAccess($route)) return false;
        }
        else {
            $url = $urlOrRouteId;
        }

        if(trim($place_in) == "") return false;
        if(strtolower($place_in) == "root") {
            // Add the menu option to the root menu
            $this->menu_array[] = array(
                "name" => $name,
                "url" => $url,
                "active" => ($_SERVER['REQUEST_URI'] == $url || $is_active)
            );
        }
        else {
            // Add the menu option to the submenu
            $array_id = "";
            if(is_array($this->menu_array)) {
                @reset($this->menu_array);
                while(list($key, $value) = each($this->menu_array)) {
                    if($this->menu_array[$key]["name"] == $place_in) {
                        $array_id = $key;
                        break;
                    }
                }
            }
            
            if($array_id === "") {
                $this->addItem("ROOT", $place_in, NULL);
                $array_id = count($this->menu_array) - 1;
            }
            
            if($_SERVER['REQUEST_URI'] == preg_replace("#http[s]?://.*/#Ui", "/", $url)) {
                $is_active = true;
                $this->menu_array[$array_id]["active"] = true;
            }
            
            $this->menu_array[$array_id]['submenu'][] = array(
                "name" => $name,
                "url" => $url,
                "active" => $is_active
            );
        }
        
        return true;
    }
    
    public function get_array() {
        return $this->menu_array;
    }
    
}