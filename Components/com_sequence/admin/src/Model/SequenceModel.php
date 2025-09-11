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

class SequenceModel extends AdminModel
{
    protected $text_prefix = 'COM_SEQUENCE';

    public function getTable($type = 'Sequence', $prefix = 'Table', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = [], $loadData = true): mixed
    {
        $form = $this->loadForm('com_sequence.sequence', 'sequence', ['control' => 'jform', 'load_data' => $loadData]);
        return $form ?: false;
    }

    public function getItem($pk = null) {

        if ($item = parent::getItem($pk)) {
        // Convert the images field to an array.
            $registry     = new Registry($item->images);
            $item->images = $registry->toArray();
        }

        return $item;
    }

    protected function loadFormData(): mixed
    {
        /** @var \Joomla\CMS\Application\CMSWebApplicationInterface $app */
        $app = JToKu::getApp();
        $data = $app->getUserState('com_sequence.edit.sequence.data', []);

        return $data ?: $this->getItem();
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

        // handle images in the form
        if (isset($data['images']) && \is_array($data['images'])) {
            $registry = new Registry($data['images']);

            $data['images'] = (string) $registry;
        }

        return parent::save($data);
    }
}