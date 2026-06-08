<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_upcomingevent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use ToKu\Library\Closure;
use ToKu\Library\Joomlib;
use ToKu\Module\UpcomingEvent\Site\Helper\UpcomingEventHelper;

\defined('_JEXEC') or die;

Joomlib::registerExtensionFile(UpcomingEventHelper::MODULE);
Joomlib::useStyles(UpcomingEventHelper::getAsset('style'));

/**
 * @var \Joomla\Registry\Registry $params Module parameters
 * @var \stdClass $event
 */

$param = Closure::param($params);

if (empty($event)) {
    echo '<!-- ' . UpcomingEventHelper::MODULE . ' :: no events -->';
    return;
}

?>

<div class="<?= Joomlib::getModuleClass(UpcomingEventHelper::NAME); ?><?= $param('module_class'); ?>">

    <?= Joomlib::render('toku.module.frame', [
        'name' => 'upcoming-event',
        'type' => 'header',
        'text' => $params->get('module_header_text'),
        'position' => $params->get('module_header_position'),
        'src' => $params->get('module_header_image'),
        'alt' => $params->get('module_header_alt'),
        'css' => $params->get('module_header_css')
    ]); ?>

    <div class="upcoming-event">
        <?= Joomlib::render('toku.upcomingevent.event', [
            'event' => $event,
            'params' => $params,
            'headline' => $params->get('headline')
        ], UpcomingEventHelper::MODULE); ?>
    </div>

    <?= Joomlib::render('toku.module.frame', [
        'name' => 'upcoming-event',
        'type' => 'footer',
        'text' => $params->get('module_footer_text'),
        'position' => $params->get('module_footer_position'),
        'src' => $params->get('module_footer_image'),
        'alt' => $params->get('module_footer_alt'),
        'css' => $params->get('module_footer_css')
    ]); ?>
</div>