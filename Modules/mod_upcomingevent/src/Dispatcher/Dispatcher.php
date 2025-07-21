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

\defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_upcomingevent
 *
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     */
    protected function getLayoutData()
    {
        // get layout data - Joomla implementation
        $data = parent::getLayoutData();

        /** @var UpcomingEventHelper $helper */
        $helper = $this->getHelperFactory()->getHelper('UpcomingEventHelper');

        // get the next event
        $data['event'] = $helper->getUpcomingEvent($data['params'], $this->getApplication());

        return $data;
    }
}
