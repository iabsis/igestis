<?php

namespace Igestis\Ldap;

/**
* Error thrown when failed to bind
*/
class BindException extends \Exception
{
    public function __construct($message = null, $code = 0, \Exception $previous = null) {

        if (!$message) {
            $message = "Unable to bind to the serveur";
        }

        // assurez-vous que tout a été assigné proprement
        parent::__construct($message, $code, $previous);
    }
}