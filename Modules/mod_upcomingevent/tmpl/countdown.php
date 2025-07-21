<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_upcomingevent
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use ToKu\Module\UpcomingEvent\Site\Helper\UpcomingEventHelper;
use ToKu\Library\JToKu;

\defined('_JEXEC') or die;

$wa = JToKu::wamRegister('mod_upcomingevent');
$wa->useScript('toku.countdown');
$wa->useStyle('mod_upcomingevent.style');

if (empty($event)) {
    echo '<!-- mod_upcomingevent :: no events -->';
    return;
}
$date = new Date($event->value);
$link = Route::_('index.php?option=com_content&view=article&id=' . (int) $event->id);
$images = json_decode($event->images);
if (!empty($images->image_intro)) {
    $image = [
        'src' => $images->image_intro,
        'alt' => empty($images->image_intro_alt) && empty($images->image_intro_alt_empty) ? false : $images->image_intro_alt,
    ];
}
$class = $params->get('module_class', '');
$labels = UpcomingEventHelper::getLabels($params->get('countdown_labels', ''), ['days', 'hours', 'minutes', 'seconds']);

?>
<div class="upcoming-event event-countdown <?= $class; ?>">
    <div data-js="countdown" data-countdown="<?= $event->value ?>" class="countdown">
        <div class="upcoming-header"><?= $params->get('upcoming_header'); ?></div>
        <div class="countdown-cont">
            <div class="countdown-block">
                <div data-js="countdown-day" class="countdown-digit">00</div>
                <div class="countdown-label"><?= $labels[0]; ?></div>
            </div>
            <div class="countdown-block">
                <div data-js="countdown-hour" class="countdown-digit">00</div>
                <div class="countdown-label"><?= $labels[1]; ?></div>
            </div>
            <div class="countdown-block">
                <div data-js="countdown-minute" class="countdown-digit">00</div>
                <div class="countdown-label"><?= $labels[2]; ?></div>
            </div>
            <div class="countdown-block">
                <div data-js="countdown-second" class="countdown-digit">00</div>
                <div class="countdown-label"><?= $labels[3]; ?></div>
            </div>
        </div>
        <div class="countdown-message" data-js="countdown-expired">
            <div class="countdown-text"><?= $params->get('expired_text'); ?></div>
        </div>
    </div>

    <div class="event">
        <?php if ($params->get('show_image', 0) && !empty($images->image_intro)): ?>
            <figure class="event-image">
                <?php if ($params->get('link_image')): ?>
                    <a href="<?= htmlspecialchars($link) ?>" title="<?= htmlspecialchars($event->title) ?>">
                        <?= LayoutHelper::render('joomla.html.image', $image); ?>
                    </a>
                <?php else: ?>
                    <?= LayoutHelper::render('joomla.html.image', $image); ?>
                <?php endif; ?>
            </figure>
        <?php endif; ?>
        <div class="event-body">
            <div class="event-start"><?= $date->format($params->get('detetime_format', 'l, j. F Y H:i'), true); ?></div>
            <h3><a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($event->title) ?></a></h3>
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