<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\Sequence\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;

\defined('_JEXEC') or die;

class ItemsController extends AdminController
{
    public function getModel($name = 'Item', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function saveOrderAjax()
    {
        // check for request forgeries
        $this->checkToken();

        // get the input
        $pks   = (array) $this->input->post->get('cid', [], 'array');
        $order = (array) $this->input->post->get('order', [], 'array');

        // get model and table
        $model = $this->getModel();
        $table = $model->getTable();
        $table->reset();

        // load all items and group by sequence_id
        $grouped = [];

        foreach ($pks as $i => $id) {
            // check zero PKs
            if ($id == 0) {
                continue;
            }
            // load item
            $table->load($id);
            $sid = $table->sequence_id;

            $grouped[$sid][] = [
                'id' => $id,
                'ordering' => $order[$i]
            ];
        }

        // save and reorder per group
        foreach ($grouped as $sid => $items) {
            foreach ($items as $item) {
                $table->load($item['id']);
                $table->ordering = $item['ordering'];
                $table->store();
            }

            $return = $table->reorder('sequence_id = ' . (int) $sid);
            if (!$return) {
                break;
            }
        }

        if ($return) {
            echo '1';
        }

        // close the application
        $this->app->close();
    }
}