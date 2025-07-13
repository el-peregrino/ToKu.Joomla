<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_articlecarousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The article carousel module service provider.
 */
return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     */
    public function register(Container $container) :void
    {
        // Joomla services
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\ToKu\\Module\\ArticleCarousel'));
        $container->registerServiceProvider(new HelperFactory('\\ToKu\\Module\\ArticleCarousel\\Site\\Helper'));
        $container->registerServiceProvider(new Module());
    }
};
