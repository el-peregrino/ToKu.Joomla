<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\Sequence\Administrator\Helper;

use Joomla\CMS\Language\Text;

class SequenceHelper {

    protected static array $sequenceTypes = [ 
        0 => 'COM_SQ_TYPE_LIST', 
        1 => 'COM_SQ_TYPE_TIMELINE'
    ];

    public static function getSequenceType(int $type): string 
    {
        return Text::_(self::$sequenceTypes[$type] ?? '');
    }
}