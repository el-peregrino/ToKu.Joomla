<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\Model;

use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\ParameterType;
use ToKu\Component\JexContent\Administrator\Table\RecordTable;
use ToKu\Library\Joomlib;

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
            Joomlib::convertColumnToFieldset($item, 'links');
            Joomlib::convertColumnToFieldset($item, 'images');
            
            // Load tags for this record
            if ($item->id) {
                $tagsHelper = new TagsHelper();
                $item->tags = $tagsHelper->getTagIds($item->id, 'com_jexcontent.record');
            }
        }

        return $item;
    }

    protected function loadFormData(): array
    {
        $app = Joomlib::getApp();
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
        $app = Joomlib::getApp();
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
        // handle images in the form
        Joomlib::convertFieldsetToColumn($data, 'links');
        Joomlib::convertFieldsetToColumn($data, 'images');
        Joomlib::convertFieldsetToColumn($data, 'params');

        // Call parent save
        $return = parent::save($data);

        // Save tags after record is saved
        if ($return && isset($data['tags'])) {
            $tags = is_array($data['tags']) ? $data['tags'] : explode(',', (string) $data['tags']);

            // Determine the saved record ID with several fallbacks
            $id = 0;

            // 1) If the incoming data contained an id (edit), prefer that
            if (!empty($data['id']) && (int) $data['id'] > 0) {
                $id = (int) $data['id'];
            }

            // 2) Some AdminModel implementations populate the model state with the id
            if ($id === 0) {
                try {
                    $stateId = (int) $this->getState($this->getName() . '.id');
                    if ($stateId > 0) {
                        $id = $stateId;
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            // 3) Check the table object for an id property
            if ($id === 0) {
                try {
                    $table = $this->getTable();
                    if (!empty($table->id)) {
                        $id = (int) $table->id;
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }


            if ($id > 0) {
                $this->saveRecordTags($id, $tags);
            } else {
                $app = Joomlib::getApp();
                $app->enqueueMessage('Unable to determine saved record id; tags were not attached.', 'warning');
            }
        }

        return $return;
    }

    private function saveRecordTags(int $id, array $tags): void
    {
        $db = $this->getDatabase();
        
        // Get the type_id for the content type
        $query = $db->getQuery(true)
            ->select($db->quoteName('type_id'))
            ->from($db->quoteName('#__content_types'))
            ->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_jexcontent.record'));
        
        $db->setQuery($query);
        $typeId = (int) $db->loadResult();
        
        if (!$typeId) {
            return; // Content type not registered, skip tag save
        }

        // Delete existing tags for this record
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__contentitem_tag_map'))
            ->where($db->quoteName('content_item_id') . ' = ' . $db->quote($id))
            ->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_jexcontent.record'));

        $db->setQuery($query)->execute();

        // Insert new tags
        foreach ($tags as $tagId) {
            $tagId = (int) $tagId;
            if ($tagId === 0) {
                continue;
            }

            $columns = [
                $db->quoteName('content_item_id'),
                $db->quoteName('type_alias'),
                $db->quoteName('tag_id'),
                $db->quoteName('type_id'),
                $db->quoteName('core_content_id'),
            ];
            $values = [
                $db->quote($id),
                $db->quote('com_jexcontent.record'),
                $db->quote($tagId),
                $db->quote($typeId),
                $db->quote($id),
            ];

            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__contentitem_tag_map'))
                ->columns($columns)
                ->values(implode(',', $values));

            $db->setQuery($query)->execute();
        }
    }
}