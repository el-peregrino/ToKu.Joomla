<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_carousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

/**
 * Layout variables
 * -----------------
 * @var   array  $displayData  Array with all the given attributes for the carousel item element.
 *                             Contains [image, item, link, params]
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use ToKu\Library\Closure;
use ToKu\Library\Html;

\defined('_JEXEC') or die;

// retrieve values from $display data

/** @var array|bool */
$image = $displayData['image'];
/** @var \ToKu\Module\Carousel\Site\Helper\CarouselData */
$item = $displayData['item'];
/** @var string */
$link = $displayData['link'];
/** @var \Joomla\Registry\Registry $params */
$params = $displayData['params'];

// define helper closures

$equals = Closure::equals($params);
$isTrue = Closure::isTrue($params);
?>

<?php if ($equals('link_style', 'card') && !empty($link)): // open the anchor tag ?>
    <a href="<?= htmlspecialchars($link) ?>" target="<?= $item->link_target ?: '_self' ?>"
        rel="<?= Html::noopener($item->link_target); ?>">
<?php endif; ?>

    <div class="card">
        <?php if ($image): ?>
            <figure class="card-image">
                <?php if ($equals('link_style', 'title') && $isTrue('link_image') && !empty($link)): ?>
                    <a href="<?= htmlspecialchars($link) ?>" title="<?= htmlspecialchars($item->carousel_heading) ?>">
                        <?= LayoutHelper::render('joomla.html.image', $image); ?>
                    </a>
                <?php else: ?>
                    <?= LayoutHelper::render('joomla.html.image', $image); ?>
                <?php endif; ?>
            </figure>
        <?php endif; ?>

        <div class="card-body">
            <?php if (empty($link) || $equals('link_style', 'card')): ?>
                <?php if (!empty($item->carousel_heading)): ?>
                    <h3 class="card-title"><?= htmlspecialchars($item->carousel_heading) ?></h3>
                <?php endif; ?>
                <?php if (!empty($item->carousel_subheading)): ?>
                    <h4 class="card-subtitle"><?= htmlspecialchars($item->carousel_subheading) ?></h4>
                <?php endif; ?>
                <?php if (!empty($item->carousel_text)): ?>
                    <div class="card-text"><?= HTMLHelper::_('content.prepare', $item->carousel_text); ?></div>
                <?php endif; ?>
            <?php else: ?>
                <?php if (!empty($item->carousel_heading)): ?>
                    <h3 class="card-title">
                        <a href="<?= htmlspecialchars($link) ?>" target="<?= $item->link_target ?: '_self' ?>"
                            rel="<?= Html::noopener($item->link_target); ?>">
                            <?= htmlspecialchars($item->carousel_heading) ?>
                        </a>
                    </h3>
                <?php endif; ?>
                <?php if (!empty($item->carousel_subheading)): ?>
                    <h4 class="card-subtitle"><?= htmlspecialchars($item->carousel_subheading) ?></h4>
                <?php endif; ?>
                <?php if (!empty($item->carousel_text)): ?>
                    <div class="card-text"><?= HTMLHelper::_('content.prepare', $item->carousel_text); ?></div>
                <?php endif; ?>
                <?php if ($isTrue('show_readmore')): ?>
                    <a href="<?= htmlspecialchars($link) ?>" class="btn btn-primary">
                        <?= Text::_('JGLOBAL_READ_MORE'); ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>

<?php if ($equals('link_style', 'card')): // close the anchor tag ?>
    </a>
<?php endif; ?>