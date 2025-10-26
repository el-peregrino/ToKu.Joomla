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

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

$title      = $item->anchor_title ? ' title="' . $item->anchor_title . '"' : '';
$anchor_css = $item->anchor_css ?: '';

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

if ($item->level > 1) {
	$anchor_css .= " dropdown-item";
} else {
	$anchor_css .= " nav-link";
}

$attributes = '';

if($item->deeper){
	if(isset($item->mega_sub)){
		$anchor_css .= ' dropdown-toggle';
	}
	$attributes .= ' role = "button"';
	$attributes .= ' aria-haspopup = "true"';
	$attributes .= ' aria-expanded = "false"';
	$attributes .= $params->get('jamegamenu') ? '' : ' data-toggle = "dropdown"';
}

$itemCaption = !empty($item->caption)  ? '<span class="menu-item-caption">' . $item->caption . '</span>'  : "";
$linktype = (!empty($item->icon)  ? $item->icon  : "") . $linktype  . $itemCaption;

?>
<a itemprop="url" href="javascript:;" class="nav-header <?php echo $anchor_css; ?>"<?php echo $title; ?> <?php echo $attributes; ?>>
	<span itemprop="name"><?php echo $linktype; ?></span>
	<?php echo !empty($item->caret)  ? $item->caret  : "" ?>
</a>
