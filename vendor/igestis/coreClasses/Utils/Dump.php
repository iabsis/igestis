<?php

namespace Igestis\Utils;
/**
 * This class allow to get or print a var_dump with a cool syntaxic coloration. It uses the Doctrine dumping classe to get the var_dump format.
 * It just add the coloration and allow to get or print the result
 *
 * @author Gilles Hemmerlé (iabsis) <giloux@gmail.com>
 * @package igestis
 */
class Dump {
    /**
     * Print the dump with <pre> and syntaxic coloration
     * @param mixed $var Variable to dump
     * @param int $maxDepth Max depth to dump
     */
    public static function show($var, $maxDepth = 2) {
        ob_start();
        \Doctrine\Common\Util\Debug::dump($var, $maxDepth, false);
        $dump = ob_get_contents();
        ob_end_clean();    
        
        echo self::colorize($dump);
    }
    
    /**
     * Return a string with the dump with <pre> and syntaxic coloration
     * @param mixed $var Variable to dump
     * @param int $maxDepth Max depth to dump
     * @return string The dump content with syntaxic coloration
     */
    public static function get($var, $maxDepth = 2) {
        ob_start();
        \Doctrine\Common\Util\Debug::dump($var, $maxDepth, false);
        $dump = ob_get_contents();
        ob_end_clean(); 
        
        return self::colorize($dump);
    }
    
    /**
     * Transform the var_dump with a cool syntaxic coloration to show on the web pages
     * @param string Result of a var_dump
     * @return string Colored var_dump string
     */
    private static function colorize($dump) {        
        if(preg_match("/^<pre/", $dump)) {
            return $dump;
        }
        // Replace the [""] by simply ''
        $dump = preg_replace("#\[\"(.*)\"]#", "'$1' ", $dump);
        // Replace the line break after => by only a space
        $dump = preg_replace("#=>(\\r\\n|\\r|\\n)[ ]*#", "<font color=\"#888a85\">=></font> ", $dump);
        // Replace the string(xx) lines by a well formed new syntax
        $dump = preg_replace("#(string)\(([0-9]+)\) \"(.*)\"#", "<small>$1</small> <font color=\"#cc0000\">'$3'</font> <i>(length=$2)</i>", $dump);
        // Replace array display format by a new well formed one
        $dump = preg_replace("#(array)\(([0-9]+)\)#", "<b>$1</b> <i>(size=$2)</i>", $dump);        
        // Replace boolean style
        $dump = preg_replace("#(bool)\((true|false)\)#", "<small>boolean</small> <font color=\"#75507b\">$2</font>", $dump);        
        // Manage the null value
        $dump = preg_replace("#(</font>) (NULL)#", "$1 <font color=\"#3465a4\">null</font>", $dump);        
         // Replace int style
        $dump = preg_replace("#(int)\(([0-9]+)\)#", "<small>int</small> <font color=\"#4e9a06\">$2</font>", $dump);        
         // Replace float style
        $dump = preg_replace("#(float)\(([0-9\.]+)\)#", "<small>float</small> <font color=\"#f57900\">$2</font>", $dump);        
        // Replace object keyword with bold
        $dump = preg_replace("#(object)(\(stdClass\))#", "<b>$1</b>$2", $dump);        
        
        return "<pre>".$dump."</pre>";
    }
}