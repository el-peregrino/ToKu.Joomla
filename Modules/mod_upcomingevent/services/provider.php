<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_upcomingevent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use ToKu\Library\Joomlib;
use ToKu\Module\UpcomingEvent\Site\Helper\UpcomingEventHelper;

\defined('_JEXEC') or die;

/**
 * The upcoming event module service provider.
 */
return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     */
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory(Joomlib::getNamespace(UpcomingEventHelper::NAME, Joomlib::MODULE)));
        $container->registerServiceProvider(new HelperFactory(Joomlib::getSiteHelper(UpcomingEventHelper::NAME)));
        $container->registerServiceProvider(new Module());
    }
};
