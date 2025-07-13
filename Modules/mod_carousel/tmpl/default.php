<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_carousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$wa = Factory::getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('mod_carousel');
$wa->useScript('mod_carousel.carousel');
$wa->useStyle('mod_carousel.style');

if (empty($items) || count($items) == 0) {
    echo '<!-- no items -->';
    return;
}

// create unique id
$carouselId = 'toku-' . uniqid();
$indicators = $params->get('show_indicators');
?>

<div id="<?= $carouselId; ?>" class="carousel slide <?= $params->get('module_class'); ?>" data-js="carousel-infinite" data-interval="<?= $params->get('interval'); ?>" data-autoplay="<?= $params->get('autoplay') ? "true" : "false"; ?>" data-direction="<?= $params->get('direction'); ?>" data-indicators="<?= $indicators != 'none' ? 'true' : 'false'; ?>">
    
    <?php if ($indicators == 'above') : ?>
        <ol class="carousel-indicators" data-js="indicators">
            <?php foreach ($items as $_) : ?>
                <li class="carousel-indicator fas fa-circle"></li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>

    <div class="carousel-container" data-js="container">
        <?php foreach ($items as $item) : ?>
            <?php // get link url
            switch ($item->link_type) {
                case 'menu':
                    $menu = Factory::getApplication()->getMenu()->getItem($item->menu_item);
                    $link = JRoute::_($menu->link);
                    break;
                case 'article':
                    $link = JRoute::_('index.php?option=com_content&view=article&id=' . (int) $item->article_id);
                    break;
                case 'external':
                    $link = $item->external_url;
                    break;
            }
            // prepare image data
            if (isset($item->image)) {
                $image = [
                    'src' => $item->image,
                    'alt' => $item->image_alt,
                ];
            }
            ?>
            <div class="carousel-box <?= $params->get('box_class'); ?> <?= $item->class; ?>" data-js="box">
                <div class="card">
                    <?php if (isset($image)) : ?>
                        <div class="card-image">
                            <?= LayoutHelper::render('joomla.html.image', $image); ?>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <?php if ($item->link_type == 'none' || empty($link)) : ?>
                            <?php if (!empty($item->heading)) : ?>
                                <h3 class="card-title"><?= htmlspecialchars($item->heading) ?></h3>
                            <?php endif; ?>
                            <?php if (!empty($item->text)) : ?>
                                <div class="card-text"><?= $item->text; ?></div>
                            <?php endif; ?>
                        <?php else : ?>
                            <?php if (!empty($item->heading)) : ?>
                                <h3 class="card-title">
                                    <a href="<?= htmlspecialchars($link) ?>" target="<?= $item->link_target ?: '_self' ?>" rel="<?= $item->link_target === '_blank' ? 'noopener' : '' ?>">
                                        <?= htmlspecialchars($item->heading) ?>
                                    </a>
                                </h3>
                            <?php endif; ?>
                            <?php if (!empty($item->text)) : ?>
                                <div class="card-text"><?= $item->text; ?></div>
                            <?php endif; ?>
                            <?php if ($params->get('show_readmore', 0)) : ?>
                                <a href="<?= htmlspecialchars($link) ?>" class="btn btn-primary">
                                    <?= Text::_('JGLOBAL_READ_MORE'); ?>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($params->get('show_controls', 0)) : ?>
        <div class="carousel-controls">
            <a href="#<?php echo $carouselId; ?>" role="button" data-js="prev" class="carousel-control-prev">
                <span aria-hidden="true" class="fas fa-angle-left"></span>
            </a>
            <a href="#<?php echo $carouselId; ?>" role="button" data-js="next" class="carousel-control-next">
                <span aria-hidden="true" class="fas fa-angle-right"></span>
            </a>
        </div>
    <?php endif; ?>
    
    <?php if ($indicators == 'below') : ?>
        <ol class="carousel-indicators" data-js="indicators">
            <?php foreach ($items as $_) : ?>
                <li class="carousel-indicator fas fa-circle"></li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</div>