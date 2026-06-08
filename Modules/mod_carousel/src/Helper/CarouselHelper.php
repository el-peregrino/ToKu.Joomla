<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_carousel
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Carousel\Site\Helper;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use ToKu\Library\Joomlib;

\defined('_JEXEC') or die;

/**
 * Helper for mod_carousel
 */
class CarouselHelper
{
    // TYPED CONSTANTS ARE SUPPORTED SINCE PHP 8.3

    public const NAME = 'Carousel';
    public const MODULE = 'mod_carousel';

    public static function getAsset(string $name): string
    {
        return self::MODULE . ".$name";
    }

    /**
     * Retrieve carousel items
     * 
     * @param Registry                $params   The module parameters
     * @param CMSApplicationInterface $app      The application
     * 
     * @return array
     */
    public static function getItems(Registry $params, CMSApplicationInterface $app): array
    {
        $items = $params->get('items', []);
        $levels = $app->getIdentity()->getAuthorisedViewLevels();

        $output = [];

        foreach ($items as $item) {
            // check access
            if (!in_array((int) $item->access, $levels))
                continue;

            // check the data
            if (!Joomlib::hasAnyValue($item, 'image', 'heading', 'text'))
                continue;

            $output[] = $item;
        }

        return $output;
    }

    public static function getLinkUrl(CarouselData $item): string 
    {
        switch ($item->link_type) {
            case 'menu':
                $menu = Joomlib::getApp()->getMenu()->getItem($item->link_menu);
                return Route::_($menu->route);
            case 'article':
                return Route::_('index.php?option=com_content&view=article&id=' . (int) $item->link_article);
            case 'external':
                return $item->link_url;
            default:
                return null;
        }
    }

    public static function getImage(CarouselData $item): array|bool
    {
        switch ($item->item_type) {
            
            case 'carousel':
                return empty($item->carousel_image)
                    ? false
                    : [
                        'src' => $item->carousel_image,
                        'alt' => empty($item->carousel_image_alt) ? false : $item->carousel_image_alt,
                    ];

            case 'testimonial':
                return empty($item->testimonial_image)
                    ? false
                    : [
                        'src' => $item->testimonial_image,
                        'alt' => empty($item->testimonial_image_alt) ? false : $item->testimonial_image_alt,
                    ];

            default:
                return false;
        }
    }

    public static function isNotSupported(CarouselData $item): bool {
        return match ($item->item_type) {
            'carousel' => false,
            'testimonial' => false,
            default => true
        };
    }
}