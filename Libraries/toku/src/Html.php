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
    public static function append(string $value, bool $condition = true): string
    {
        return $condition && $value ? " $value" : '';
    }

    public static function boolean($value): string
    {
        return $value ? 'true' : 'false';
    }

    public static function noopener($target): string
    {
        return $target === '_blank' ? 'noopener' : '';
    }
}