<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_carousel
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Carousel\Site\Helper;

\defined('_JEXEC') or die;

class CarouselData
{
    /**
     * Carousel content type.
     * @var string
     */
    public $item_type;

    /**
     * Heading of the carousel item.
     * @var string
     */
    public $carousel_heading;
    
    /**
     * Subheading of the carousel item.
     * @var string
     */
    public $carousel_subheading;
    
    /**
     * Body of the carousel item.
     * @var string
     */
    public $carousel_text;
    
    /**
     * Carousel image url.
     * @var string
     */
    public $carousel_image;
    
    /**
     * Image alt attribute.
     * @var string
     */
    public $carousel_image_alt;

    /**
     * Heading of the testimonial item.
     * @var string
     */
    public $testimonial_heading;
    
    /**
     * Body of the testimonial item.
     * @var string
     */
    public $testimonial_text;
    
    /**
     * Testimonial image url.
     * @var string
     */
    public $testimonial_image;
    
    /**
     * Image alt attribute.
     * @var string
     */
    public $testimonial_image_alt;

    /**
     * Name of the testimonial author.
     * @var string
     */
    public $testimonial_author_name;

    /**
     * Title (or position) of the testimonial author.
     * @var string
     */
    public $testimonial_author_title;

    /**
     * Type of the link.
     * @var string
     */
    public $link_type;

    /**
     * Id of the linked menu item.
     * @var int
     */
    public $link_menu;

    /**
     * Id of the linked article.
     * @var int
     */
    public $link_article;
    
    /**
     * Url of the external link.
     * @var string
     */
    public $link_url;

    /**
     * Link target window.
     * @var string
     */
    public $link_target;

    /**
     * Additional CSS styles.
     * @var string
     */
    public $item_class;
}