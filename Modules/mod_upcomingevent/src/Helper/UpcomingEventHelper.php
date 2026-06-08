<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_upcomingevent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\UpcomingEvent\Site\Helper;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

/**
 * Helper for mod_upcomingevent
 */
class UpcomingEventHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    public const NAME = 'UpcomingEvent';
    public const MODULE = 'mod_upcomingevent';

    private static array $labels = ['DAYS', 'HOURS', 'MINUTES', 'SECONDS'];

    public static function getAsset(string $name): string
    {
        return self::MODULE . ".$name";
    }
    
    /**
     * Retrieve upcoming event data
     *
     * @param   Registry                $params     The module parameters.
     * @param   CMSApplicationInterface $app        The application.
     * @param   string                  $language   The language of the module
     *
     * @return  mixed
     */
    public function getUpcomingEvent(Registry $params, CMSApplicationInterface $app, string $language): ?\stdClass
    {
        // id of the event field
        $eventField = $params->get('event_field');
        if (!$eventField) {
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
            ->join('INNER', 
                $db->quoteName('#__fields_values', 'fv'), 
                $db->quoteName('fv.item_id') . '=' . $db->quoteName('a.id'))
            // event field
            ->where($db->quoteName('fv.field_id') . ' = :field')
                ->bind(':field', $eventField)
            // published items only
            ->where($db->quoteName('a.state') . ' = 1')
            ->where($db->quoteName('a.publish_up') . ' <= NOW() AND (' . $db->quoteName('a.publish_down') . ' is null OR ' . $db->quoteName('a.publish_down') . ' <= NOW())')
            // upcoming events only
            ->where($db->quoteName('fv.value') . ' >= NOW()')
            // user access
            ->whereIn($db->quoteName('a.access'), $user->getAuthorisedViewLevels(), ParameterType::INTEGER);

        // language filter
        if ($language !== '*') {
            $query->where($db->quoteName('a.language') . ' = :language')
                ->bind(':language', $language);
        }

        // optional category filter
        $categories = $params->get('categories', []);
        if (!empty($categories)) {
            $query->whereIn($db->quoteName('a.catid'), $categories, ParameterType::INTEGER);
        }

        // optional tag filter
        $tags = $params->get('tags', []);
        if (!empty($tags)) {
            $query->join('INNER', 
                $db->quoteName('#__contentitem_tag_map', 'tm'),
                $db->quoteName('tm.content_item_id') . ' = ' . $db->quoteName('a.id') . ' AND ' . $db->quoteName('tm.type_alias') . ' = ' . $db->quote($db->escape('com_content.article')))
                ->whereIn($db->quoteName('tm.tag_id'), $tags, ParameterType::INTEGER);
        }

        // ordering
        $query->order($db->quoteName('fv.value') . ' ASC');

        $db->setQuery($query, 0, 1); // only one result

        return $db->loadObject() ?: null;
    }

    private static function getDefaultLabels(): array
    {
        return array_map(fn($key)=> Text::_("MOD_UPCOMINGEVENT_$key"), self::$labels);
    }

    /**
     * Splits a string by multiple characters, trims each part, removes empty ones,
     * and ensures the result has exactly N elements, where N is size of the defaults.
     *
     * @param string $input The input string
     * @param string $delimiters A regex-safe character list (e.g. ",;|")
     * @return array An array of exactly N cleaned segments
     */
    public static function getLabels(string $input, string $delimiters = ",;| "): array
    {
        // Split by any delimiter using regex
        $parts = preg_split('/[' . preg_quote($delimiters, '/') . ']/', $input);

        $defaults = self::getDefaultLabels();

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
