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
use Joomla\Registry\Registry;
use ToKu\Library\JToKu;

\defined('_JEXEC') or die;

class ItemModel extends AdminModel
{
    public function getTable($type = 'Item', $prefix = 'Table', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    protected function populateState() 
    {
        parent::populateState();


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
        /** @var \Joomla\CMS\Application\CMSWebApplicationInterface $app */
        $app = JToKu::getApp();
        $state = $app->getUserState('com_sequence.edit.item.data', []);

        $data = $state ?: (array) $this->getItem();

        /**
         * Get the session value. It check multiple sources in the following order:
         * 1. POST request values (form data)
         * 2. GET request values (url params)
         * 3. Session values (user state)
         * 4. Default value
         * The result value is stored in the user state (session).
         */
        $sequence = $app->getUserStateFromRequest('com_sequence.items.sequence', 'sequence', '', 'cmd');

        // prefill the sequence_id
        if (empty($data['sequence_id']) && $sequence) {
            $data['sequence_id'] = $sequence;
        }
        
        return $data;
    }

    protected function prepareTable($table)
    {
        /** @var \Joomla\CMS\Application\CMSWebApplicationInterface $app */
        $app = JToKu::getApp();
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

        parent::prepareTable($table);
    }

    public function save($data): mixed
    {
        /* Add code to modify data before saving */

        // handle links in the form
        if (isset($data['links']) && \is_array($data['links'])) {
            $registry = new Registry($data['links']);

            $data['links'] = (string) $registry;
        }

        // handle images in the form
        if (isset($data['images']) && \is_array($data['images'])) {
            $registry = new Registry($data['images']);

            $data['images'] = (string) $registry;
        }

        return parent::save($data);
    }
}