<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_journal
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Journal\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Helper\ModuleHelper;
use ToKu\Module\Journal\Site\Helper\JournalHelper;

\defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_journal
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
        
        require ModuleHelper::getLayoutPath(JournalHelper::MODULE, $data['layout']);
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
        $module = $data['module'];

        /** @var JournalHelper $helper */
        $helper = $this->getHelperFactory()->getHelper('JournalHelper');

        $levels = $this->app->getIdentity()->getAuthorisedViewLevels();
        // load records
        $data['records'] = $helper->getRecords($params, $levels, $module->language);
        // layout
        $data['layout'] = $params->get('layout', 'default');

        return $data;
    }
}