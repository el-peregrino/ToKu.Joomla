<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

/**
 * Layout variables
 * -----------------
 * @var   array  $displayData  Array with all the given attributes for the sequence frame.
 *                             Contains [text, src, alt, position, type]
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

\defined('_JEXEC') or die;

$text = empty($displayData['text']) ? false : $displayData['text'];
$image = empty($displayData['src']) ? false : [
    'src' => $displayData['src'],
    'alt' => empty($displayData['alt']) ? false : $displayData['alt']
];

if ($text === false && ($displayData['position'] === 'none' || $image === false)) {
    // there is nothing to render
    return;
}

?>

<div class="sequence-<?= $displayData['type']; ?>">
    <?php if ($displayData['position'] === 'above' && $image !== false): ?>
        <figure class="sequence-image">                
            <?= LayoutHelper::render('joomla.html.image', $image); ?>
        </figure>
    <?php endif; ?>
    <?php if ($text !== false): ?>
        <div class="sequence-text"><?= HTMLHelper::_('content.prepare', $text); ?></div>
    <?php endif; ?>    
    <?php if ($displayData['position'] === 'below' && $image !== false): ?>
        <figure class="sequence-image">                
            <?= LayoutHelper::render('joomla.html.image', $image); ?>
        </figure>
    <?php endif; ?>
</div>