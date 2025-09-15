<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Sequence\Site\Dispatcher;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Dispatcher\DispatcherInterface;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use ToKu\Module\Sequence\Site\Helper\SequenceHelper;
use ToKu\Module\Timeline\Site\Helper\TimelineData;

\defined('_JEXEC') or die;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    public function __construct(\stdClass $module, CMSApplicationInterface $app, Input $input)
    {
        parent::__construct($module, $app, $input);
    }

    public function dispatch(): void
    {
        $data = $this->getLayoutData();

        // stop the dispatch process
        if ($data === false) return;

        // extract data for the template
        extract($data);
        
        require ModuleHelper::getLayoutPath(SequenceHelper::MODULE, $data['layout']);
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

        /** @var SequenceHelper $helper */
        $helper = $this->getHelperFactory()->getHelper('SequenceHelper');

        $levels = $helper->getAccessLevels($this->app);
        // load sequence
        $sequence = $helper->getSequence((int) $params->get('sequence'), $levels);
        $data['sequence'] = $sequence;
        // load items
        $data['items'] = $helper->getItems($params, $levels, $sequence ? $sequence->type : -1);

        return $data;
    }
}