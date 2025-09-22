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
use Joomla\CMS\Helper\ModuleHelper;
use ToKu\Module\Carousel\Site\Helper\CarouselHelper;

\defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_carousel
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
        
        require ModuleHelper::getLayoutPath(CarouselHelper::MODULE, $data['layout']);
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

        /** @var \ToKu\Module\Carousel\Site\Helper\CarouselHelper $helper */
        $helper = $this->getHelperFactory()->getHelper('CarouselHelper');

        // get the items
        $data['items'] = $helper->getItems($params, $this->app);

        // layout
        $data['layout'] = $params->get('layout', 'default');

        return $data;
    }
}
