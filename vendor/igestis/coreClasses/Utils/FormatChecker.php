<?php

namespace Igestis\Utils;

/**
 * Misc format checker
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */
class FormatChecker
{
    /**
     * Check if the passed string is a properly formatted email address
     * @param $emailAddress string email address
     * @return boolean True if well formed, false else
     */
    public static function isEmail($emailAddress)
    {
        return (bool)filter_var($emailAddress, FILTER_VALIDATE_EMAIL);
    }
}
