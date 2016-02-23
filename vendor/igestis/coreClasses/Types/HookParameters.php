<?php

namespace Igestis\Types;

/**
 * Define the type HookParameters
 *
 * @author Gilles Hemmerlé
 */
class HookParameters
{
    /**
     *
     * @var Array List of parameters
     */
    private $parameters;

    /**
     * Constructor, initialize parameters
     * @param array $array Array with pair key/value of parameters
     */
    public function __construct($array = null)
    {
        if (is_array($array)) {
            $this->parameters = $array;
        } else {
            $this->parameters = array();
        }
    }

    /**
     * Set a variable
     * @param string $var Variable name
     * @param string $value Variable value
     */
    public function set($var, $value)
    {
        $this->parameters[$var] = $value;
        reset($this->parameters);
    }

    /**
     * Get a value
     * @param string $var Value to return
     * @return string Requested value
     */
    public function get($var)
    {

        if (!isset($this->parameters[$var])) {
            return null;
        }
        return $this->parameters[$var];
    }

}
