<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_menu
 *
 * This is override of the Joomla's default mod_menu template. It flattens the menu tree into a list.
 * 
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Filter\OutputFilter;

$attributes = [];

if ($item->anchor_title) {
    $attributes['title'] = $item->anchor_title;
}

if ($item->anchor_css) {
    $attributes['class'] = $item->anchor_css;
}

if ($item->anchor_rel) {
    $attributes['rel'] = $item->anchor_rel;
}

$linktype = '<span class="menu-item-title">' . $item->title . '</span>';

// ToKu - added to show menu subtitle
if ($itemParams->get('menu_text', 1) && $itemParams->get('menu_subtitle')) {
	$linktype = '<span><span class="menu-item-title">' . $item->title . '</span><small class="menu-item-subtitle">' . $itemParams->get('menu_subtitle') . '</small></span>';
}

// ToKu - ignores the JEX menu enable icon
if ($item->menu_icon) {
    // ToKu - ignores the menu text flag
    // if the link text is to be displayed, the icon is added with aria-hidden
    $linktype = '<i class="menu-icon ' . $item->menu_icon . '" aria-hidden="true"></i>' . $linktype;
} elseif ($item->menu_image) {
    // The link is an image, maybe with an own class
    $image_attributes = [];

    if ($item->menu_image_css) {
        $image_attributes['class'] = $item->menu_image_css;
    }

    $linktype = HTMLHelper::_('image', $item->menu_image, $item->title, $image_attributes);

    if ($itemParams->get('menu_text', 1)) {
        $linktype .= '<span class="image-title">' . $item->title . '</span>';
    }
}

if ($item->browserNav == 1) {
    $attributes['target'] = '_blank';
    $attributes['rel'] = 'noopener noreferrer';

    if ($item->anchor_rel == 'nofollow') {
        $attributes['rel'] .= ' nofollow';
    }
} elseif ($item->browserNav == 2) {
    $options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,' . $params->get('window_open');

    $attributes['onclick'] = "window.open(this.href, 'targetWindow', '" . $options . "'); return false;";
}

echo HTMLHelper::_('link', OutputFilter::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)), $linktype, $attributes);
