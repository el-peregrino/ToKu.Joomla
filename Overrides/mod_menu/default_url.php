<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_menu
 *
 * This is override of the T4's default mod_menu template.
 * 
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

$attributes = array();
$attributes['itemprop'] = 'url';

if ($item->anchor_title)
{
	$attributes['title'] = $item->anchor_title;
}

// T4: add class nav-link
if ($item->anchor_css)
{
	if(($item->level > 1)) {
		$attributes['class'] = $item->anchor_css . ' dropdown-item';
	} else {
		$attributes['class'] = $item->anchor_css . ' nav-link';
	}
}else{
	if(($item->level > 1)) {
		$attributes['class'] = ' dropdown-item';
	} else {
		$attributes['class'] = ' nav-link';
	}
}

if (empty($item->caret)) $item->caret = '';

if ($item->anchor_rel)
{
	$attributes['rel'] = $item->anchor_rel;
}

$linktype = '<span class="menu-item-title">' . $item->title . '</span>';

// ToKu - added to show menu subtitle
if ($itemParams->get('menu_text', 1) && $itemParams->get('menu_subtitle')) {
	$linktype = '<span><span class="menu-item-title">' . $item->title . '</span><small class="menu-item-subtitle">' . $itemParams->get('menu_subtitle') . '</small></span>';
}

// ToKu - added to show the menu icon
if ($item->menu_icon && $itemParams->get('menu_enable_icon', 0)) {
	// the link is an icon
    if ($itemParams->get('menu_text', 1)) {
        // if the link text is to be displayed, the icon is added with aria-hidden
        $linktype = '<i class="menu-icon ' . $item->menu_icon . '" aria-hidden="true"></i>' . $linktype;
    } else {
        // if the icon itself is the link, it needs a visually hidden text
        $linktype = '<span class="menu-icon ' . $item->menu_icon . '" aria-hidden="true"></span><span class="visually-hidden">' . $item->title . '</span>';
    }
}
elseif ($item->menu_image)
{
	$itemParams = version_compare('4','ge') ? $item->getParams() : $item->params;

	if ($item->menu_image_css)
	{
		$image_attributes['class'] = $item->menu_image_css;
		$linktype = HTMLHelper::_('image', $item->menu_image, $item->title, $image_attributes);
	}
	else
	{
		$linktype = HTMLHelper::_('image', $item->menu_image, $item->title);
	}
	if ($itemParams->get('menu_text', 1))
	{
		$linktype .= '<span class="image-title">' . $item->title . '</span>';
	}
}

if ($item->browserNav == 1)
{
	$attributes['target'] = '_blank';
	$attributes['rel'] = 'noopener noreferrer';
}
elseif ($item->browserNav == 2)
{
	$options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,' . $params->get('window_open');

	$attributes['onclick'] = "window.open(this.href, 'targetWindow', '" . $options . "'); return false;";
}

if($item->deeper){
	if (!preg_match('/http/', $item->flink) && preg_match('/#/', $item->flink)) {
		$attributes['class'] .= ' dropdown-toggle anchoring';
	}else {
		$attributes['class'] .= ' dropdown-toggle';
	}
	$attributes['role'] = 'button';
	$attributes['aria-haspopup'] = 'true';
	$attributes['aria-expanded'] = 'false';
	$attributes['data-toggle'] = $params->get('jamegamenu') ? '' : 'dropdown';
}

$itemCaption = !empty($item->caption)  ? '<span class="menu-item-caption">' . $item->caption . '</span>'  : "";

$linktype = (!empty($item->icon)  ? $item->icon  : "") . $linktype . $itemCaption;

$linktype = '<span itemprop="name">'.$linktype.'</span>' . (!empty($item->caret)  ? $item->caret  : "");

echo HTMLHelper::_('link', OutputFilter::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)), $linktype, $attributes);
