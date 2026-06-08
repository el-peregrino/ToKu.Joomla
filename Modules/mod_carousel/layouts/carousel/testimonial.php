<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_carousel
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

/**
 * Layout variables
 * -----------------
 * @var   array  $displayData  Array with all the given attributes for the carousel item element.
 *                             Contains [image, item, link, params]
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

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

?>

<div class="card">
    <?php if ($image): ?>
        <figure class="card-image">
            <?= LayoutHelper::render('joomla.html.image', $image); ?>
        </figure>
    <?php endif; ?>
    <div class="card-body">
        <?php if (!empty($item->testimonial_heading)): ?>
            <h3 class="card-title"><?= htmlspecialchars($item->testimonial_heading) ?></h3>
        <?php endif; ?>
        <?php if (!empty($item->testimonial_text)): ?>
            <div class="card-text"><?= HTMLHelper::_('content.prepare', $item->testimonial_text); ?></div>
        <?php endif; ?>
        <?php if ($item->testimonial_author_name): ?>
            <div class="card-footer">
                <span class="author-name"><?= htmlspecialchars($item->testimonial_author_name) ?></span>
                <span class="author-title"><?= htmlspecialchars($item->testimonial_author_title) ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>
