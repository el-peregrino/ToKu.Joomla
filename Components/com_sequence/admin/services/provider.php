<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use ToKu\Component\Sequence\Administrator\Extension\SequenceComponent;
use ToKu\Library\JooToKu;

\defined('_JEXEC') or die;

/**
 * The sequence component service provider.
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
        $container->registerServiceProvider(new ComponentDispatcherFactory(JooToKu::getNamespace(SequenceComponent::NAME, JooToKu::COMPONENT)));
        $container->registerServiceProvider(new MVCFactory(JooToKu::getNamespace(SequenceComponent::NAME, JooToKu::COMPONENT)));
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new SequenceComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                return $component;
            }
        );
    }
};