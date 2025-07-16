<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Library;

use Joomla\CMS\Factory;

/**
 * ToKu.Joomla library
 */
class JToKu 
{
    /**
     * Version of the library.
     */
    public const VERSION = '1.0.3';

    /**
     * Web Asset Manager - Register Asset Helper
     * 
     * Automatically registers the toku asset.
     * 
     * @param   string[]    $assets  comma separated list of asset names.
     * 
     * @return  object      Web Asset Manager
     */
    public static function wamRegister(...$assets) 
    {
        // get web asset manager
        $wa = Factory::getDocument()->getWebAssetManager();
        // register toku library
        $wa->getRegistry()->addExtensionRegistryFile('toku');
        // register other assets
        foreach($assets as $asset) 
        {
            $wa->getRegistry()->addExtensionRegistryFile($asset);
        }

        return $wa;
    }
}