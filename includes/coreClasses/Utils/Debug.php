<?php
namespace Igestis\Utils;
/**
 * This class is used to store some debugs datas and calculate the execution time of the whole page generation.
 * The collected datas are returned and could be sent to the view by the template generator.
 *
 * @author Gilles Hemmerlé (iabsis) <giloux@gmail.com>
 * @package igestis
 */
class Debug {
    /**
     *
     * @var Igestis\Utils\Debug Instance reference (singleton)
     */
    private static $_instance;   
    
    /**
     *
     * @var string Microtime when the script has started 
     */
    private static $scriptStart;
    
    /**
     *
     * @var Array List of the events to show to the debugger view
     */
    private static $eventsList;
    
    private static $showed;
    
    /** 
     * The constructor is private, use the getInstance() method to retreive the singleton
     */
    private function __construct() {
        self::$scriptStart = microtime(true);
        self::$eventsList = array();
        self::$showed = false;
        if(isset($_SESSION['DEBUG_DUMPS']) && is_array($_SESSION['DEBUG_DUMPS'])) {
            self::$eventsList = array_merge(self::$eventsList, $_SESSION['DEBUG_DUMPS']);
        }
    }
    
    public function __destruct() {
        if(\ConfigIgestisGlobalVars::debugMode() == false) return;
        
        if(self::$showed == false) {
            $_SESSION['DEBUG_DUMPS'] = self::getDumps();
        }
        else {
             $_SESSION['DEBUG_DUMPS'] = array();
        }
    }
    
    private static function getDumps() {
        $buffer = array();
        foreach (self::$eventsList as $event) {
            if($event['type'] == "dump") {
                $event['type'] = "old_dump";
                $buffer[] = $event;
            }
        }
        
        return $buffer;
    }

    
    /**
     * Return the list of events
     * @return Array List of events
     */
    public function getEvents() {
        self::$showed = true;
        return self::$eventsList;
    }

    /**
     * Return the singleton reference
     * @return Igestis\Utils\Debug Reference of the object
     */
  public static function getInstance() {
        if(self::$_instance == null) {
            self::$_instance =  new self();
        }        
        return self::$_instance;
    }
    
    /**
     * Return the execute time from the begining of the script
     * @return String Actuel execution time
     */
    public static function getScriptTime() {
        return microtime(true) - self::$scriptStart;
    }
    
    /**
     * Add an error to the event listener which will be sent to the debugger view
     * @param type $errno
     * @param type $errstr
     * @param type $errfile
     * @param type $errline
     */
    public function addError($errno, $errstr, $errfile, $errline) {        
        if(\ConfigIgestisGlobalVars::debugMode() == false) return;
        self::$eventsList[] = array(
            "type" => "error", 
            "scriptDuration" => self::getScriptTime(),
            "datas" => array(
                "errno" => $errno,
                "message" =>$errstr,
                "errfile" => $errfile,
                "errline" => $errline                
             )
        );
    }
    
    /**
     * Add the doctrine sql sentences to the events log for the debug environment
     * @param String $sql
     * @param Array $params
     * @param Array $types
     * @param float $time
     */
    public static function addDoctrineLog($sql, $params, $types, $time) {     
        if(\ConfigIgestisGlobalVars::debugMode() == false) return;
        self::$eventsList[] = array(
            "type" => "doctrine",
            "scriptDuration" => self::getScriptTime(),
            "datas" => array(
                "sql" => $sql,
                "params" => \Igestis\Utils\Dump::get($params), 
                "executionTime" => $time
            )
        );
    }
    
    /**
     * Add a variable dump to the debugger view
     * @param Mixed Variable to dump and to show to the debugger
     * @param String Variable name
     * @param Int $depth Depth of the dump (for arrays)
     */
    public static function addDump($var, $varname=null, $depth=2) {      
        if(\ConfigIgestisGlobalVars::debugMode() == false) return;
        self::$eventsList[] = array(
            "type" => "dump",
            "scriptDuration" => self::getScriptTime(),
            "datas" => array(
                "varname" => $varname == null ? "Unnamed variable" : $varname,
                "dump" => \Igestis\Utils\Dump::get($var)
            )
        );
    }
    
    /**
     * Add a log text to the debug vew
     * @param type $msg
     */
    public static function addLog($msg) {
        if(\ConfigIgestisGlobalVars::debugMode() == false) return;
        self::$eventsList[] = array(
            "type" => "log",
            "scriptDuration" => self::getScriptTime(),
            "datas" => array(
                "message" => $msg
            )
        );
    }
    
    public static function FileLogger($log, $logFile = null) {
        $application = \Application::getInstance();
        
        $login = "Unknown";
        if (isset($application->security->contact)) {
            $login = $application->security->contact->getLogin();
        }
        else {
            $login = "Anonymous";
        }
        if($logFile !== null) {
            @file_put_contents($logFile, date("Y-m-d H:i:s") . " - " . $login . " - " . $log . "\n", FILE_APPEND);
        }
        elseif (\ConfigIgestisGlobalVars::logFile()) {
            @file_put_contents(\ConfigIgestisGlobalVars::logFile(), date("Y-m-d H:i:s") . " - " . $login . " - " . $log . "\n", FILE_APPEND);
        }
    }


    /**
     * Return the printable text of the object
     * @return String How to show the object
     */
    public function __toString() {
        return "Debuging time = " . self::getScriptTime() . " millisecondes";
    }
    
}