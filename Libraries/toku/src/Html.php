<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JooToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Library;

class Html
{
    /**
     * Appends a string to another string when a condition is met.
     * If the value is not empty, the output is indented with a space char.
     * @param ?string $value    The value to append.
     * @param bool $condition   When true the value is appended.
     * @return string
     */
    public static function append(?string $value, bool $condition = true): string
    {
        return $condition && $value ? " $value" : '';
    }

    public static function attribute(string $name, string $value, bool $condition = true): string
    {
        return $condition && $name && $value ? " $name=\"$value\"" : '';
    }

    public static function boolean($value): string
    {
        return $value ? 'true' : 'false';
    }

    public static function noopener(string $target): string
    {
        return $target === '_blank' ? 'noopener' : '';
    }
}