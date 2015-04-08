<?php

/**
 * Description of HookListenerInterface
 *
 * @author Gilles Hemmerlé
 */

namespace Igestis\Interfaces;

interface HookListenerInterface {
    /**
     * @param string $HookName Name reference of the hook
     * @param \Igestis\Types\HookParameters $params Parameters sent by the hook
     */
    public static function listen ($HookName, \Igestis\Types\HookParameters $params=null);
}