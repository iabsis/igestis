<?php

namespace Igestis\Types;

/**
 * Description of SidebarComplexItem
 *
 * @author Gilles Hemmerlé <giloux@gmail.com>
 */
abstract class SidebarComplexItem {
    /**
     * @return string Type of the complex type
     */
    abstract public function getType();
    /**
     * @return string Target of the template file
     */
    abstract public function getTemplateFile();
    /**
     * @return bool Is the link active or not
     */
    abstract public function isActive();
    /**
     * @return array List of the variable to replace in the field template
     */
    abstract public function getTemplateReplacements();
    /**
     * @return array List of the variable to replace in the global template
     */
    abstract public function getCustomGlobalReplacements();
}