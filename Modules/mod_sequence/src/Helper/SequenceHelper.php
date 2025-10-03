<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Sequence\Site\Helper;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;

class SequenceHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    // TYPED CONSTANTS ARE SUPPORTED SINCE PHP 8.3

    public const NAME = 'Sequence';
    public const MODULE = 'mod_sequence';

    public function getAccessLevels(CMSApplicationInterface $app): string 
    {
        return implode(',', $app->getIdentity()->getAuthorisedViewLevels());
    }

    public function getSequence(int $id, string $levels): ?SequenceData 
    {
        $db = $this->getDatabase();
        // build query
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__sequences'))
            // filter the sequence
            ->where($db->quoteName('id') . ' = :id')
                ->bind(':id', $id)
            // filter published
            ->where($db->quoteName('published') . ' = 1')
            // filter access
            ->where($db->quoteName('access') . ' IN(:levels)')
                ->bind(':levels', $levels);

        $db->setQuery($query);
        return $db->loadObject(SequenceData::class);
    }

    public function getItems(Registry $params, string $levels, int $type)
    {
        $db = $this->getDatabase();
        $sequence = (int) $params->get('sequence');
        // build query
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__sequence_items'))
            // filter the sequence
            ->where($db->quoteName('sequence_id') . ' = :sequence')
                ->bind(':sequence', $sequence)
            // filter published
            ->where($db->quoteName('published') . ' = 1')
            // filter access
            ->where($db->quoteName('access') . ' IN(:levels)')
                ->bind(':levels', $levels);

        // ordering
        $ordering = $params->get('ordering', 'component');
        $direction = $this->getDirection($params->get('direction', 'component'), $type);

        switch ($ordering) {
            case 'random':
                $query->order('RAND()');
                break;
            case 'heading':
                $query->order("heading $direction");
                break;
            case 'component':
            default:
                if ($type === 1) {
                    // order by date
                    $query->order("date $direction");
                }
                else {
                    $query->order("ordering $direction");
                }
                break;
        }

        $db->setQuery($query);
        return $db->loadObjectList(ItemData::class);
    }

    private function getDirection(string $direction, int $type): string 
    {
        return match ($direction) {
            'ascending' => 'ASC',
            'descending' => 'DESC',
            'reverse' => $type === 1 ? 'ASC' : 'DESC',
            'component' => $type === 1 ? 'DESC' : 'ASC',
            default => ''
        };
    }
}