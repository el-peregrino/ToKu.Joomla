<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_carousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Carousel\Site\Helper;

use Joomla\CMS\Application\SiteApplication;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

/**
 * Helper for mod_upcomingevent
 *
 */
class CarouselHelper
{
    private static function hasValue($input)
    {
        return isset($input) && !empty($input);
    }

    public static function getItems(Registry $params, SiteApplication $app): array
    {
        $items = $params->get('items');
        $levels = $app->getIdentity()->getAuthorisedViewLevels();

        $output = [];

        foreach ($items as $key => $item) {
            // check access
            if (!in_array((int) $item->access, $levels))
                continue;

            // check the data
            if (!self::hasValue($item->image) && !self::hasValue($item->heading) && !self::hasValue($item->text))
                continue;

            $output[] = $item;
        }

        return $output;
    }
}