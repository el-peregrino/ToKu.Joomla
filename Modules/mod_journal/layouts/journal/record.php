<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_journal
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

/**
 * Layout variables
 * -----------------
 * @var   array  $displayData  Array with all the given attributes for the journal record element.
 *                             Contains [format, item, justify, selector, styles, target, toggle, view]
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use ToKu\Library\Html;

\defined('_JEXEC') or die;

// retrieve values from $display data

$caret = ($displayData['justify'] === 'right') ? 'left' : 'right';
/** @var \ToKu\Module\Journal\Site\Helper\RecordData $item */
$item = $displayData['item'];
/** @var string $selector */
$selector = $displayData['selector'];
/** @var array $styles */
$styles = $displayData['styles'];
/** @var string $target */
$target = $displayData['target'];
/** @var array $toggle */
$toggle = $displayData['toggle'];
$attributes = implode(' ', array_map(fn($key, $value) => "$key=\"$value\"", array_keys($toggle), $toggle));
/** @var \ToKu\Module\Journal\Site\Helper\RecordView $view */
$view = $displayData['view'];

$collapsed = function() use ($view): string 
{
    return $view->collapsed ? ' collapsed' : '';
};

if ($item->timeline) {
    // show label when control enabled (label is not empty due to the time field)
    $label = $view->control;

    // format datetime
    $date = (new Date($item->date))->format($displayData['format'] ?: Text::_('DATE_FORMAT_LC2'));
}
else {
    // show label when control enabled and label is not empty
    $label = $view->control && (!empty($item->title) || !empty($item->subtitle));
    $date = null;
}

?>

<div id="<?= $view->uid; ?>" class="<?= implode(' ', array_filter($styles)); ?>">

    <div class="record-control<?= $collapsed(); ?>"<?= Html::append($attributes); ?>>
        <?php if ($view->control && $view->expandable) : ?>
            <i class="fa-solid fa-circle-plus" aria-hidden="true"></i>
            <i class="fa-solid fa-circle-minus" aria-hidden="true"></i>
        <?php elseif ($view->control) : ?>
            <i class="fa-solid fa-circle-dot" aria-hidden="true"></i>
        <?php else : ?>
            <i class="fa-solid fa-circle" aria-hidden="true"></i>
        <?php endif; ?>
    </div>

    <?php if ($label) : ?>
        <div class="record-label<?= $collapsed(); ?>"<?= Html::append($attributes); ?>>
            <?php if ($date) : ?>
                <span class="record-date"><?= htmlspecialchars($date); ?></span>
            <?php endif; ?>
            <?php if ($item->title) : ?>
                <span class="record-title"><?= htmlspecialchars($item->title); ?></span>
            <?php endif; ?>
            <?php if ($item->subtitle) : ?>
                <span class="record-subtitle">(<?= htmlspecialchars($item->subtitle); ?>)</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="record-card">

        <span class="record-arrow"><i class="fa-solid fa-caret-<?= $caret; ?>" aria-hidden="true"></i></span>
        <span class="record-caret"><i class="fa-solid fa-caret-up" aria-hidden="true"></i></span>

        <div class="record-header<?= $collapsed(); ?>"<?= Html::append($attributes); ?>>

            <?php if ($view->header !== false && $view->header['position'] === 'above'): ?>
                <figure class="record-image image-above">
                    <?= LayoutHelper::render('joomla.html.image', $view->header); ?>
                </figure>
            <?php endif; ?>

            <div class="record-heading<?= Html::append('has-icon', !!$view->icon); ?>">
                <?php if ($view->icon) : ?>
                    <i class="<?= $view->icon; ?> record-icon" aria-hidden="true"></i>
                <?php endif; ?>
                <div>
                    <h3 class="heading"><?= $item->heading; ?></h3>
                    <?php if ($item->subheading) : ?>
                        <span class="subheading"><?= $item->subheading; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($item->header) : ?>
                <div class="header-text">
                    <?= HTMLHelper::_('content.prepare', $item->header); ?>
                </div>
            <?php endif; ?>

            <?php if ($view->header !== false && $view->header['position'] === 'below'): ?>
                <figure class="record-image image-below">
                    <?= LayoutHelper::render('joomla.html.image', $view->header); ?>
                </figure>
            <?php endif; ?>
        </div>

        <?php if ($view->body) : ?>
            <div id="<?= $target; ?>"<?= Html::append("data-bs-parent=\"#$selector\"", $view->parent); ?> class="record-container<?= Html::append('collapse', $view->expandable); ?><?= Html::append('show', $view->expanded); ?>">
                <?php if ($item->body) : ?>
                    <div class="record-body">
                        <?= HTMLHelper::_('content.prepare', $item->body); ?>
                    </div>
                <?php endif; ?>
                <?php if ($view->link) : ?>
                    <div class="record-link">
                        <a href="<?= htmlspecialchars($view->link['url']) ?>"
                            target="<?= $view->link['target'] ?: '_self' ?>"
                            rel="<?= Html::noopener($view->link['target']); ?>">
                            <?= $view->link['text']; ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if ($item->footer || $view->footer): ?>
                    <div class="record-footer">
                        <?php if ($view->footer !== false && $view->footer['position'] === 'above'): ?>
                            <figure class="record-image image-above">
                                <?= LayoutHelper::render('joomla.html.image', $view->footer); ?>
                            </figure>
                        <?php endif; ?>
                        <?php if ($item->footer) : ?>
                            <div class="footer-text">
                                <?= HTMLHelper::_('content.prepare', $item->footer); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($view->footer !== false && $view->footer['position'] === 'below'): ?>
                            <figure class="record-image image-below">
                                <?= LayoutHelper::render('joomla.html.image', $view->footer); ?>
                            </figure>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>
<div class="clearfix"></div>