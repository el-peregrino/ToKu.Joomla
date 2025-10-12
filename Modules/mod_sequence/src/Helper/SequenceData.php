<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Sequence\Site\Helper;

\defined('_JEXEC') or die;

class SequenceData
{
    public int $id;
    public int $type;
    public string $title;
    public string $header;
    public string $footer;
    public string $images;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}