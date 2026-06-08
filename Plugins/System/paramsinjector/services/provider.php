<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  paramsinjector
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use ToKu\Library\Joomla;
use ToKu\Plugin\System\ParamsInjector\Extension\ParamsInjectorPlugin;

\defined('_JEXEC') or die;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     * @return  void
     */
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $plugin = new ParamsInjectorPlugin(
                    (array) PluginHelper::getPlugin(Joomla::SYSTEM, ParamsInjectorPlugin::ELEMENT)
                );
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
