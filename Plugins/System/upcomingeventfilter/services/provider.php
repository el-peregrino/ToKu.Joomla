<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  upcomingeventfilter
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use ToKu\Plugin\System\UpcomingEventFilter\Extension\UpcomingEventFilterPlugin;

\defined('_JEXEC') or die;

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
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $plugin = new UpcomingEventFilterPlugin(
                    $container->get(DispatcherInterface::class),
                    (array) PluginHelper::getPlugin('system', 'upcomingeventfilter')
                );
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
