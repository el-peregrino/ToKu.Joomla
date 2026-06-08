<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;
use ToKu\Component\JexContent\Administrator\Trait\AjaxSortAwareTrait;

\defined('_JEXEC') or die;

class CarouselsController extends AdminController
{
    use AjaxSortAwareTrait;

    public function getModel($name = 'Carousel', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function saveOrderAjax()
    {
        // check for request forgeries
        $this->checkToken();

        if ($this->reorderTable()) {
            echo '1';
        }

        $this->app->close();
    }
}
