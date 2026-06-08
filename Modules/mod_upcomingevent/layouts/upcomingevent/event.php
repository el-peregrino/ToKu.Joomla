<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_upcomingevent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

/**
 * Layout variables
 * -----------------
 * @var   array  $displayData  Array with all the given attributes for the upcoming event.
 *                             Contains [event, params, headline]
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use ToKu\Library\Closure;

\defined('_JEXEC') or die;

// retrieve values from $display data
/** @var \stdClass $event */
$event = $displayData['event'];
/** @var string|bool $headline */
$headline = $displayData['headline'] ?? false;
/** @var \Joomla\Registry\Registry $params */
$params = $displayData['params'];

$isTrue = Closure::isTrue($params);

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
        <?php if ($headline): ?>
            <div class="upcoming-event-headline"><?= htmlspecialchars($headline); ?></div>
        <?php endif; ?>
        <h3><a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($event->title) ?></a></h3>
        <div class="event-start"><?= $date->format($params->get('datetime_format', 'l, j. F Y H:i'), true); ?></div>
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