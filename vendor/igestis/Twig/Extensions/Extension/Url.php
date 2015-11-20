<?php
namespace Igestis\Twig\Extensions\Extension;

class Url extends \Twig_Extension {
    public function getName()
    {
        return 'url';
    }
    
    public function getFunctions()
    {
        return array(
            'url' => new \Twig_Function_Method($this, 'url', array('is_safe' => array('html')))
        );
    }
 
    public function url($txt="", $params=null)
    {
        return \IgestisConfigController::createUrl($txt, $params);

    }
}