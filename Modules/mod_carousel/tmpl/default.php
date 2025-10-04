<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_carousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Layout\LayoutHelper;
use ToKu\Library\Closures;
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

// define helper closures

$isTrue = Closures::isTrue($params);
$param = Closures::param($params);

if (empty($items) || count($items) === 0) {
    echo '<!-- ' . CarouselHelper::MODULE . ' :: no items -->';
    return;
}

// create unique id
$carouselId = JooToKu::getUniqueId();
$indicators = $params->get('indicators');
?>

<?= LayoutHelper::render('toku.module.frame', [
    'name' => 'carousel',
    'type' => 'header',
    'text' => $params->get('module_header_text'),
    'position' => $params->get('module_header_position'),
    'src' => $params->get('module_header_image'),
    'alt' => $params->get('module_header_alt'),
    'css' => $params->get('module_header_css')
]); ?>

<div id="<?= $carouselId; ?>" class="carousel slide<?= $param('module_class'); ?>" 
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
            /** @var \ToKu\Module\Carousel\Site\Helper\CarouselData $item */
            if (CarouselHelper::isNotSupported($item))
            {
                // TODO remove once article, quote, and sequence support added
                continue; // not yet supported
            }
            // get link url
            $link = CarouselHelper::getLinkUrl($item);
            // prepare image data
            $image = CarouselHelper::getImage($item);
            ?>
            <div class="carousel-box<?= $param('box_class'); ?><?= Html::append($item->item_class); ?>" data-js="box">
                
                <?= LayoutHelper::render("toku.carousel.$item->item_type", [
                    'image' => $image, 
                    'item' => $item, 
                    'link' => $link, 
                    'params' => $params
                ]); ?>

            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($isTrue('show_controls')): ?>
        <div class="carousel-controls">
            <a href="#<?= $carouselId; ?>" role="button" data-js="prev" class="control-prev">
                <span aria-hidden="true" class="fas fa-angle-left"></span>
            </a>
            <a href="#<?= $carouselId; ?>" role="button" data-js="next" class="control-next">
                <span aria-hidden="true" class="fas fa-angle-right"></span>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($indicators === 'below'): ?>
        <ol class="carousel-indicators" data-js="indicators">
            <?php foreach ($items as $_): ?>
                <li class="carousel-indicator fas fa-circle"></li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</div>

<?= LayoutHelper::render('toku.module.frame', [
    'name' => 'carousel',
    'type' => 'footer',
    'text' => $params->get('module_footer_text'),
    'position' => $params->get('module_footer_position'),
    'src' => $params->get('module_footer_image'),
    'alt' => $params->get('module_footer_alt'),
    'css' => $params->get('module_footer_css')
]); ?>