<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_journal
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Journal\Site\Helper;

\defined('_JEXEC') or die;

class RecordData
{
    public int $id;
    public string $title;
    public string $subtitle;
    public string $heading;
    public string $subheading;
    public string $header;
    public string $body;
    public string $footer;
    public int $timeline;
    public string $date;
    public string $images;
    public string $links;
    public ?string $params;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
