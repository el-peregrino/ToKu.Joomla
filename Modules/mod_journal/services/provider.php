<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_journal
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
use ToKu\Module\Journal\Site\Helper\JournalHelper;

\defined('_JEXEC') or die;

/**
 * The journal module service provider.
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
        $container->registerServiceProvider(new ModuleDispatcherFactory(Joomlib::getNamespace(JournalHelper::NAME, Joomlib::MODULE)));
        $container->registerServiceProvider(new HelperFactory(Joomlib::getSiteHelper(JournalHelper::NAME)));
        $container->registerServiceProvider(new Module());
    }
};