<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_upcomingevent
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\UpcomingEvent\Site\Helper;

use Joomla\CMS\Application\SiteApplication;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

/**
 * Helper for mod_upcomingevent
 *
 */
class UpcomingEventHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Retrieve upcoming event data
     *
     * @param   Registry            $params  The module parameters.
     * @param   SiteApplication     $app     The application.
     *
     * @return  mixed
     *
     */
    public function getUpcomingEvent(Registry $params, SiteApplication $app): ?\stdClass
    {
        // id of the criteria field
        $criteriaField = $params->get('criteria_field');
        if (!$criteriaField) {
            return null;
        }

        $user = $app->getIdentity();
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('a.id'),
                $db->quoteName('a.title'),
                $db->quoteName('a.introtext'),
                $db->quoteName('a.images'),
                $db->quoteName('fv.value')
            ])
            ->from($db->quoteName('#__content', 'a'))
            ->join('INNER', $db->quoteName('#__fields_values', 'fv'), $db->quoteName('fv.item_id') . '=' . $db->quoteName('a.id'))
            // criteria field
            ->where($db->quoteName('fv.field_id') . ' = ' . $db->quote($criteriaField))
            // published items only
            ->where($db->quoteName('a.state') . ' = 1')
            ->where($db->quoteName('a.publish_up') . ' <= NOW() AND (' . $db->quoteName('a.publish_down') . ' is null OR ' . $db->quoteName('a.publish_down') . ' <= NOW())')
            // upcoming events only
            ->where($db->quoteName('fv.value') . ' >= NOW()')
            // user access
            ->where($db->quoteName('a.access') . ' IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');

        // optional category filter
        $categoryIds = $params->get('filter_categories', []);
        if (!empty($categoryIds)) {
            $query->where($db->quoteName('a.catid') . ' IN (' . implode(',', array_map('intval', $categoryIds)) . ')');
        }

        // optional tag filter
        $tagIds = $params->get('filter_tags', []);
        if (!empty($tagIds)) {
            $query->join('INNER', $db->quoteName('#__contentitem_tag_map') . ' AS ' . $db->quoteName('tm')
                . ' ON ' . $db->quoteName('tm.content_item_id') . ' = ' . $db->quoteName('a.id')
                . ' AND ' . $db->quoteName('tm.type_alias') . ' = ' . $db->quote($db->escape('com_content.article')))
                ->where($db->quoteName('tm.tag_id') . ' IN (' . implode(',', array_map('intval', $tagIds)) . ')');
        }

        // ordering
        $query->order($db->quoteName('fv.value') . ' ASC');

        $db->setQuery($query, 0, 1); // only one result

        return $db->loadObject() ?: null;
    }

    /**
     * Splits a string by multiple characters, trims each part, removes empty ones,
     * and ensures the result has exactly N elements, where N is size of the defaults.
     *
     * @param string $input The input string
     * @param array $defaults Array of default values to fill missing positions
     * @param string $delimiters A regex-safe character list (e.g. ",;|")
     * @return array An array of exactly N cleaned segments
     */
    public static function getLabels(string $input, array $defaults, string $delimiters = ",;| "): array
    {
        // Split by any delimiter using regex
        $parts = preg_split('/[' . preg_quote($delimiters, '/') . ']/', $input);

        $count = count($defaults);

        // Trim each part and remove completely empty ones
        $parts = array_values(array_filter(array_map('trim', $parts), fn($val) => $val !== ''));

        // Fill in defaults if missing
        for ($i = 0; $i < $count; $i++) {
            if (!isset($parts[$i])) {
                $parts[$i] = $defaults[$i] ?? null;
            }
        }

        // Truncate if more than count
        return array_slice($parts, 0, $count);
    }
}
