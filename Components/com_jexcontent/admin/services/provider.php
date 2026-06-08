<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use ToKu\Component\JexContent\Administrator\Extension\JexContentComponent;
use ToKu\Library\Joomlib;

\defined('_JEXEC') or die;

/**
 * The jex content component service provider.
 */
return new class() implements ServiceProviderInterface {
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
        $container->registerServiceProvider(new ComponentDispatcherFactory(Joomlib::getNamespace(JexContentComponent::NAME, Joomlib::COMPONENT)));
        $container->registerServiceProvider(new MVCFactory(Joomlib::getNamespace(JexContentComponent::NAME, Joomlib::COMPONENT)));
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new JexContentComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                return $component;
            }
        );
    }
};