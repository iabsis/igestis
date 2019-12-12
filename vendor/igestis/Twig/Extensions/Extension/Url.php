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
            'url' => new \Twig_SimpleFunction('url', [$this, 'url'], ['is_safe' => ['html']])
        );
    }
 
    public function url($txt="", $params=null)
    {
        return \IgestisConfigController::createUrl($txt, $params);

    }
}