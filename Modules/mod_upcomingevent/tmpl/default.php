<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_upcomingevent
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

$wa = Joomla\CMS\Factory::getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('mod_upcomingevent');
$wa->useStyle('mod_upcomingevent.style');

if (empty($event)) {
    echo '<!-- mod_upcomingevent :: no events -->';
    return;
}

$date = new Date($event->value);
$link = JRoute::_('index.php?option=com_content&view=article&id=' . (int) $event->id);
$images  = json_decode($event->images);
if (!empty($images->image_intro)) {
    $image = [
        'src' => $images->image_intro,
        'alt' => empty($images->image_intro_alt) && empty($images->image_intro_alt_empty) ? false : $images->image_intro_alt,
    ];
}
$class = $params->get('module_class', '');
?>
<div class="upcoming-event <?= $class; ?>">
    <div class="event">
        <?php if ($params->get('show_image', 0) && !empty($images->image_intro)) : ?>
            <figure class="event-image">
                <?php if ($params->get('link_image')) : ?>
                    <a href="<?= htmlspecialchars($link) ?>" title="<?= htmlspecialchars($event->title) ?>">
                        <?= LayoutHelper::render('joomla.html.image', $image); ?>
                    </a>
                <?php else : ?>
                    <?= LayoutHelper::render('joomla.html.image', $image); ?>
                <?php endif; ?>
            </figure>
        <?php endif; ?>
        <div class="event-body">
            <div class="upcoming-header"><?= $params->get('upcoming_header'); ?></div>        
            <h3><a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($event->title) ?></a></h3>
            <div class="event-start"><?= $date->format($params->get('detetime_format', 'l, j. F Y H:i'), true); ?></div>
            <div class="event-intro"><?= HTMLHelper::_('content.prepare', $event->introtext); ?></div>

            <?php if ($params->get('show_readmore', 0) == 1) {
                // override access view
                $params->set('access-view', true);
                // override alternative read more settings
                $event->alternative_readmore = false;
                echo LayoutHelper::render('joomla.content.readmore', ['item' => $event, 'params' => $params, 'link' => $link]);
            } ?>
        </div>
    </div>
</div>