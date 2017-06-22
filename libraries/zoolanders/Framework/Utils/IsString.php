<?php

namespace Zoolanders\Framework\Utils;

trait IsString {
    /**
     * @param $item
     * @return bool
     */
    public function isString ($item) {
        if (
            (!is_array($item)) &&
            $item !== null &&
            !is_integer($item) &&
            !is_float($item) &&
            ((!is_object($item) && settype($item, 'string') !== false) ||
                (is_object($item) && method_exists($item, '__toString')))
        ) {
            return true;
        }

        return false;
    }
}
