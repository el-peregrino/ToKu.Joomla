<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\Sequence\Site\Helper;

use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use ToKu\Library\JooToKu;

\defined('_JEXEC') or die;

class ViewData
{
    /**
     * True indicates the sequence items are collapsible.
     * @var bool
     */
    public readonly bool $collapsible;
    /**
     * The expansion mode of the sequence tree.
     * Indicates how many items can be expanded (single or multiple).
     * @var string
     */
    public readonly string $mode;
    /**
     * Position of the sequence line (center, left, right).
     * @var string
     */
    public readonly string $line;
    /**
     * True indicates that the expansion control is enabled.
     * @var bool
     */
    public readonly bool $control;
    /**
     * True indicates that the item cannot be collapsed.
     * @var bool
     */
    public readonly bool $keep;
    /**
     * True indicates that the item has content and can be expanded.
     * @var bool
     */
    public readonly bool $expandable;
    /**
     * Indicates the initial state of the item is collapsed.
     * @var bool
     */
    public readonly bool $collapsed;
    /**
     * Indicates the initial state of the item is expanded.
     * @var bool
     */
    public readonly bool $expanded;
    /**
     * True indicates that the expansion mode is single and parent is needed.
     * @var bool
     */
    public readonly bool $parent;
    public readonly string $icon;
    public readonly string $css;
    public readonly array|bool $link;
    public readonly bool $body;
    public readonly array|bool $header;
    public readonly array|bool $footer;


    public function __construct(Registry $params, ItemData $item)
    {
        $this->collapsible = $params->get('collapsible', false);
        $this->mode = $params->get('mode', 'single');
        $this->line = $params->get('align');
        
        // parse options
        $options = new Registry($item->params);
        $this->control = $options->get('control', 1);
        $this->keep = $this->collapsible && $options->get('keep', 0);
        $this->icon = $options->get('icon');
        $this->icon = $options->get('css');
        // parse links
        $links = new Registry($item->links);
        $url = self::getLinkUrl($links);
        $this->link = empty($url) || empty($links->get('link_text'))
            ? false
            : [
                'url' => $url,
                'text' => $links->get('link_text'),
                'target' => $links->get('link_target')
        ];
        $this->link_text = $links->get('link_text');

        // parse images
        $images = new Registry($item->images);
        $this->header = empty($images->get('image_header')) || $images->get('header_position') === 'none'
            ? false
            : [
                'position' => $images->get('header_position'),
                'src' => $images->get('image_header'),
                'alt' => empty($images->get('image_header_alt')) ? false : $images->get('image_header_alt')
        ];
        $this->footer = empty($images->get('image_footer')) || $images->get('footer_position') === 'none'
            ? false
            : [
                'position' => $images->get('footer_position'),
                'src' => $images->get('image_footer'),
                'alt' => empty($images->get('image_footer_alt')) ? false : $images->get('image_footer_alt')
        ];

        // has body content
        $this->body = $item->body || $this->link || $item->footer || $this->footer;
        
        $this->expandable = $this->body && $this->collapsible && !$this->keep;
        $this->collapsed = $this->expandable && !$options->get('initial', false);
        $this->expanded = $this->expandable && $options->get('initial', false);
        $this->parent = $this->expandable && $this->mode === 'single';
    }

    protected static function getLinkUrl(Registry $links): string
    {
        switch ($links->get('link_type')) {
            case 'menu':
                $menu = JooToKu::getApp()->getMenu()->getItem($links->get('menu_item'));
                return Route::_($menu->route);
            case 'article':
                return Route::_('index.php?option=com_content&view=article&id=' . (int) $links->get('article_id'));
            case 'external':
                return $links->get('external_url');
            default:
                return null;
        }
    }

    public function getJustification(string $justify): string
    {
        if ($this->line === 'left') {
            return 'right';
        }

        if ($this->line === 'right') {
            return 'left';
        }
        
        if (!$this->control) {
            return $justify ?: 'right';
        }

        return ($justify === 'right') ? 'left' : 'right';
    }
}
