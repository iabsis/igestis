<?php
/**** That's the class that allows igestis to show error / warning / successful message on a little popup *******/

/**
 * @deprecated Use the wizz static variables instead WIZZ::WIZZ_ERROR
 */
define("WIZZ_ERROR", "WIZZ_ERROR");
/**
 * @deprecated Use the wizz static variables instead WIZZ::WIZZ_INFO
 */
define("WIZZ_INFO", "WIZZ_INFO");
/**
 * @deprecated Use the wizz static variables instead WIZZ::WIZZ_SUCCESS
 */
define("WIZZ_SUCCESS", "WIZZ_SUCCESS");
/**
 * @deprecated Use the wizz static variables instead WIZZ::WIZZ_WARNING
 */
define("WIZZ_WARNING", "WIZZ_WARNING");

$_SESSION['WIZZ_ALREADY_SENT'] = NULL;
$_SESSION['WIZZ_AJAX_MODE'] = false;

class wizz {
    /**
     * @var string Show a red alert
     * @deprecated since version 3.0, use the const instead
     */
    public static $WIZZ_ERROR = "WIZZ_ERROR";
    /**
     * @var string Show a blue alert
     * @deprecated since version 3.0, use the const instead
     */
    public static $WIZZ_INFO = "WIZZ_INFO";
    /**
     * @var string Show a green alert
     * @deprecated since version 3.0, use the const instead
     */
    public static $WIZZ_SUCCESS = "WIZZ_SUCCESS";
    /**
     * @var string Show a yellow alert
     * @deprecated since version 3.0, use the const instead
     */
    public static $WIZZ_WARNING = "WIZZ_WARNING";
    
    /**
     * @var string Show a red alert
     */
    const WIZZ_ERROR = "WIZZ_ERROR";
    /**
     * @var string Show a blue alert
     */
    const WIZZ_INFO = "WIZZ_INFO";
    /**
     * @var string Show a green alert
     */
    const WIZZ_SUCCESS = "WIZZ_SUCCESS";
    /**
     * @var string Show a yellow alert
     */
    const WIZZ_WARNING = "WIZZ_WARNING";


    /**
     * @param string $message Text to show
     * @param string $type SELF::WIZZ_ERROR | SELF::WIZZ_INFO | SELF::WIZZ_SUCCESS | SELF::WIZZ_WARNING
     */
    public function __construct($message, $type=WIZZ_ERROR, $time=0) {
        // Constructor

        $message = str_replace("\n", "", $message);
        $message = str_replace("\r", "", $message);

        foreach ($_SESSION['WIZZ_MESSAGE'] as $currentMessage) {
            if ($currentMessage['message'] == $message) {
                return;
            }
        }

        $_SESSION['WIZZ_ALREADY_SENT'][$type] = true;
        $_SESSION['WIZZ_MESSAGE'][] = array("message" => $message, "type" => $type, "time" => $time);
    }

    /**
     * Add the wizz to the old template system
     * @global application $application
     */
    public static function show_messages() {
        // Affiche les messages
        global $application;

        if(is_array($_SESSION['WIZZ_MESSAGE'])) {
            while(list($key, $value) = each($_SESSION['WIZZ_MESSAGE'])) {
                global $application;
                if($application instanceof application) {
                    $application->add_block(
                            "WIZZ_MESSAGE",
                            array(
                                "message" => str_replace("'", "\\'", $_SESSION['WIZZ_MESSAGE'][$key]['message']),
                                "type" => $_SESSION['WIZZ_MESSAGE'][$key]['type'],
                                "time" => $_SESSION['WIZZ_MESSAGE'][$key]['time'])
                    );
                    unset($_SESSION['WIZZ_MESSAGE'][$key]);
                }
                
            }
        }
    }
    
    /**
     * Add the wizz to the new twig template system
     * @return Array $wizzlist Wizz list to show
     */
    public static function show_twig_messages() {
        $wizz_list = null;
        //var_dump($_SESSION['WIZZ_MESSAGE']); exit;
        if(isset($_SESSION['WIZZ_MESSAGE']) && is_array($_SESSION['WIZZ_MESSAGE'])) {
            @reset($_SESSION['WIZZ_MESSAGE']);
            while(list($key, $value) = each($_SESSION['WIZZ_MESSAGE'])) {
                $wizz_list[] = array(
                    "message" =>$_SESSION['WIZZ_MESSAGE'][$key]['message'],
                    "type" => $_SESSION['WIZZ_MESSAGE'][$key]['type'],
                    "time" => $_SESSION['WIZZ_MESSAGE'][$key]['time']
                );
                unset($_SESSION['WIZZ_MESSAGE'][$key]);   
            }
        }
        return $wizz_list;
    }

    public function already_wizzed($type = "") {
        if(!is_array($_SESSION['WIZZ_ALREADY_SENT'])) return false;

        if(!$type){
            if(is_array($_SESSION['WIZZ_ALREADY_SENT'])) return true;
            else return false;
        }
        else {
            if($_SESSION['WIZZ_ALREADY_SENT'][$type]) return true;
            else return false;
        }
    }

    /**
     * @deprecated just to keep compatibility with old modules, no longer used
     */
    public function set_ajax_mode() {
        $_SESSION['WIZZ_AJAX_MODE'] = false;
    }

    /**
     * @deprecated just to keep compatibility with old modules, no longer used
     */
    public function get_ajax_mode() {
        return $_SESSION['WIZZ_AJAX_MODE'];
    }
}

