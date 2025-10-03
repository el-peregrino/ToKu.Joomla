<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_upcomingevent
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\UpcomingEvent\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Helper\ModuleHelper;
use ToKu\Module\UpcomingEvent\Site\Helper\UpcomingEventHelper;

\defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_upcomingevent
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    public function dispatch(): void
    {
        $data = $this->getLayoutData();

        // stop the dispatch process
        if ($data === false) return;

        // extract data for the template
        extract($data);
        
        require ModuleHelper::getLayoutPath(UpcomingEventHelper::MODULE, $data['layout']);
    }

    protected function getLayoutData(): array|bool
    {
        // get layout data - Joomla implementation
        // data contains keys: ['module', 'app', 'input', 'params', 'template']
        $data = parent::getLayoutData();

        // stop the dispatch process
        if ($data === false) return $data;

        /** @var \Joomla\Registry\Registry $params */
        $params = $data['params'];

        /** @var UpcomingEventHelper $helper */
        $helper = $this->getHelperFactory()->getHelper('UpcomingEventHelper');

        // get the next event
        $data['event'] = $helper->getUpcomingEvent($data['params'], $this->app, $this->module->language ?? '*');
        // layout
        $data['layout'] = $params->get('layout', 'default');

        return $data;
    }
}
