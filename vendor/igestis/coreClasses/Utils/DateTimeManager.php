<?php

namespace Igestis\Utils;


/**
 * Class DateTimeManager found on the php.net website
 * @link http://www.php.net/manual/fr/class.datetime.php#96875 Comment where is the class
 */
class DateTimeManager extends \DateTime {

    public function getTimestamp() {
        return $this->format("U");
    }

    /**
     * This function calculates the number of days between the first and the second date. Arguments must be subclasses of DateTime
     * @param \DateTime $firstDate
     * @param \DateTime $secondDate
     * @return int Number of days
     */
    static function differenceInDays(\DateTime $firstDate, \DateTime $secondDate) {
        $firstDateTimeStamp = $firstDate->format("U");
        $secondDateTimeStamp = $secondDate->format("U");
        $rv = round((($firstDateTimeStamp - $secondDateTimeStamp)) / 86400);
        return $rv;
    }

    /**
     * This function returns an object of DateClass from $time in format $format. See date() for possible values for $format
     * @param string $format (ie: "d/m/Y : H:i:s")
     * @param string $time The date with the same formate (ie: "31/12/2013 15:10:02")
     * @return \Igestis\Utils\DateTimeManager
     */
    static function createFromFormat($format, $time) {
        
        if($format == "" || $time == "") {
            return null;
        }

        $regexpArray['Y'] = "(?P<Y>19|20\d\d)";
        $regexpArray['m'] = "(?P<m>0[1-9]|1[012])";
        $regexpArray['d'] = "(?P<d>0[1-9]|[12][0-9]|3[01])";
        $regexpArray['-'] = "[-]";
        $regexpArray['/'] = "[\/]";
        $regexpArray['.'] = "[\. /.]";
        $regexpArray[':'] = "[:]";
        $regexpArray['space'] = "[\s]";
        $regexpArray['H'] = "(?P<H>0[0-9]|1[0-9]|2[0-3])";
        $regexpArray['i'] = "(?P<i>[0-5][0-9])";
        $regexpArray['s'] = "(?P<s>[0-5][0-9])";

        $formatArray = str_split($format);
        $regex = "";

        // create the regular expression
        foreach ($formatArray as $character) {
            if ($character == " ")
                $regex = $regex . $regexpArray['space'];
            elseif (array_key_exists($character, $regexpArray))
                $regex = $regex . $regexpArray[$character];
        }
        $regex = "/" . $regex . "/";

        // get results for regualar expression
        $result = null;
        preg_match($regex, $time, $result);

        // create the init string for the new DateTime
        $initString = $result['Y'] . "-" . $result['m'] . "-" . $result['d'];

        // if no value for hours, minutes and seconds was found add 00:00:00
        if (isset($result['H']))
            $initString = $initString . " " . $result['H'] . ":" . $result['i'] . ":" . $result['s'];
        else {
            $initString = $initString . " 00:00:00";
        }
        try {
            $date = new self($initString);
            return $date;
        }
        catch(\Exception $e) {     
            return null;
        }
        
    }

}