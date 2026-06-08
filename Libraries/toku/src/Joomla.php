<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  Joomlib
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
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
    // THEY ARE NOT SUPPORTED IN PHP 8.2, SO WE CANNOT USE THEM YET

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

    public const COMPONENTS = 'components';
    public const LIBRARIES = 'libraries';
    public const MODULES = 'modules';
    public const PLUGINS = 'plugins';
    public const LAYOUTS = 'layouts';

    /**
     * Builds file or directory path.
     * @param string[] $names   Parts (folders) of the path.
     * @return string
     */
    public static function getPath(?string ...$names): string 
    {
        return implode(DIRECTORY_SEPARATOR, array_filter($names));
    }

    public static function getLayoutPath(string $name, string $type, ?string $subtype = null): string 
    {
        return self::getPath(JPATH_ROOT, $type, $subtype, strtolower($name), self::LAYOUTS);
    }
}