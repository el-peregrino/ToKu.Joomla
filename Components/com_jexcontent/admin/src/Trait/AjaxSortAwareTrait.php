<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\Trait;

\defined('_JEXEC') or die;

trait AjaxSortAwareTrait
{
    protected function reorderTable(string $groupColumn = 'catid')
    {
        // get the input
        $pks   = (array) $this->input->post->get('cid', [], 'array');
        $order = (array) $this->input->post->get('order', [], 'array');

        // get model and table
        $model = $this->getModel();
        $table = $model->getTable();
        $table->reset();

        // load all items and group by configured column
        $grouped = [];

        foreach ($pks as $i => $id) {
            // check zero PKs
            if ($id == 0) {
                continue;
            }
            // load item
            $table->load($id);
            $sid = $table->$groupColumn;

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

            $return = $table->reorder($groupColumn . ' = ' . (int) $sid);
            if (!$return) {
                break;
            }
        }

        return $return;
    }
}