<?php
$old_error_handler = set_error_handler("iGestisErrorHandler");
$_SESSION['IGESTIS_ERRORS'] = array();

function iGestisErrorHandler($errno, $errstr, $errfile, $errline)
{
    if(\ConfigIgestisGlobalVars::debugMode() == true) {
        \Igestis\Utils\Debug::FileLogger("[$errno] $errstr - Erreur fatale sur la ligne $errline dans le fichier $errfile");
        $debugger = Igestis\Utils\Debug::getInstance();
        $debugger->addError($errno, $errstr, $errfile, $errline);        
        return true;
        //return false;
    }
    else {
        error_reporting (0);
    }
    global $application;
    global $objResponse;
    $show_popup_error = true;
    if (error_reporting() == 0) $show_popup_error = false;

    $_SESSION['IGESTIS_ERRORS'][] = array("message" => $errstr, "file" => $errfile, "line" => $errline);
    
    switch ($errno) {
    case E_USER_ERROR:
        $message =  "Une erreur interne s'est produite lors de l'execution de la page. Détails de l'erreur à transmettre à l'administrateur :<br />\n" .
                    "[$errno] $errstr<br />\n" .
                    "  Erreur fatale sur la ligne $errline dans le fichier $errfile" .
                    ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";

        \Igestis\Utils\Debug::FileLogger($message);
        if($application instanceof application) {
            if($objResponse instanceof objResponse) {                
                $application->message_die($message);
            }
            else {
                if($show_popup_error) new wizz(str_replace("<br />", "", $message), WIZZ_ERROR, $objResponse);
                return true;
            }
        }
        exit(1);
        break;
    case E_NOTICE:
        return true;
        break;
    case E_USER_WARNING:
    case E_WARNING :
        if($show_popup_error) new wizz("Attention : [$errno] $errstr <br /><br /> $errfile (ligne $errline)<br />\n");
        return true;
        break;
    default:    
        break;
    }
    \Igestis\Utils\Debug::FileLogger("[$errno] $errstr - Erreur fatale sur la ligne $errline dans le fichier $errfile");
    /* Ne pas exécuter le gestionnaire interne de PHP */
    return true;
}


class igestis_error {
	
	private $error_message;
	
	function __construct($err_msg) {
		$this->error_message = $err_msg;
	}
	
	function get_message() {
		return $this->error_message;
	}
}

