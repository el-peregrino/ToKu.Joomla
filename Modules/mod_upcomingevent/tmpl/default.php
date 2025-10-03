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
use ToKu\Library\Closures;
use ToKu\Library\JToKu;
use ToKu\Module\UpcomingEvent\Site\Helper\UpcomingEventHelper;

\defined('_JEXEC') or die;

JToKu::registerExtensionFile(UpcomingEventHelper::MODULE);
JToKu::useStyles(UpcomingEventHelper::getAsset('style'));

/**
 * @var \Joomla\Registry\Registry $params Module parameters
 * @var \stdClass $event
 */

$isTrue = Closures::isTrue($params);
$param = Closures::param($params);

if (empty($event)) {
    echo '<!-- ' . UpcomingEventHelper::MODULE . ' :: no events -->';
    return;
}

$date = new Date($event->value);
$link = Route::_('index.php?option=com_content&view=article&id=' . (int) $event->id);
// prepare image data
$images = json_decode($event->images);
$image = empty($images->image_intro)
    ? false
    : [
        'src' => $images->image_intro,
        'alt' => empty($images->image_intro_alt) ? false : $images->image_intro_alt
    ];
?>

<?= LayoutHelper::render('toku.module.frame', [
    'name' => 'upcoming-event',
    'type' => 'header',
    'text' => $params->get('module_header_text'),
    'position' => $params->get('module_header_position'),
    'src' => $params->get('module_header_image'),
    'alt' => $params->get('module_header_alt'),
    'css' => $params->get('module_header_css')
]); ?>

<div class="upcoming-event<?= $param('module_class'); ?>">
    <div class="event">
        <?php if ($isTrue('show_image') && $image): ?>
            <figure class="event-image">
                <?php if ($isTrue('link_image')): ?>
                    <a href="<?= htmlspecialchars($link) ?>" title="<?= htmlspecialchars($event->title) ?>">
                        <?= LayoutHelper::render('joomla.html.image', $image); ?>
                    </a>
                <?php else: ?>
                    <?= LayoutHelper::render('joomla.html.image', $image); ?>
                <?php endif; ?>
            </figure>
        <?php endif; ?>
        <div class="event-body">
            <div class="upcoming-event-header"><?= $params->get('event_header'); ?></div>
            <h3><a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($event->title) ?></a></h3>
            <div class="event-start"><?= $date->format($params->get('detetime_format', 'l, j. F Y H:i'), true); ?></div>
            <div class="event-intro"><?= HTMLHelper::_('content.prepare', $event->introtext); ?></div>

            <?php if ($isTrue('show_readmore')) {
                // override access view
                $params->set('access-view', true);
                // override alternative read more settings
                $event->alternative_readmore = false;
                echo LayoutHelper::render('joomla.content.readmore', ['item' => $event, 'params' => $params, 'link' => $link]);
            } ?>
        </div>
    </div>
</div>

<?= LayoutHelper::render('toku.module.frame', [
    'name' => 'upcoming-event',
    'type' => 'footer',
    'text' => $params->get('module_footer_text'),
    'position' => $params->get('module_footer_position'),
    'src' => $params->get('module_footer_image'),
    'alt' => $params->get('module_footer_alt'),
    'css' => $params->get('module_footer_css')
]); ?>