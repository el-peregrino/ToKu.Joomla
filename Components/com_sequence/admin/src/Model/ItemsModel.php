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
use ToKu\Library\JToKu;

\defined('_JEXEC') or die;

class ItemsModel extends ListModel
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
                'id', 'si.id',
                'title', 'si.title',
                'published', 'si.published',
                'access', 'si.access', 'access_level',
            ];
        }

        parent::__construct($config, $factory);
    }

    protected function populateState($ordering = 'si.id', $direction = 'asc')
    {
        $app = JToKu::getApp();

        /**
         * Current sequence.
         * The sequence is read from input. If not defined, session value is used.
         * The result value is written back to the session (user state).
         * 
         * Notice: The original getUserStateFromRequest() should do the job, but it may fail silently.
         * 
         * @var int $sequence
         */
        $sequence = JToKu::getUserStateFromRequest('com_sequence.items.filter.sequence', 'sequence', null, 'int');

        // get the sequence filter value from the request state (local, not affected by session)
        $sequenceFilter = $this->getState('filter.sequence', '');
        
        // handle sequence changes
        if ($sequence != $sequenceFilter) {
            // filter has changed
            $app->getInput()->set('limitstart', 0); // reset pagination
        }

        // set the sequence in the request state (local, not affected by session)
        $this->setState('filter.sequence', $sequence);
        
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select([
            $db->quoteName('si.id'),
            $db->quoteName('si.sequence_id'),
            $db->quoteName('si.title'),
            $db->quoteName('si.caption'),
            $db->quoteName('si.heading'),
            $db->quoteName('si.subheading'),
            $db->quoteName('si.published'),
            $db->quoteName('si.note')
        ]);
        
        $query->from($db->quoteName('#__sequence_items', 'si'));

        // join sequences (the parent)
        $query->select([
            $db->quoteName('s.title', 'sequence_title'),
            $db->quoteName('s.type', 'sequence_type'),
        ]);
        $query->join('INNER',
            $db->quoteName('#__sequences', 's'),
            $db->quoteName('s.id') . ' = '. $db->quoteName('si.sequence_id')
        );

        // access (asset groups)
        $query->select($db->quoteName('ag.title', 'access_level'));
        $query->join('LEFT',
            $db->quoteName('#__viewlevels', 'ag'),
            $db->quoteName('ag.id') . ' = ' . $db->quoteName('si.access')
        );

        // filter by sequence
        $sequence = $this->getState('filter.sequence');
        if (!empty($sequence)) {
            $query->where($db->quoteName('si.sequence_id') . ' = :sequence')
                ->bind(':sequence', $sequence, ParameterType::INTEGER);
        }

        // filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $search = (int) substr($search, 3);
                $query->where($db->quoteName('si.id') . ' = :search')
                    ->bind(':search', $search, ParameterType::INTEGER);
            } else {
                $search = '%' . str_replace(' ', '%', trim($search)) . '%';
                $query->extendWhere(
                    'AND',
                    [
                        $db->quoteName('si.title') . ' LIKE :search',
                        $db->quoteName('si.caption') . ' LIKE :search',
                        $db->quoteName('si.heading') . ' LIKE :search',
                        $db->quoteName('si.subheading') . ' LIKE :search',
                        $db->quoteName('si.note') . ' LIKE :search',
                    ],
                    'OR'
                )
                    ->bind(':search', $search);
            }
        }

        // filter by published state
        $published = (string) $this->getState('filter.published');

        if (is_numeric($published)) {
            $published = (int) $published;
            $query->where($db->quoteName('si.published') . ' = :published')
                ->bind(':published', $published, ParameterType::INTEGER);
        } elseif ($published === '') {
            $query->whereIn($db->quoteName('si.published'), [0, 1]);
        }

        // filter by access level.
        if ($access = (int) $this->getState('filter.access')) {
            $query->where($db->quoteName('si.access') . ' = :access')
                ->bind(':access', $access, ParameterType::INTEGER);
        }

        // add the list ordering clause
        $listOrdering = $this->getState('list.ordering', 'si.id');
        $listDir     = $db->escape($this->getState('list.direction', 'ASC'));

        $query->order($db->escape($listOrdering) . ' ' . $listDir);

        return $query;
    }

    public function getFilterForm($data = [], $loadData = true)
    {
        // inject the filter.sequence state into the form data
        $data['sequence'] = $this->getState('filter.sequence');

        /** @var \Joomla\CMS\Form\Form $form */
        $form = parent::getFilterForm($data, $loadData);

        /** @var \Joomla\CMS\Form\Field\ListField $field */
        $field = $form->getField('sequence');

        // load SequencesModel
        $model = new SequencesModel();
        $options = $model->getSequenceOptions();

        // inject options into filter field
        foreach ($options as $option) {
            $field->addOption($option->text, ['value' => $option->value]);
        }

        $form->bind($data);

        return $form;
    }

}