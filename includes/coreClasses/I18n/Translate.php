<?php

namespace Igestis\I18n;
/**
 * This class define all the methods needed to convert sentences.
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */
class Translate {
    /**
     * Return a translated text from the passed text domain. If no text domain passed, it search in the current module text domain,
     * If translation not found in the module text domain, it search in the core text domain.
     * 
     * @example 
     * <pre>
     * // Current module text domain used, and core text domain if not found in the module one
     * \Igestis\I18n\Translate::_("This text has to be translated");
     * 
     * // Use only the definded text domain
     * \Igestis\I18n\Translate::_("This text has to be translated", "YourDomain");
     * 
     * @param type $text
     * @param type $textDomain
     * @return type
     */
    public static function _($text, $textDomain = "") {
        if($textDomain) {
            return dgettext($textDomain, $text);
        }
        else {
            $activeRoute = \IgestisParseRequest::getActiveRoute();
            if($activeRoute['Module'] != "core") {
                $configClass = "\\Igestis\\Modules\\" . $activeRoute['Module'] . "\\ConfigModuleVars";
                if(class_exists($configClass) && defined("$configClass::textDomain")) {
                    $translatedText = dgettext($configClass::textDomain, $text);
                    if($translatedText != $text) return $translatedText;
                }
            }
            
            return dgettext(\ConfigIgestisGlobalVars::textDomain(), $text);
        }
    }

}

?>
