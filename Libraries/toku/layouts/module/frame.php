<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  Joomlib
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

/**
 * Layout variables
 * -----------------
 * @var   array  $displayData  Array with all the given attributes for the module frame.
 *                             Contains [name, type, text, src, alt, position, css]
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use ToKu\Library\Html;

\defined('_JEXEC') or die;

// retrieve values from displayData
$name = strtolower(trim($displayData['name'])) ?: false;
$type = strtolower(trim($displayData['type'])) ?: 'block';
$text = empty($displayData['text']) ? false : $displayData['text'];
$image = empty($displayData['src']) ? false : [
    'src' => $displayData['src'],
    'alt' => empty($displayData['alt']) ? false : $displayData['alt']
];

if ($name === false || $text === false && ($displayData['position'] === 'none' || $image === false)) {
    // there is nothing to render
    return;
}
?>

<div class="module-<?= $name; ?> module-<?= $type; ?><?= Html::append($displayData['css']); ?>">
    <?php if ($displayData['position'] === 'above' && $image !== false): ?>
        <figure class="module-image image-above">
            <?= LayoutHelper::render('joomla.html.image', $image); ?>
        </figure>
    <?php endif; ?>
    <?php if ($text !== false): ?>
        <div class="module-text"><?= HTMLHelper::_('content.prepare', $text); ?></div>
    <?php endif; ?>    
    <?php if ($displayData['position'] === 'below' && $image !== false): ?>
        <figure class="module-image image-below">
            <?= LayoutHelper::render('joomla.html.image', $image); ?>
        </figure>
    <?php endif; ?>
</div>