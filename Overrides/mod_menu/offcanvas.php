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

use Joomla\CMS\Helper\ModuleHelper;

$id = '';

if ($tagId = $params->get('tag_id', '')) {
    $id = ' id="' . htmlspecialchars($tagId, ENT_QUOTES, 'UTF-8') . '"';
}

// The menu class is deprecated. Use mod-menu instead
?>
<div class="mod-menu mod-list <?php echo $class_sfx; ?>">
<?php foreach ($list as $i => &$item) {
    $itemParams = $item->getParams();
    $class      = 'nav-item';

    if ($item->id == $default_id) {
        $class .= ' default';
    }

    if ($item->id == $active_id || ($item->type === 'alias' && $itemParams->get('aliasoptions') == $active_id)) {
        $class .= ' current';
    }

    if (in_array($item->id, $path)) {
        $class .= ' active';
    } elseif ($item->type === 'alias') {
        $aliasToId = $itemParams->get('aliasoptions');

        if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
            $class .= ' active';
        } elseif (in_array($aliasToId, $path)) {
            $class .= ' alias-parent-active';
        }
    }

    if ($item->type === 'separator') {
        $class .= ' divider';
    }

    if ($item->parent) {
        $class .= ' parent';
    }

    echo '<div class="' . $class . '">';

    switch ($item->type) :
        case 'separator':
        case 'component':
        case 'heading':
        case 'url':
            require ModuleHelper::getLayoutPath('mod_menu', 'offcanvas_' . $item->type);
            break;

        default:
            require ModuleHelper::getLayoutPath('mod_menu', 'offcanvas_url');
            break;
    endswitch;

    echo '</div>';

    // The next item is deeper.
    if ($item->deeper) {
        echo '<div class="sub-menu">';
    } elseif ($item->shallower) {
        // The next item is shallower.
        //echo '</div>';
        echo str_repeat('</div>', $item->level_diff);
    }
}
?></div>
