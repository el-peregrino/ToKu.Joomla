<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_articlecarousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\ArticleCarousel\Site\Dispatcher;

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

        /** @var ArticleCarouselHelper $helper */
        $helper = $this->getHelperFactory()->getHelper('ArticleCarouselHelper');

        // get articles
        $data['articles'] = $helper->getArticles($data['params'], $this->getApplication());

        return $data;
    }
}
