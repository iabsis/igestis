<?php

namespace Igestis\Interfaces;

/**
 * Description of ConfigMenuInterface
 *
 * @author Gilles Hemmerlé
 */
interface ConfigMenuInterface {
    public static function menuSet(\Application $context, \IgestisMenu &$menu);
}