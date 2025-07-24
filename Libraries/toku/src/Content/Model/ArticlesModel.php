<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Library\Content\Model;

use Joomla\Component\Content\Site\Model\ArticlesModel as BaseModel;

\defined('_JEXEC') or die;

class ArticlesModel extends BaseModel
{
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

        // add the criteria field value - when the upcoming event starts
        $query->select($db->quoteName('fv.value') . ' AS ' . $db->quoteName('event_start'));

        // join field values
        $query->join('INNER', $db->quoteName('#__fields_values', 'fv'), $db->quoteName('fv.item_id') . '=' . $db->quoteName('a.id'));

        // use the configured criteria field
        $query->where($db->quoteName('fv.field_id') . ' = ' . $params->get('eventfilter_criteria_field'));

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
