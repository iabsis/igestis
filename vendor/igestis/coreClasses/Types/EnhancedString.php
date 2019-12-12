<?php

namespace Igestis\Types;
/**
 * Description of String
 *
 * @author Gilles Hemmerlé (iabsis) <giloux@gmail.com>
 */
class EnhancedString {
    /**
     *
     * @var string The calculated string
     */
    private $string;

    /**
     *
     * @param string $string Initialize string
     */
    public function __construct($string) {
        $this->string = $string;
    }

    /**
     * Replace whole string
     * @param string $string Initialize string
     * @return \String
     */
    public function set($string) {
        $this->string = $string;
        return $this;
    }

    /**
     * Concatains the passed string with the sotred one
     * @param type $string
     * @return \String
     */
    public function concatains($string) {
        $this->string .= $string;
        return $this;
    }

    /**
     * Return complete string
     * @return string Complete string
     */
    public function __toString() {
        return $this->string;
    }

    /**
     * Suppress all accents from the string
     * @return \String
     */
    public function stripAccents() {
        $this->string = htmlentities($this->string, ENT_NOQUOTES, 'utf-8');
        $this->string = preg_replace('#\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;#', '\1', $this->string);
        $this->string = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $this->string);
        $this->string = preg_replace('#\&[^;]+\;#', '', $this->string);

        return $this;
    }

    /**
     * This function crop a text and return a text corresponding to $nbcar first caracters. It try to stop after a blank character
     * @param string $string Text to crop
     * @param int $nbcar Number of caractere to return
     * @return string Cropped text with '...' suffix
     */
    public static function cutSentencee($string, $nbcar = 0) {
        if (strlen($string) > $nbcar && (0 != $nbcar)) {
            $wordsList = explode(' ', $string);
            $stringToReturn = '';

            while (list(, $v) = each($wordsList)) {
                if (strlen($stringToReturn) >= $nbcar)
                    break;
                $stringToReturn .= $v . ' ';
            }

            $stringToReturn = substr($stringToReturn, 0, strlen($stringToReturn) - 1);
            if (count($wordsList) > 1)
                $stringToReturn .= '...';
        }
        else
            $stringToReturn = $string;

        return $stringToReturn;
    }

    public static function slug($text)
    {
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text))
        {
        return 'n-a';
        }

        return $text;
    }
}

?>
