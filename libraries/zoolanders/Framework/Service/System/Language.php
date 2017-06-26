<?php

namespace Zoolanders\Framework\Service\System;

use Zoolanders\Framework\Service\System;

/**
 * Class Language
 * @package Zoolanders\System
 */
class Language extends System
{
    /**
     * Translate a string into the current language
     *
     * @param string $string The string to translate
     * @param booolean $js_safe If the string should be made js safe (default: true)
     *
     * @return string The translated string
     *
     * @since 1.0.0
     */
    public function l ($string, $js_safe = false)
    {
        return $this->_($string, $js_safe);
    }
}
