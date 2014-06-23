<?php
namespace Igestis\Types;

/**
 * Description of SidebarModalLink
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */
class SidebarModalLink extends SidebarComplexItem {
    
    /**
     * Id of the modal to call
     * @var string 
     */
    private $modalId;
    
    /**
     * Content to send to the global twig renderer
     * @var array 
     */
    private $twigContent;
    
    public function __construct($modalId, $twigContent) {
        $this->modalId = $modalId;
        $this->twigContent = $twigContent;
    }
    
    public function getTemplateFile() {
        return "fields/sidebarCustomLink.twig";
    }
    
    public function getTemplateReplacements() {
        return array(
            "id" => $this->modalId
        );
    }

    public function getType() {
        return "ModalLink";
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
