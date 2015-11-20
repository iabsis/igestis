<?php

/**
 * @Author : Gilles Hemmerlé <giloux@gmail.com>
 */

class IgestisMisc
{
    static function superRandomize($nbChar) {
        $super_randomize_sting = "";
        srand((float) microtime() * 1000000);

        $arr = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

        for ($i = 0; $i < $nbChar; $i++) {
            $randval = round(rand(1, 2));

            switch ($randval) {
                case '1':
                    $val = round(rand(0, 25));
                    if(round(rand(0, 25) == 0)) {
                        $super_randomize_string .= strtoupper($arr[$val]);
                    }
                    else {
                        $super_randomize_string .= $arr[$val];
                    }

                    break;
                case'2':
                    $val = round(rand(0, 9));
                    $super_randomize_string .= $val;
                    break;
            }
        }

        return $super_randomize_string;
    }

    public static function isEmail($string) {
        return preg_match("/^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$/", $string);
    }
    
    public static function unsetEmptyValues($array) {
        $buffer = array();
        foreach ($array as $row) {
            if ($row != "") $buffer[] = $row;
        }
        return $buffer;
    }
}
