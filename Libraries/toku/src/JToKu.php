<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Library;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Application\WebApplication;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\WebAsset\WebAssetManager;

\defined('_JEXEC') or die;

/**
 * ToKu.Joomla 5 Library
 */
class JToKu
{
    private static $application;
    private static $document;
    private static $webAssetManager;

    /**
     * Name of the library.
     */
    public const string NAME = 'ToKu';

    /**
     * Asset name of the library.
     */
    public const string LIBRARY = 'toku';

    /**
     * Version of the library.
     */
    public const string VERSION = '1.0.6';

    /**
     * Namespace prefix of the library.
     */
    public const string NAMESPACE = '\\ToKu\\Module\\';

    /**
     * Site helper namespace path.
     */
    public const string SITE_HELPER = '\\Site\\Helper';

    /**
     * Gets the path of the asset in the ToKu library.
     * @param string $name  Name of the asset
     * @return string
     */
    public static function getAsset(string $name): string
    {
        return self::LIBRARY . ".$name";
    }

    /**
     * Gets unique id.
     * @return string
     */
    public static function getUniqueId(): string
    {
        return self::LIBRARY . '-' . uniqid();
    }

    /**
     * Gets namespace of a ToKu extension.
     * @param string $name  Name of the extension.
     * @return string
     */
    public static function getNamespace(string $name): string
    {
        return self::NAMESPACE . $name;
    }

    /**
     * Gets site helper path of a ToKu extension.
     * @param string $name  Name of the extension.
     * @return string
     */
    public static function getSiteHelper(string $name): string
    {
        return self::getNamespace($name) . self::SITE_HELPER;
    }

    /**
     * Web Asset Manager - Register Asset Helper
     * 
     * Registers all assets at once.
     * 
     * Automatically registers the ToKu asset.
     * 
     * @param array $assets     Array of asset names to register.
     * @param array $scripts    Array of script names to use.
     * @param array $styles     Array of style names to use.
     * @return WebAssetManager  Instance of WebAssetManager
     */
    public static function registerWebAssets(array $assets, array $scripts, array $styles): WebAssetManager 
    {
        $wa = self::registerExtensionFile(...$assets);
        self::useScripts(...$scripts);
        self::useStyles(...$styles);

        return $wa;
    }

    /**
     * Web Asset Manager - Register Asset Helper
     * 
     * Automatically registers the ToKu asset.
     * 
     * @param   string[]        $assets  Comma separated list of asset names.
     * 
     * @return  WebAssetManager Instance of WebAssetManager      
     */
    public static function registerExtensionFile(string ...$assets): WebAssetManager
    {
        // get web asset manager
        $wa = self::getWebAssetManager();
        // register ToKu library
        $wa->getRegistry()->addExtensionRegistryFile(self::LIBRARY);
        // register other assets
        foreach ($assets as $asset) {
            $wa->getRegistry()->addExtensionRegistryFile($asset);
        }

        return $wa;
    }

    /**
     * Web Asset Manager - Use Script Helper
     * 
     * @param   string[]        $scripts  Comma separated list of script names.
     * 
     * @return  WebAssetManager Instance of WebAssetManager      
     */
    public static function useScripts(string ...$scripts): WebAssetManager
    {
        // get web asset manager
        $wa = self::getWebAssetManager();
        // register scripts
        foreach ($scripts as $script) {
            $wa->useScript($script);
        }

        return $wa;
    }

    /**
     * Web Asset Manager - Use Style Helper
     * 
     * @param   string[]        $styles  Comma separated list of style names.
     * 
     * @return  WebAssetManager Instance of WebAssetManager      
     */
    public static function useStyles(string ...$styles): WebAssetManager
    {
        // get web asset manager
        $wa = self::getWebAssetManager();
        // register scripts
        foreach ($styles as $style) {
            $wa->useStyle($style);
        }

        return $wa;
    }

    /**
     * Gets the global application object.
     * Wraps the Factory::getApplication().
     * @return CMSApplicationInterface | WebApplication
     */
    public static function getApp(): CMSApplicationInterface | WebApplication
    {
        if (!self::$application) {
            self::$application = Factory::getApplication();
        }
        return self::$application;
    }

    /**
     * Gets the application document object.
     * @return Document
     */
    public static function getDocument(): Document
    {
        if (!self::$document) {
            self::$document = self::getApp()->getDocument();
        }
        return self::$document;
    }

    /**
     * Gets the web asset manager object.
     * @return WebAssetManager
     */
    public static function getWebAssetManager() : WebAssetManager
    {
        if (!self::$webAssetManager) {
            self::$webAssetManager = self::getDocument()->getWebAssetManager();
        }
        return self::$webAssetManager;
    }

}