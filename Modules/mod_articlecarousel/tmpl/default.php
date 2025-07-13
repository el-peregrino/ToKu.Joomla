<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_articlecarousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use ToKu\Library\JToKu;

$wa = JToKu::wamRegister('mod_articlecarousel');
$wa->useScript('toku.carousel');
$wa->useStyle('toku.style');
$wa->useStyle('mod_articlecarousel.style');

if (empty($articles) || count($articles) == 0) {
    echo '<!-- mod_articlecarousel :: no items -->';
    return;
}

// create unique id
$carouselId = 'toku-' . uniqid();
$indicators = $params->get('show_indicators');
?>

<div id="<?= $carouselId; ?>" class="carousel slide <?= $params->get('module_class'); ?>" data-js="carousel-infinite" data-interval="<?= $params->get('interval'); ?>" data-autoplay="<?= $params->get('autoplay') ? "true" : "false"; ?>" data-direction="<?= $params->get('direction'); ?>" data-indicators="<?= $indicators != 'none' ? 'true' : 'false'; ?>">
    
    <?php if ($indicators == 'above') : ?>
        <ol class="carousel-indicators" data-js="indicators">
            <?php foreach ($articles as $_) : ?>
                <li class="carousel-indicator fas fa-circle"></li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>

    <div class="carousel-container" data-js="container">
        <?php foreach ($articles as $article) : ?>
            <?php // get link url
            $link = JRoute::_('index.php?option=com_content&view=article&id=' . (int) $article->id);
            // prepare image data
            $images  = json_decode($article->images);
            if (!empty($images->image_intro)) {
                $image = [
                    'src' => $images->image_intro,
                    'alt' => empty($images->image_intro_alt) && empty($images->image_intro_alt_empty) ? false : $images->image_intro_alt,
                ];
            }
            ?>
            <div class="carousel-box <?= $params->get('box_class'); ?>" data-js="box">
                <div class="card">
                    <?php if ($params->get('show_image', 0) && isset($image)) : ?>
                        <figure class="card-image">
                            <?php if ($params->get('link_image', 0)) : ?>
                                <a href="<?= htmlspecialchars($link) ?>" title="<?= htmlspecialchars($article->title) ?>">
                                    <?= LayoutHelper::render('joomla.html.image', $image); ?>
                                </a>
                            <?php else : ?>
                                <?= LayoutHelper::render('joomla.html.image', $image); ?>
                            <?php endif; ?>
                        </figure>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h3 class="card-title"><a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($article->title) ?></a></h3>
                        <div class="card-text"><?= HTMLHelper::_('content.prepare', $article->introtext); ?></div>

                        <?php if ($params->get('show_readmore', 0)) {
                            // override access view
                            $params->set('access-view', true);
                            // override alternative read more settings
                            $article->alternative_readmore = false;
                            echo LayoutHelper::render('joomla.content.readmore', ['item' => $article, 'params' => $params, 'link' => $link]);
                        } ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($params->get('show_controls', 0)) : ?>
        <div class="carousel-controls">
            <a href="#<?php echo $carouselId; ?>" role="button" data-js="prev" class="control-prev">
                <span aria-hidden="true" class="fas fa-angle-left"></span>
            </a>
            <a href="#<?php echo $carouselId; ?>" role="button" data-js="next" class="control-next">
                <span aria-hidden="true" class="fas fa-angle-right"></span>
            </a>
        </div>
    <?php endif; ?>
    
    <?php if ($indicators == 'below') : ?>
        <ol class="carousel-indicators" data-js="indicators">
            <?php foreach ($articles as $_) : ?>
                <li class="carousel-indicator fas fa-circle"></li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</div>