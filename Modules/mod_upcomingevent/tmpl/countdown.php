<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_upcomingevent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use ToKu\Library\Closure;
use ToKu\Library\Joomla;
use ToKu\Library\Joomlib;
use ToKu\Module\UpcomingEvent\Site\Helper\UpcomingEventHelper;

\defined('_JEXEC') or die;

Joomlib::registerWebAssets(
    [UpcomingEventHelper::MODULE],
    [Joomlib::getAsset('countdown')],
    [UpcomingEventHelper::getAsset('style')]
);

/**
 * @var \Joomla\Registry\Registry $params Module parameters
 * @var \stdClass $event
 */

$param = Closure::param($params);

if (empty($event)) {
    echo '<!-- ' . UpcomingEventHelper::MODULE . ' :: no events -->';
    return;
}

$labels = UpcomingEventHelper::getLabels($params->get('countdown_labels', ''));

?>

<div class="<?= Joomlib::getModuleClass(UpcomingEventHelper::NAME); ?><?= $param('module_class'); ?>">

    <?= Joomlib::render('module.frame', [
        'name' => 'upcoming-event',
        'type' => 'header',
        'text' => $params->get('module_header_text'),
        'position' => $params->get('module_header_position'),
        'src' => $params->get('module_header_image'),
        'alt' => $params->get('module_header_alt'),
        'css' => $params->get('module_header_css')
    ]); ?>

    <div class="upcoming-event event-countdown">
        <div class="upcoming-event-headline"><?= htmlspecialchars($params->get('headline')); ?></div>
        <?= Joomlib::render('upcomingevent.countdown', [
            'countdown' => $event->value,
            'expired' => $params->get('expired_text'),
            'labels' => $labels
        ], UpcomingEventHelper::MODULE); ?>

        <?= Joomlib::render('upcomingevent.event', [
            'event' => $event,
            'params' => $params,
            'headline' => null
        ], UpcomingEventHelper::MODULE); ?>
    </div>

    <?= Joomlib::render('module.frame', [
        'name' => 'upcoming-event',
        'type' => 'footer',
        'text' => $params->get('module_footer_text'),
        'position' => $params->get('module_footer_position'),
        'src' => $params->get('module_footer_image'),
        'alt' => $params->get('module_footer_alt'),
        'css' => $params->get('module_footer_css')
    ]); ?>
</div>