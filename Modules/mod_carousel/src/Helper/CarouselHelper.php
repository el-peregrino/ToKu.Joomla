<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_carousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Carousel\Site\Helper;

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Helper for mod_upcomingevent
 *
 */
class CarouselHelper
{
    static function hasValue($input) {
        return isset($input) && !empty($input);
    }

    public static function getItems(Registry $params): array
    {
        $items = $params->get('items');
        $output = [];

        foreach ($items as $key => $item) {
            // check the data
            if (!self::hasValue($item->image) && !self::hasValue($item->heading) && !self::hasValue($item->text)) {
                continue;
            }

            $output[] = $item;
        }

        return $output;
    }
}