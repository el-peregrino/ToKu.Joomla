<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_journal
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Journal\Site\Helper;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;

class JournalHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    // TYPED CONSTANTS ARE SUPPORTED SINCE PHP 8.3

    public const NAME = 'Journal';
    public const MODULE = 'mod_journal';

    public function getAccessLevels(CMSApplicationInterface $app): string 
    {
        return implode(',', $app->getIdentity()->getAuthorisedViewLevels());
    }

    public function getRecord(int $id, array $levels): ?RecordData 
    {
        $db = $this->getDatabase();
        // build query
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__jex_records'))
            // filter the records
            ->where($db->quoteName('id') . ' = :id')
                ->bind(':id', $id)
            // filter published
            ->where($db->quoteName('published') . ' = 1')
            // filter access
            ->whereIn($db->quoteName('r.access') . ' IN(:levels)', $levels, ParameterType::INTEGER);

        $db->setQuery($query);
        $data = $db->loadAssoc();
        return $data ? new RecordData($data) : null;
    }

    public function getRecords(Registry $params, array $levels, string $language): array
    {
        $db = $this->getDatabase();
        // build query
        $query = $db->getQuery(true)
            ->select('r.*')
            ->from($db->quoteName('#__jex_records'))
            // filter published
            ->where($db->quoteName('r.published') . ' = 1')
            // filter access
            ->whereIn($db->quoteName('r.access') . ' IN(:levels)', $levels, ParameterType::INTEGER);

        // filter by language
        $lang = $params->get('language', 'inherit');
        if ($lang === 'inherit') {
            $lang = $language;
        }
        if ($lang !== '*') {
            $query->where($db->quoteName('r.language') . ' = :lang')
                ->bind(':lang', $lang);
        }

        // filter by timeline flag
        $timeline = $params->get('timeline');
        if ($timeline !== null) {
            $query->where($db->quoteName('r.timeline') . ' = :timeline')
                ->bind(':timeline', $timeline);
        }

        // filter by date
        $mode = $params->get('timemode');
        $date = $params->get('date', 'NOW()');
        if ($timeline && $mode && $date) {
            $cond = $mode === 'future' ? '>= :date' : '<= :date';
            $query->where($db->quoteName('r.date') . ' ' . $cond)
                ->bind(':date', $date);
        }

        // filter by categories
        $categories = $params->get('categories', []);
        if (!empty($categories)) {
            $query->whereIn($db->quoteName('r.catid'), $categories, ParameterType::INTEGER);
        }

        // filter by tags
        $tags = $params->get('tags', []);
        if (!empty($tags)) {
            $query->innerJoin(
                $db->quoteName('#__contentitem_tag_map', 'tm'),
                $db->quoteName('tm.content_item_id') . ' = ' . $db->quoteName('r.id')
                    . ' AND ' . $db->quoteName('tm.type_alias') . ' = ' . $db->quote('com_jexcontent.record')
            )
            ->whereIn($db->quoteName('tm.tag_id'), $tags, ParameterType::INTEGER);
        }

        // ordering
        $ordering = $params->get('ordering', 'component');
        $direction = $this->getDirection($params->get('direction', 'component'), $timeline);

        switch ($ordering) {
            case 'random':
                $query->order('RAND()');
                break;
            case 'heading':
                $query->order("heading $direction");
                break;
            case 'component':
            default:
                if ($timeline) {
                    // order by date
                    $query->order("date $direction");
                }
                else {
                    $query->order("ordering $direction");
                }
                break;
        }

        $db->setQuery($query);
        $items = [];
        $values = $db->loadAssocList('id');
        if ($values) {
            foreach ($values as $id => $value) {
                $items[$id] = new RecordData($value);
            }
        }
        return $items;
    }

    private function getDirection(string $direction, bool $timeline): string 
    {
        return match ($direction) {
            'ascending' => 'ASC',
            'descending' => 'DESC',
            'reverse' => $timeline ? 'ASC' : 'DESC',
            'component' => $timeline ? 'DESC' : 'ASC',
            default => ''
        };
    }
}