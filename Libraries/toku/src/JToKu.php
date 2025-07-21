<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Library;

use Joomla\CMS\WebAsset\WebAssetManager;
use Joomla\CMS\Factory;

\defined('_JEXEC') or die;

/**
 * ToKu.Joomla 5 Library
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
     * Automatically registers the ToKu asset.
     * 
     * @param   string[]        $assets  Comma separated list of asset names.
     * 
     * @return  WebAssetManager Instance of WebAssetManager      
     */
    public static function wamRegister(...$assets): WebAssetManager
    {
        // get web asset manager
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        // register ToKu library
        $wa->getRegistry()->addExtensionRegistryFile('toku');
        // register other assets
        foreach ($assets as $asset) {
            $wa->getRegistry()->addExtensionRegistryFile($asset);
        }

        return $wa;
    }
}