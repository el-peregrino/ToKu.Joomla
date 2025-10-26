<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\Model;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use ToKu\Library\JooToKu;

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
                'heading', 's.heading',
                'published', 's.published',
                'access', 's.access', 'access_level',
                'language', 's.language', 'language_title',
                'ordering', 's.ordering',
                'category', 'c.title',
            ];
        }

        parent::__construct($config, $factory);
    }

    protected function populateState($ordering = 's.id', $direction = 'asc')
    {
        /**
         * Current category.
         * The category (catid) is read from input. If not defined, session value is used.
         * The result value is written back to the session (user state).
         * 
         * Notice: The original getUserStateFromRequest() should do the job, but it may fail silently.
         * 
         * @var int $sequence
         */
        $sequence = JooToKu::getUserStateFromRequest('com_jexcontent.sequences.filter.catid', 'catid', null, 'int');

        // set the category in the request state (local, not affected by session)
        $this->setState('filter.catid', $sequence);

        parent::populateState($ordering, $direction);
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select([
            $db->quoteName('s.id'),
            $db->quoteName('s.catid'),
            $db->quoteName('s.title'),
            $db->quoteName('s.subtitle'),
            $db->quoteName('s.heading'),
            $db->quoteName('s.subheading'),
            $db->quoteName('s.date'),
            $db->quoteName('s.timeline'),
            $db->quoteName('s.published'),
            $db->quoteName('s.language'),
            $db->quoteName('s.note'),
            $db->quoteName('s.ordering'),
        ]);
        
        $query->from($db->quoteName('#__jex_sequences', 's'));

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

        // category
        $query->select($db->quoteName('c.title', 'category'));
        $query->join('LEFT',
            $db->quoteName('#__categories', 'c'),
            $db->quoteName('c.id') . ' = ' . $db->quoteName('s.catid')
        );


        // filter by published state
        $published = (string) $this->getState('filter.published');

        if (is_numeric($published)) {
            $published = (int) $published;
            $query->where($db->quoteName('s.published') . ' = :published')
                ->bind(':published', $published, ParameterType::INTEGER);
        } 
        else {
            $query->whereIn($db->quoteName('s.published'), [0, 1]);
        }

        // filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $search = (int) substr($search, 3);
                $query->where($db->quoteName('s.id') . ' = :search')
                    ->bind(':search', $search, ParameterType::INTEGER);
            } else {
                $search = '%' . str_replace(' ', '%', trim($search)) . '%';
                // BE AWARE - extendWhere expects an existing where clause in the query
                $query->extendWhere(
                    'AND',
                    [
                        $db->quoteName('s.title') . ' LIKE :search',
                        $db->quoteName('s.subtitle') . ' LIKE :search',
                        $db->quoteName('s.heading') . ' LIKE :search',
                        $db->quoteName('s.subheading') . ' LIKE :search',
                        $db->quoteName('s.note') . ' LIKE :search',
                    ],
                    'OR'
                )
                    ->bind(':search', $search);
            }
        }

        // filter by category
        if ($catid = $this->getState('filter.catid')) {
            $query->where($db->quoteName('s.catid') . ' = :catid')
                ->bind(':catid', $catid, ParameterType::INTEGER);
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
        $ordering = [];
        $listOrdering = $this->getState('list.ordering', 's.id');
        $listDir     = $db->escape($this->getState('list.direction', 'ASC'));

        if ($listOrdering === 's.ordering') {
            // order by category first
            $ordering[] = $db->escape('s.catid') . ' ' . $listDir;
        }

        $ordering[] = $db->escape($listOrdering) . ' ' . $listDir;

        if ($listOrdering === 's.heading') {
            // order by subheading too
            $ordering[] = $db->escape('s.subheading') . ' ' . $listDir;
        }

        if ($listOrdering === 's.title') {
            // order by subtitle too
            $ordering[] = $db->escape('s.subtitle') . ' ' . $listDir;
        }

        $query->order($ordering);

        return $query;
    }

    public function getFilterForm($data = [], $loadData = true)
    {
        // inject the filter.catid state into the form data
        $data['catid'] = $this->getState('filter.catid');

        /** @var \Joomla\CMS\Form\Form $form */
        $form = parent::getFilterForm($data, $loadData);

        $form->bind($data);

        return $form;
    }
}