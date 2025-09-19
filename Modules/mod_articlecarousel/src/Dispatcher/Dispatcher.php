<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_articlecarousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\ArticleCarousel\Site\Dispatcher;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\Input\Input;
use ToKu\Module\ArticleCarousel\Site\Helper\ArticleCarouselHelper;

\defined('_JEXEC') or die;

/**
 * Dispatcher class for mod_articlecarousel
 *
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    public function __construct(\stdClass $module, CMSApplicationInterface $app, Input $input)
    {
        parent::__construct($module, $app, $input);
    }

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     */
    protected function getLayoutData(): array|bool
    {
        // get layout data - Joomla implementation
        // data contains keys: ['module', 'app', 'input', 'params', 'template']
        $data = parent::getLayoutData();

        // stop the dispatch process
        if ($data === false) return false;

        /** @var ArticleCarouselHelper $helper */
        $helper = $this->getHelperFactory()->getHelper('ArticleCarouselHelper');

        // get articles
        $data['articles'] = $helper->getArticles($data['params'], $this->app);

        return $data;
    }
}
