<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JooToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Library;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\WebAsset\WebAssetManager;

\defined('_JEXEC') or die;

/**
 * ToKu.Joomla 5 Library
 */
class JooToKu
{
    private static $application;
    private static $document;
    private static $webAssetManager;

    // TYPED CONSTANTS ARE SUPPORTED SINCE PHP 8.3

    /**
     * Name of the library.
     * @var string
     */
    public const NAME = 'ToKu';

    /**
     * Version of the library.
     * @var string
     */
    public const VERSION = '1.0.15';

    /**
     * Namespace prefix of a component.
     * @var string
     */
    public const COMPONENT = 'Component';

    /**
     * Namespace prefix of a library.
     * @var string
     */
    public const LIBRARY = 'Library';

    /**
     * Namespace prefix of a module.
     * @var string
     */
    public const MODULE = 'Module';

    /**
     * Site helper namespace path.
     * @var string
     */
    public const SITE_HELPER = '\\Site\\Helper';

    /**
     * Namespace separator.
     * @var string
     */
    public const NAMESPACE_SEPARATOR = '\\';

    /**
     * Builds namespace path for ToKu extension.
     * @param string[] $names   Names of the path parts.
     * @return string
     */
    public static function getPath(string ...$names): string 
    {
        array_unshift($names, self::NAMESPACE_SEPARATOR, self::NAME);
        return implode(self::NAMESPACE_SEPARATOR, $names);
    }

    /**
     * Gets the path of the asset in the ToKu library.
     * @param string $name  Name of the asset
     * @return string
     */
    public static function getAsset(string $name): string
    {
        return strtolower(self::NAME) . ".$name";
    }

    /**
     * Gets unique id.
     * @return string
     */
    public static function getUniqueId(): string
    {
        return self::NAME . '-' . uniqid();
    }

    /**
     * Gets namespace of a ToKu extension.
     * @param string $name      Name of the extension.
     * @param string $extension Type of the extension.
     * @return string
     */
    public static function getNamespace(string $name, string $extension): string
    {
        return self::getPath($extension, $name);
    }

    /**
     * Gets site helper path of a ToKu extension.
     * @param string $name  Name of the extension.
     * @return string
     */
    public static function getSiteHelper(string $name): string
    {
        return self::getPath(self::MODULE, $name) . self::SITE_HELPER;
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
        $wa->getRegistry()->addExtensionRegistryFile(strtolower(self::NAME));
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
     * @return SiteApplication
     */
    public static function getApp(): SiteApplication
    {
        if (!self::$application && (Factory::getApplication() instanceof SiteApplication)) {
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

    /**
     * Gets the value of a user state variable.
     *
     * @param   string  $key      The key of the user state variable.
     * @param   string  $request  The name of the variable passed in a request.
     * @param   string  $default  The default value for the variable if not found. Optional.
     * @param   string  $type     Filter for the variable. Optional.
     *                  @see      \Joomla\CMS\Filter\InputFilter::clean() for valid values.
     *
     * @return  mixed  The request user state.
     */
    public static function getUserStateFromRequest(string $key, string $request, $default = null, string $type = 'none'): mixed
    {
        $app = self::getApp();

        // get value from the session
        $state = $app->getUserState($key, $default);
        
        // get value from the input
        $value = $app->getInput()->get($request, $state, $type);

        // set user state
        $app->setUserState($key, $value);

        return $value;
    }

    /**
     * Gets name of the module the config form.
     * @param Form $form    The form object to inspect.
     * @return string|false
     */
    public static function getConfigModule(Form $form): string|false
    {
        // find config element with module attribute
        $config = $form->getXml()->xpath('//config[@module]');
        // read module attribute or return empty
        return $config ? $config[0]['module'] : false;
    }

}