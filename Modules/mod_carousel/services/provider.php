<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_carousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

\defined('_JEXEC') or die;

/**
 * The carousel module service provider.
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
    public function register(Container $container): void
    {
        // Joomla services
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\ToKu\\Module\\Carousel'));
        $container->registerServiceProvider(new HelperFactory('\\ToKu\\Module\\Carousel\\Site\\Helper'));
        $container->registerServiceProvider(new Module());
    }
};
