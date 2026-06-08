<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\Model;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use ToKu\Library\Joomlib;

\defined('_JEXEC') or die;

class TestimonialsModel extends ListModel
{
    public function __construct($config = [], ?MVCFactoryInterface $factory = null)
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'r.id',
                'title', 'r.title',
                'published', 'r.published',
                'access', 'r.access', 'access_level',
                'language', 'r.language', 'language_title',
                'ordering', 'r.ordering',
                'category', 'c.title',
            ];
        }

        parent::__construct($config, $factory);
    }

    protected function populateState($ordering = 'r.id', $direction = 'asc')
    {
        $record = Joomlib::getUserStateFromRequest('com_jexcontent.testimonials.filter.catid', 'catid', null, 'int');

        $this->setState('filter.catid', $record);

        parent::populateState($ordering, $direction);
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select([
            $db->quoteName('r.id'),
            $db->quoteName('r.catid'),
            $db->quoteName('r.title'),
            $db->quoteName('r.header'),
            $db->quoteName('r.body'),
            $db->quoteName('r.footer'),
            $db->quoteName('r.author'),
            $db->quoteName('r.about'),
            $db->quoteName('r.published'),
            $db->quoteName('r.language'),
            $db->quoteName('r.note'),
            $db->quoteName('r.ordering'),
        ]);

        $query->from($db->quoteName('#__jex_testimonials', 'r'));

        $query->select([
            $db->quoteName('l.title', 'language_title'),
            $db->quoteName('l.image', 'language_image'),
        ]);
        $query->join('LEFT',
            $db->quoteName('#__languages', 'l'),
            $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('r.language')
        );

        $query->select($db->quoteName('ag.title', 'access_level'));
        $query->join('LEFT',
            $db->quoteName('#__viewlevels', 'ag'),
            $db->quoteName('ag.id') . ' = ' . $db->quoteName('r.access')
        );

        $query->select($db->quoteName('c.title', 'category'));
        $query->join('LEFT',
            $db->quoteName('#__categories', 'c'),
            $db->quoteName('c.id') . ' = ' . $db->quoteName('r.catid')
        );

        $published = (string) $this->getState('filter.published');

        if (is_numeric($published)) {
            $published = (int) $published;
            $query->where($db->quoteName('r.published') . ' = :published')
                ->bind(':published', $published, ParameterType::INTEGER);
        } else {
            $query->whereIn($db->quoteName('r.published'), [0, 1]);
        }

        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $search = (int) substr($search, 3);
                $query->where($db->quoteName('r.id') . ' = :search')
                    ->bind(':search', $search, ParameterType::INTEGER);
            } else {
                $search = '%' . str_replace(' ', '%', trim($search)) . '%';
                $query->extendWhere(
                    'AND',
                    [
                        $db->quoteName('r.title') . ' LIKE :search',
                        $db->quoteName('r.header') . ' LIKE :search',
                        $db->quoteName('r.body') . ' LIKE :search',
                    ],
                    'OR'
                )
                    ->bind(':search', $search);
            }
        }

        if ($catid = $this->getState('filter.catid')) {
            $query->where($db->quoteName('r.catid') . ' = :catid')
                ->bind(':catid', $catid, ParameterType::INTEGER);
        }

        if ($access = (int) $this->getState('filter.access')) {
            $query->where($db->quoteName('r.access') . ' = :access')
                ->bind(':access', $access, ParameterType::INTEGER);
        }

        if ($language = $this->getState('filter.language')) {
            $query->where($db->quoteName('r.language') . ' = :language')
                ->bind(':language', $language);
        }

        // tag filtering
        $tags = $this->getState('filter.tags') ?: $this->getState('filter.tag');

        if (!empty($tags)) {
            if (!is_array($tags)) {
                $tags = explode(',', (string) $tags);
            }

            $tagIds = array_map('intval', $tags);
            $tagIds = array_filter($tagIds, fn($v) => $v > 0);

            if (!empty($tagIds)) {
                $query->join('INNER',
                    $db->quoteName('#__contentitem_tag_map', 'tm'),
                    $db->quoteName('tm.content_item_id') . ' = ' . $db->quoteName('r.id')
                    . ' AND ' . $db->quoteName('tm.type_alias') . ' = ' . $db->quote('com_jexcontent.testimonial')
                );

                $query->where($db->quoteName('tm.tag_id') . ' IN (' . implode(',', $tagIds) . ')');

                $query->group($db->quoteName('r.id'));
            }
        }

        $ordering = [];
        $listOrdering = $this->getState('list.ordering', 'r.id');
        $listDir     = $db->escape($this->getState('list.direction', 'ASC'));

        if ($listOrdering === 'r.ordering') {
            $ordering[] = $db->escape('r.catid') . ' ' . $listDir;
        }

        $ordering[] = $db->escape($listOrdering) . ' ' . $listDir;

        $query->order($ordering);

        return $query;
    }

    public function getFilterForm($data = [], $loadData = true)
    {
        $data['catid'] = $this->getState('filter.catid');

        $form = parent::getFilterForm($data, $loadData);

        $form->bind($data);

        return $form;
    }
}
