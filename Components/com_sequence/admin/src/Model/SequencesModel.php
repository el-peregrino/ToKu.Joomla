<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\Sequence\Administrator\Model;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

/**
 * Model class for handling list of sequences.
 */
class SequencesModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array                 $config   An optional associative array of configuration settings.
     * @param   ?MVCFactoryInterface  $factory  The factory.
     *
     * @since   1.6
     */
    public function __construct($config = [], ?MVCFactoryInterface $factory = null)
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 's.id',
                'title', 's.title',
                'alias', 's.alias',
                'type', 's.type',
                'published', 's.published',
                'access', 's.access', 'access_level',
                'language', 's.language', 'language_title',
            ];
        }

        parent::__construct($config, $factory);
    }

    protected function populateState($ordering = 's.id', $direction = 'asc')
    {
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select([
            $db->quoteName('s.id'),
            $db->quoteName('s.title'),
            $db->quoteName('s.alias'),
            $db->quoteName('s.type'),
            $db->quoteName('s.published'),
            $db->quoteName('s.language'),
            $db->quoteName('s.note'),
        ]);
        
        $query->from($db->quoteName('#__sequences', 's'));

        // language
        $query->select([
            $db->quoteName('l.title', 'language_title'),
            $db->quoteName('l.image', 'language_image'),
        ]);
        $query->join('LEFT',
            $db->quoteName('#__languages', 'l'),
            $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('s.language')
        );

        // access (asset groups)
        $query->select($db->quoteName('ag.title', 'access_level'));
        $query->join('LEFT',
            $db->quoteName('#__viewlevels', 'ag'),
            $db->quoteName('ag.id') . ' = ' . $db->quoteName('s.access')
        );

        // filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $search = (int) substr($search, 3);
                $query->where($db->quoteName('s.id') . ' = :search')
                    ->bind(':search', $search, ParameterType::INTEGER);
            } else {
                $search = '%' . str_replace(' ', '%', trim($search)) . '%';
                $query->extendWhere(
                    'AND',
                    [
                        $db->quoteName('s.title') . ' LIKE :title',
                        $db->quoteName('s.alias') . ' LIKE :alias',
                        $db->quoteName('s.note') . ' LIKE :note',
                    ],
                    'OR'
                )
                    ->bind(':title', $search)
                    ->bind(':alias', $search)
                    ->bind(':note', $search);
            }
        }

        // filter by sequence type
        $type = (string) $this->getState('filter.type');

        if (is_numeric($type)) {
            $type = (int) $type;
            $query->where($db->quoteName('s.type') . ' = :type')
                ->bind(':type', $type, ParameterType::INTEGER);
        } elseif ($type === '') {
            $query->whereIn($db->quoteName('s.type'), [0, 1]);
        }

        // filter by published state
        $published = (string) $this->getState('filter.published');

        if (is_numeric($published)) {
            $published = (int) $published;
            $query->where($db->quoteName('s.published') . ' = :published')
                ->bind(':published', $published, ParameterType::INTEGER);
        } elseif ($published === '') {
            $query->whereIn($db->quoteName('s.published'), [0, 1]);
        }

        // filter by access level.
        if ($access = (int) $this->getState('filter.access')) {
            $query->where($db->quoteName('s.access') . ' = :access')
                ->bind(':access', $access, ParameterType::INTEGER);
        }

        // filter on the language.
        if ($language = $this->getState('filter.language')) {
            $query->where($db->quoteName('s.language') . ' = :language')
                ->bind(':language', $language);
        }

        // add the list ordering clause
        $listOrdering = $this->getState('list.ordering', 's.id');
        $listDir     = $db->escape($this->getState('list.direction', 'ASC'));

        $query->order($db->escape($listOrdering) . ' ' . $listDir);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();

        $ids = array_map(fn($item) => $item->id, $items);
        $counts = $this->getItemCounts($ids);

        foreach ($items as &$item) {
            $item->published_count = $counts[$item->id][1] ?? 0;
            $item->unpublished_count = $counts[$item->id][0] ?? 0;
        }

        return $items;
    }


    protected function getItemCounts(array $ids)
    {
        if (empty($ids)) return [];

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('sequence_id'),
                $db->quoteName('published'),
                'COUNT(*) as count'])
            ->from('#__sequence_items')
            ->whereIn($db->quoteName('sequence_id'), $ids)
            ->group(['sequence_id', 'published']);
        $db->setQuery($query);
        $counts = $db->loadAssocList();

        // map the output
        $nested = [];
        foreach ($counts as $row) {
            $id = $row['sequence_id'];
            $type = $row['published'];
            $nested[$id][$type] = $row['count'];
        }
        return $nested;
    }

    public function getSequenceOptions()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('id AS value, title AS text')
            ->from('#__sequences')
            ->order('title ASC');
        $db->setQuery($query);

        return $db->loadObjectList();
    }
}