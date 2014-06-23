<?php
namespace Igestis\Types;

/**
 * Description of SidebarModalLink
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */
class SidebarJavascriptOnClick extends SidebarComplexItem {
    
    /**
     * Id of the modal to call
     * @var string 
     */
    private $onClick;
    
    /**
     * Content to send to the global twig renderer
     * @var array 
     */
    private $twigContent;
    
    public function __construct($onClick, $twigContent) {
        $this->onClick = $onClick;
        $this->twigContent = $twigContent;
    }
    
    public function getTemplateFile() {
        return "fields/sidebarCustomLink.twig";
    }
    
    public function getTemplateReplacements() {
        return array(
            "onclick" => $this->onClick
        );
    }

    public function getType() {
        return "onClickLink";
    }

    /**
     * The modal links won't never be active links
     * @return boolean
     */
    public function isActive() {
        return false;
    }

    public function getCustomGlobalReplacements() {
        
    }
 
}
