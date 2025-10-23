<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_carousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use ToKu\Library\Closure;
use ToKu\Library\Html;
use ToKu\Library\JooToKu;
use ToKu\Module\Carousel\Site\Helper\CarouselHelper;

\defined('_JEXEC') or die;

JooToKu::registerWebAssets(
    [CarouselHelper::MODULE],
    [JooToKu::getAsset('carousel')],
    [JooToKu::getAsset('style'), CarouselHelper::getAsset('style')]
);

/**
 * @var \Joomla\Registry\Registry $params Module parameters
 * @var array $items
 */

$equals = Closure::equals($params);
$isFalse = Closure::isFalse($params);
$isTrue = Closure::isTrue($params);
$param = Closure::param($params);

if (empty($items) || count($items) === 0) {
    echo '<!-- ' . CarouselHelper::MODULE . ' :: no items -->';
    return;
}

// create unique id
$carouselId = JooToKu::getUniqueId();
$indicators = $params->get('indicators');
?>

<div class="<?= JooToKu::getModuleClass(CarouselHelper::NAME); ?><?= $param('module_class'); ?>">

    <?= JooToKu::render('module.frame', [
        'name' => 'carousel',
        'type' => 'header',
        'text' => $params->get('module_header_text'),
        'position' => $params->get('module_header_position'),
        'src' => $params->get('module_header_image'),
        'alt' => $params->get('module_header_alt'),
        'css' => $params->get('module_header_css')
    ]); ?>

    <div id="<?= $carouselId; ?>" class="carousel slide"
        data-js="carousel-infinite"
        data-interval="<?= $params->get('interval'); ?>"
        data-autoplay="<?= Html::boolean($params->get('autoplay')); ?>"
        data-direction="<?= $params->get('direction'); ?>"
        data-indicators="<?= Html::boolean($indicators !== 'none'); ?>">

        <?php if ($indicators === 'above'): ?>
            <ol class="carousel-indicators" data-js="indicators">
                <?php foreach ($items as $_): ?>
                    <li class="carousel-indicator fas fa-circle"></li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>

        <div class="carousel-container" data-js="container">
            <?php foreach ($items as $item): ?>
                <?php
                    // prepare image data
                    $image = $isFalse('show_image') || empty($item->image)
                        ? false
                        : [
                            'src' => $item->image,
                            'alt' => empty($item->image_alt) ? false : $item->image_alt,
                        ];
                    ?>
                <div class="carousel-box <?= $params->get('box_class'); ?> <?= $item->class; ?>" data-js="box">
                    <div class="card">
                        <?php if ($params->get('show_image', 0) && isset($image)): ?>
                            <figure class="card-image">
                                <?= LayoutHelper::render('joomla.html.image', $image); ?>
                            </figure>
                        <?php endif; ?>
                        <div class="card-body">
                            <?php if (!empty($item->heading)): ?>
                                <h3 class="card-title"><?= htmlspecialchars($item->heading) ?></h3>
                            <?php endif; ?>
                            <?php if (!empty($item->text)): ?>
                                <div class="card-text"><?= HTMLHelper::_('content.prepare', $item->text); ?></div>
                            <?php endif; ?>
                            <?php if ($item->show_author): ?>
                                <div class="card-footer">
                                    <span class="author-name"><?= htmlspecialchars($item->author_name) ?></span>
                                    <span class="author-title"><?= htmlspecialchars($item->author_title) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($params->get('show_controls', 0)): ?>
            <div class="carousel-controls">
                <a href="#<?= $carouselId; ?>" role="button" data-js="prev" class="control-prev">
                    <span aria-hidden="true" class="fas fa-angle-left"></span>
                </a>
                <a href="#<?= $carouselId; ?>" role="button" data-js="next" class="control-next">
                    <span aria-hidden="true" class="fas fa-angle-right"></span>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($indicators == 'below'): ?>
            <ol class="carousel-indicators" data-js="indicators">
                <?php foreach ($items as $_): ?>
                    <li class="carousel-indicator fas fa-circle"></li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>

    <?= JooToKu::render('module.frame', [
        'name' => 'carousel',
        'type' => 'footer',
        'text' => $params->get('module_footer_text'),
        'position' => $params->get('module_footer_position'),
        'src' => $params->get('module_footer_image'),
        'alt' => $params->get('module_footer_alt'),
        'css' => $params->get('module_footer_css')
    ]); ?>
</div>