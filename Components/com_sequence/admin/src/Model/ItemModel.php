<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\Sequence\Administrator\Model;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\ParameterType;
use ToKu\Component\Sequence\Administrator\Table\ItemTable;
use ToKu\Library\JooToKu;

\defined('_JEXEC') or die;

class ItemModel extends AdminModel
{
    public function getTable($type = 'Item', $prefix = 'Table', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        // new item
        if (empty($item->id)) {
            /**
             * Current sequence.
             * The sequence is read from input. If not defined, session value is used.
             * The result value is written back to the session (user state).
             * 
             * Notice: The original getUserStateFromRequest() should do the job, but it may fail silently.
             * 
             * @var int $sequence
             */
            $sequence = JooToKu::getUserStateFromRequest('com_sequence.items.filter.sequence', 'sequence', null, 'int');

            if ($sequence) {
                $item->sequence_id = $sequence;
            }
        }

        if ($item) {
            JooToKu::convertColumnToFieldset($item, 'links');
            JooToKu::convertColumnToFieldset($item, 'images');
        }

        return $item;
    }


    private function getSequenceType(array $data): ?int 
    {
        // load form data
        $formData = !empty($data) ? $data : $this->loadFormData();
        $sequenceId = $formData['sequence_id'] ?? null;

        // sequence id not set -> sequence type cannot be loaded
        if (!$sequenceId) return null;

        // load sequence
        $model = new SequenceModel();
        $sequence = $model->getItem($sequenceId);
        
        return $sequence->type;
        
    }

    public function getForm($data = [], $loadData = true): mixed
    {
        // load form from the xml spec
        $form = $this->loadForm('com_sequence.item', 'item', ['control' => 'jform', 'load_data' => $loadData]);

        // get type of the edited sequence
        $type = $this->getSequenceType($data);

        if ($type === null) {
            // the sequence type cannot be determined
            $form->setFieldAttribute('date', 'readonly', 'true');
            $form->setFieldAttribute('date', 'required', 'false');
        }
        elseif ($type === 0) {
            // the sequence is a list
            $form->removeField('date');
        }

        // fill the sequence options
        $sequences = new SequencesModel();
        $options = $sequences->getSequenceOptions();

        /** @var \Joomla\CMS\Form\Field\ListField $field */
        $field = $form->getField('sequence_id');
        foreach ($options as $option) {
            $field->addOption($option->text, ['value' => $option->value]);
        }

        $form->bind($data);

        return $form;
    }

    protected function loadFormData()
    {
        $app = JooToKu::getApp();
        // get form data from session
        $state = $app->getUserState('com_sequence.edit.item.data', []);

        // load database values and convert to array safely
        $item = (array) json_decode(json_encode($this->getItem()), true);
        
        // merge data to override missing fields from db
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

        if (!$table->id && $table instanceof ItemTable) {
            $table->ordering = $this->getNextOrdering($table);
        }

        parent::prepareTable($table);
    }

    private function getNextOrdering(ItemTable $table): int
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('MAX(ordering)')
              ->from($db->quoteName('#__sequence_items'))
              ->where($db->quoteName('sequence_id') . ' = :sequence')
              ->bind(':sequence', $table->sequence_id, ParameterType::INTEGER);

        $db->setQuery($query);
        $max = (int) $db->loadResult();

        return $max + 1;
    }

    public function save($data): mixed
    {
        /* Add code to modify data before saving */

        JooToKu::convertFieldsetToColumn($data, 'links');
        JooToKu::convertFieldsetToColumn($data, 'images');
        JooToKu::convertFieldsetToColumn($data, 'params');

        return parent::save($data);
    }
}