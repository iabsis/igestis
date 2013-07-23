<?php

namespace Igestis\Interfaces;

/**
 * Description of ConfigSidebarInterface
 *
 * @author Gilles Hemmerlé
 */
interface ConfigSidebarInterface {
    public static function sidebarSet(\Application $context, \IgestisSidebar &$sidebar);
}