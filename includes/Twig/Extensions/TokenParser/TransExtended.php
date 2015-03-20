<?php


/**
 *
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Twig_Extensions_TokenParser_TransExtended extends Twig_Extensions_TokenParser_Trans
{    
    public function parse(Twig_Token $token)
    {

        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $count = null;
        $plural = null;
        $body = null;
        $vars = new \Twig_Node_Expression_Array(array(), $lineno);
        
        $domainString = self::getTextDomainString();
        $domain = new \Twig_Node_Expression_Constant($domainString, $lineno);
        if ($stream->test('from')) {
            // {% trans from "messages" %}
            $stream->next();            
            
            $domainName = $this->parser->getExpressionParser()->parseExpression();
            $domainTag = $domainName->getAttribute("value");
            $domainString = self::getTextDomainString($domainTag);
            $domain = new \Twig_Node_Expression_Constant($domainString, $lineno);
            
        }

        if (!$stream->test(Twig_Token::BLOCK_END_TYPE)) {
            
            $body = $this->parser->getExpressionParser()->parseExpression(); 
            if($body->hasAttribute('data')) {
                $originalText = $body->getAttribute('data');
                if($originalText == dgettext($domainString, $originalText)) {
                    $domainString = self::getTextDomainString();
                    $domain = new \Twig_Node_Expression_Constant($domainString, $lineno);
                }
            }
            $test = $body->getAttribute("name");
        } else {
            $stream->expect(Twig_Token::BLOCK_END_TYPE);
            $body = $this->parser->subparse(array($this, 'decideForFork'));
            if($body->hasAttribute('data')) {
                $originalText = $body->getAttribute('data');
                if($originalText == dgettext($domainString, $originalText)) {
                    $domainString = self::getTextDomainString();
                    $domain = new \Twig_Node_Expression_Constant($domainString, $lineno);
                }
            }
            if ('plural' === $stream->next()->getValue()) {
                $count = $this->parser->getExpressionParser()->parseExpression();
                $stream->expect(Twig_Token::BLOCK_END_TYPE);
                $plural = $this->parser->subparse(array($this, 'decideForEnd'), true);
            }
        }     
        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $this->checkTransString($body, $lineno);
        return new Twig_Extensions_Node_TransExtended($body, $plural, $count, $lineno, $this->getTag(), $domain);
        //return new Twig_Extensions_Node_TransExtended($body, $domain, null, $vars, $lineno, $this->getTag());
    }
    
    /**
     * Return the textDomain name from the texdomain tag
     * @param string $domainTag Domain tag cab be Module:this, Module:core, Module:module_name or directly a full textdomain name
     * @return string Real textDomain
     */
    public static function getTextDomainString($domainTag="Module:this") {
        $matches = null;
        if(!trim($domainTag)) $domainTag = "Module:this";
        if(preg_match("/Module\:(.*)/i", $domainTag, $matches)) {
            switch(strtolower($matches[1])) {
                case "core" :
                    // Core  textdomain
                    $domain = \ConfigIgestisGlobalVars::textDomain();
                    break;
                case "this":
                    // Current module textdomain
                    $activeRoute = IgestisParseRequest::getActiveRoute();
                    $matches[1] = $activeRoute['Module'];  
                    if(strtolower($activeRoute['Module']) == "core") {
                        $domain = \ConfigIgestisGlobalVars::textDomain();
                        break;
                    }
                default :
                    // Other specified module
                    $configClass = "\\Igestis\\Modules\\" . $matches[1] . "\\ConfigModuleVars";
                    if(class_exists($configClass)) {
                        $domain = (method_exists($configClass, "textDomain") ? $configClass::textDomain() : $configClass::textDomain);
                    }
                    break;
            }
        }
        else {
            $domain = $domainTag;
        }
        return $domain;
    }

    public function decideTransFork($token)
    {
        return $token->test(array('endtrans'));
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'trans';
    }
}
