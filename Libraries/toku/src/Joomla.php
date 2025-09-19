<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Library;

\defined('_JEXEC') or die;

/**
 * Joomla system extensions.
 */
class Joomla 
{
    // TYPED CONSTANTS ARE SUPPORTED SINCE PHP 8.3

    /**
     * Joomla's system folder name.
     * Typically used with plugins.
     * @var string
     */
    public const SYSTEM = 'system';

    /**
     * Joomla's forms folder name.
     * Typically used for extension form definitions.
     * @var string
     */
    public const FORMS = 'forms';

    /**
     * Builds file or directory path.
     * @param string[] $names   Parts (folders) of the path.
     * @return string
     */
    public static function getPath(string ...$names): string 
    {
        return implode(DIRECTORY_SEPARATOR, $names);
    }
}