<?php
/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Extensions_Extension_I18nExtended extends Twig_Extensions_Extension_I18n
{
    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array(new Twig_Extensions_TokenParser_TransExtended());
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            'trans' => new Twig_Filter_Method($this, 'dgettext'),
        );
    }
    
    public function dgettext($text="", $vars=null, $domainTag="") {
        $translatedText = dgettext(Twig_Extensions_TokenParser_TransExtended::getTextDomainString($domainTag), $text);;
        if($translatedText == $text) $translatedText = dgettext(Twig_Extensions_TokenParser_TransExtended::getTextDomainString(), $text);
        return $translatedText;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'i18n';
    }
}
