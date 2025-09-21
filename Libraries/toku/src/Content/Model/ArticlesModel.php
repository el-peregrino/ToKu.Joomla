<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Library\Content\Model;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Content\Site\Model\ArticlesModel as BaseModel;

\defined('_JEXEC') or die;

class ArticlesModel extends BaseModel
{
    /**
     * Constructor.
     *
     * @param   array                 $config   An optional associative array of configuration settings.
     * @param   ?MVCFactoryInterface  $factory  The factory.
     */
    public function __construct($config = [], ?MVCFactoryInterface $factory = null)
    {
        parent::__construct($config, $factory);
    }

    protected function getListQuery()
    {
        // get the original query
        $query = parent::getListQuery();

        // retrieve parameters
        $params = $this->getState('params');

        // check upcoming event filter enabled
        if (!$params->get('eventfilter_enabled', 0)) {
            return $query;
        }

        $db = $this->getDatabase();

        // add the event field value - when the upcoming event starts
        $query->select($db->quoteName('fv.value') . ' AS ' . $db->quoteName('event_start'));

        // join field values
        $query->join('INNER', $db->quoteName('#__fields_values', 'fv'), $db->quoteName('fv.item_id') . '=' . $db->quoteName('a.id'));

        // use the configured event field
        $query->where($db->quoteName('fv.field_id') . ' = ' . $params->get('eventfilter_event_field'));

        // upcoming events filter
        if (!$params->get('eventfilter_show_past', 0)) {
            $query->where($db->quoteName('fv.value') . ' >= NOW()');
        }

        // override the ordering
        switch ($params->get('eventfilter_order_events')) {
            case 'ascending':
                $query->clear('order')->order($db->quoteName('fv.value') . ' ASC');
                break;

            case 'descending':
                $query->clear('order')->order($db->quoteName('fv.value') . ' DESC');
                break;
        }

        return $query;
    }
}
