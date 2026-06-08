<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\View;

use Joomla\CMS\Application\CMSWebApplicationInterface;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use ToKu\Library\Joomlib;

\defined('_JEXEC') or die;

/**
 * Base class for the Jex Content Html View.
 */
abstract class BaseHtmlView extends HtmlView 
{
    /**
     * Gets the toolbar object of the Html document.
     * @param string $toolbar
     * @param bool $create
     * @return Toolbar|null
     */
    protected function getToolbar(string $toolbar = 'toolbar', bool $create = true): ?Toolbar
    {
        /** @var \Joomla\CMS\Document\HtmlDocument $document; */
        $document = $this->getDocument();

        return $document->getToolbar($toolbar, $create);
    }

    /**
     * Gets the global application object.
     * Wraps the Joomlib::getApp().
     * @return CMSWebApplicationInterface
     */
    protected function getApp(): CMSWebApplicationInterface 
    { 
        return Joomlib::getApp();
    }
}