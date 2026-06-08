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
use ToKu\Library\Joomlib;

\defined('_JEXEC') or die;

abstract class BaseContentModel extends AdminModel
{
    protected $text_prefix = 'COM_JEXCONTENT';

    protected function getXmlForm($contentType, $data = [], $loadData = true): mixed
    {
        // load form from the xml spec
        $form = $this->loadForm('com_jexcontent.' . $contentType, $contentType, ['control' => 'jform', 'load_data' => $loadData]);
        return $form ?: false;
    }

    protected function getItemWithTags($contentType, $pk = null) 
    {
        if ($item = parent::getItem($pk)) 
        {
            Joomlib::convertColumnToFieldset($item, 'links');
            Joomlib::convertColumnToFieldset($item, 'images');

            // Load tags for this record
            if ($item->id) {
                $tagsHelper = new TagsHelper();
                $item->tags = $tagsHelper->getTagIds($item->id, 'com_jexcontent.' . $contentType);
            }
        }

        return $item;
    }

    protected function loadFormContentData($contentType): array
    {
        $app = Joomlib::getApp();
        // get form data from session
        $state = $app->getUserState('com_jexcontent.edit.' . $contentType . '.data', []);

        // load database values and convert to array safely
        $item = (array) json_decode(json_encode($this->getItemWithTags($contentType)), true);

        // merge data to override missing fields from db
        // state overrides item
        $data = array_merge($item, $state);

        return $data;
    }

    private function saveRecordTags(int $id, array $tags, string $type = 'quote'): void
    {
        $db = $this->getDatabase();

        $alias = 'com_jexcontent.' . $type;

        $query = $db->getQuery(true)
            ->select($db->quoteName('type_id'))
            ->from($db->quoteName('#__content_types'))
            ->where($db->quoteName('type_alias') . ' = ' . $db->quote($alias));

        $db->setQuery($query);
        $typeId = (int) $db->loadResult();

        if (!$typeId) {
            return;
        }

        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__contentitem_tag_map'))
            ->where($db->quoteName('content_item_id') . ' = ' . $db->quote($id))
            ->where($db->quoteName('type_alias') . ' = ' . $db->quote($alias));

        $db->setQuery($query)->execute();

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
                $db->quote($alias),
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

    protected function saveData($contentType, $data): mixed
    {
        Joomlib::convertFieldsetToColumn($data, 'links');
        Joomlib::convertFieldsetToColumn($data, 'images');
        Joomlib::convertFieldsetToColumn($data, 'params');

        $return = parent::save($data);

        if ($return && isset($data['tags'])) {
            $tags = is_array($data['tags']) ? $data['tags'] : explode(',', (string) $data['tags']);

            $id = 0;
            if (!empty($data['id']) && (int) $data['id'] > 0) {
                $id = (int) $data['id'];
            }

            if ($id === 0) {
                try {
                    $table = $this->getTable();
                    if (!empty($table->id)) {
                        $id = (int) $table->id;
                    }
                } catch (\Throwable $e) {
                }
            }

            if ($id > 0) {
                $this->saveRecordTags($id, $tags, $contentType);
            }
        }

        return $return;
    }
}