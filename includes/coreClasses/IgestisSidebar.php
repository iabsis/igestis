<?php


/**
 * Description of IgestisSidebar
 *
 * @author Gilles Hemmerlé
 */
class IgestisSidebar {
    
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
     * Add an item to the sidebar
     * 
     * @param String $place_in Where to place this option 
     * @param String $name Name to show in the sidebar
     * @param \Igestis\Types\SidebarComplexItem|string $url Url to get when click on the sidebar
     * @param Boolean $is_active Tell if the menu has to be overlined
     * @return boolean Yes if added, No else 
     */
    public function addItem($place_in, $name, $urlOrRouteId, $is_active=false, $customRights=null) {    
        
        if($customRights) {
            if(!$this->context->security->hasAccess($customRights, false)) return false;
        }
        
        
        $route = IgestisConfigController::getRoute($urlOrRouteId);
        if($route) {
            $url  = IgestisConfigController::createUrl($urlOrRouteId);
            if(!$this->context->security->hasAccess($route)) return false;
        }
        else {
            $url = $urlOrRouteId;
        }
        

        if(trim($place_in) == "") return false;

        // Add the menu option to the submenu
        $array_id = 0;
        $found  = false;
        if(is_array($this->menu_array)) {
            @reset($this->menu_array);
            while(list($key, $value) = each($this->menu_array)) {
                if($this->menu_array[$key]["label"] == $place_in) {
                    $array_id = $key;
                    $found = true;
                    break;
                }
            }
        }

        if(!$found) {
            $this->menu_array[] = array("label" => $place_in);
            $array_id = count($this->menu_array) - 1;
        }
        
        
        if($urlOrRouteId instanceof \Igestis\Types\SidebarComplexItem) {
            
            $replacements = $urlOrRouteId->getTemplateReplacements();
            $replacements['linkName'] = $name;
            $content = $this->context->getTwigEnvironnement()->render($urlOrRouteId->getTemplateFile(), $replacements);
            $this->menu_array[$array_id]['links'][] = array(
                "content" => $content
            );            
        }
        else {
            $this->menu_array[$array_id]['links'][] = array(
                "label" => $name,
                "url" => $url,
                "selected" => ($is_active || $_SERVER['REQUEST_URI'] == preg_replace("#http[s]?://.*/#Ui", "/", $url))
            );
        }
        
        return true;
    }
    
    public function get_array() {
        return $this->menu_array;
    }
    
}