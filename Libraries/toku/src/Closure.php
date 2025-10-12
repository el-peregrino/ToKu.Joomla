<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JooToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Library;

use Joomla\Registry\Registry;

class Closure
{
    public static function equals(Registry $params)
    {
        return function(string $key, $value) use($params): bool {
            $param = $params->get($key, '');
            return $value === $param;
        };
    }

    public static function isFalse(Registry $params)
    {
        return function(string $key) use($params): bool {
            $param = $params->get($key, '');
            return !$param;
        };
    }

    public static function isTrue(Registry $params)
    {
        return function(string $key) use($params): bool {
            $param = $params->get($key, '');
            return !!$param;
        };
    }

    public static function param(Registry $params)
    {
        return function(string $key) use($params) {
            $param = $params->get($key, '');
            return $param ? " $param" : $param;
        };
    }
}
