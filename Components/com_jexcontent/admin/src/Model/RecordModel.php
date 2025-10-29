<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\Model;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\ParameterType;
use ToKu\Component\JexContent\Administrator\Table\RecordTable;
use ToKu\Library\JooToKu;

\defined('_JEXEC') or die;

class RecordModel extends AdminModel
{
    protected $text_prefix = 'COM_JEXCONTENT';

    public function getTable($type = 'Record', $prefix = 'Table', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = [], $loadData = true): mixed
    {
        // load form from the xml spec
        $form = $this->loadForm('com_jexcontent.record', 'record', ['control' => 'jform', 'load_data' => $loadData]);
        return $form ?: false;
    }

    public function getItem($pk = null) {

        if ($item = parent::getItem($pk)) {
            JooToKu::convertColumnToFieldset($item, 'links');
            JooToKu::convertColumnToFieldset($item, 'images');
        }

        return $item;
    }

    protected function loadFormData(): array
    {
        $app = JooToKu::getApp();
        // get form data from session
        $state = $app->getUserState('com_jexcontent.edit.record.data', []);

        // load database values and convert to array safely
        $item = (array) json_decode(json_encode($this->getItem()), true);

        // merge data to override missing fields from db
        // state overrides item
        $data = array_merge($item, $state);
        
        return $data;
    }

    protected function prepareTable($table)
    {
        $app = JooToKu::getApp();
        $task = $app->getInput()->getCmd('task');
        if ($task === 'save2copy') {
            // reset ID so Joomla treats it as a new record
            $table->id = 0;
            // make unpublished
            $table->published = 0;

            // modify title and alias to avoid duplicates
            $origTitle = $table->title;
            $table->title = "$origTitle (Copy)";
            $table->alias = '';
        }

        if (!$table->id && $table instanceof RecordTable) {
            $table->ordering = $this->getNextOrdering($table);
        }

        parent::prepareTable($table);
    }

    private function getNextOrdering(RecordTable $table): int
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('MAX(ordering)')
              ->from($db->quoteName('#__jex_records'))
              ->where($db->quoteName('catid') . ' = :category')
              ->bind(':category', $table->catid, ParameterType::INTEGER);

        $db->setQuery($query);
        $max = (int) $db->loadResult();

        return $max + 1;
    }

    public function save($data): mixed
    {
        /* Add code to modify data before saving */

        // handle images in the form
        JooToKu::convertFieldsetToColumn($data, 'links');
        JooToKu::convertFieldsetToColumn($data, 'images');
        JooToKu::convertFieldsetToColumn($data, 'params');

        return parent::save($data);
    }
}