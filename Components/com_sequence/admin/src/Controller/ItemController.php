<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\Sequence\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

\defined('_JEXEC') or die;

class ItemController extends FormController
{
    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
    {
        // TODO do we really need this? the state is used in the form
        $append = parent::getRedirectToItemAppend($recordId, $urlVar);

        $sequence = $this->input->get('sequence', '', 'cmd');
        return "&sequence=$sequence$append";        
    }
}