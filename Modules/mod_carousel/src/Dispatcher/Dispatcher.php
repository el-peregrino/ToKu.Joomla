<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_carousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Carousel\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

\defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_carousel
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

        /** @var CarouselHelper $helper */
        $helper = $this->getHelperFactory()->getHelper('CarouselHelper');

        // get the items
        $data['items'] = $helper->getItems($data['params'], $this->getApplication());

        return $data;
    }
}
