<?php
namespace Igestis\Utils;

/**
 * Description of Encryption
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */
class Encryption {

    /**
     * Encrypt the string
     * @param string $string String to crypt
     * @return string Encrypted string
     */
    public static function encryptString($string) {
        $td = MCRYPT_RIJNDAEL_128; // Encryption cipher (http://www.ciphersbyritter.com/glossary.htm#Cipher)
        $iv_size = mcrypt_get_iv_size($td, MCRYPT_MODE_ECB); // Dependant on cipher/mode combination (http://www.php.net/manual/en/function.mcrypt-get-iv-size.php)

        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); // Creates an IV (http://www.ciphersbyritter.com/glossary.htm#IV)

        $encrypted_string = mcrypt_encrypt($td, md5(\ConfigIgestisGlobalVars::encryptKey()), $string, MCRYPT_MODE_ECB, $iv);
        //var_dump(mcrypt_encrypt($td, \ConfigIgestisGlobalVars::encryptKey(), $string, MCRYPT_MODE_ECB, $iv)); exit;
        $return = "";
        for ($i = 0; $i < strlen($encrypted_string); $i++) {
            $hex = dechex(ord(substr($encrypted_string, $i, 1)));
            while (strlen($hex) < 2)
                $hex = "0" . $hex;
            $return .= $hex;
        }

        return $return;
    }

    public static function DecryptString($string, $autoUnhex = true) {
        // Montage du dossier perso
        $td = MCRYPT_RIJNDAEL_128; // Encryption cipher (http://www.ciphersbyritter.com/glossary.htm#Cipher)
        $iv_size = mcrypt_get_iv_size($td, MCRYPT_MODE_ECB); // Dependant on cipher/mode combination (http://www.php.net/manual/en/function.mcrypt-get-iv-size.php)
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); // Creates an IV (http://www.ciphersbyritter.com/glossary.htm#IV)
        if($autoUnhex) {
            $string = self::unhex($string);
        }
        return trim(mcrypt_decrypt($td, md5(\ConfigIgestisGlobalVars::encryptKey()), $string, MCRYPT_MODE_ECB, $iv));
    }

    public static function unhex($string) {
        $crypted_datas = "";
        // Les données sont en hexa, on la transforme en chaine de caractere
        for ($i = 0; $i < strlen($string); $i+=2) {
            $crypted_datas .= ( chr(hexdec(substr($string, $i, 2))));
        }
        return $crypted_datas;
    }

}