<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Extension\Service\Provider\HelperFactory;
use Joomla\CMS\Extension\Service\Provider\Module;
use Joomla\CMS\Extension\Service\Provider\ModuleDispatcherFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use ToKu\Library\JooToKu;
use ToKu\Module\Sequence\Site\Helper\SequenceHelper;

\defined('_JEXEC') or die;

/**
 * The sequence module service provider.
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
        $container->registerServiceProvider(new ModuleDispatcherFactory(JooToKu::getNamespace(SequenceHelper::NAME, JooToKu::MODULE)));
        $container->registerServiceProvider(new HelperFactory(JooToKu::getSiteHelper(SequenceHelper::NAME)));
        $container->registerServiceProvider(new Module());
    }
};