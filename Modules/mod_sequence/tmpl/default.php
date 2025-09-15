<?php

namespace ToKu\Module\Sequence\Site\View;

use Joomla\CMS\Layout\LayoutHelper;
use ToKu\Library\JToKu;
use ToKu\Module\Sequence\Site\Helper\SequenceHelper;
use ToKu\Module\Sequence\Site\Helper\ViewData;

\defined('_JEXEC') or die;

JToKu::registerExtensionFile(SequenceHelper::MODULE);
JToKu::useStyles(SequenceHelper::MODULE . '.style');

/**
 * @var \Joomla\Registry\Registry $params Module parameters
 * @var \ToKu\Module\Sequence\Site\Helper\SequenceData $sequence
 * @var array $items
 */

$append = function(string $key) use ($params): string
{
    $value = $params->get($key, '');
    return $value ? " $value" : $value;
};

$boolean = fn(bool $value): string
    => $value ? 'true' : 'false';

$images = json_decode($sequence->images);
$selector = "sequence-$sequence->id";
?>

<div class="sq-sequence<?= $append('sequence_css'); ?>">
    <?php if ($params->get('show_title')): ?>
        <h2 class="sq-title"><?= htmlspecialchars($sequence->title); ?></h2>
    <?php endif; ?>

    <?= LayoutHelper::render('toku.sequence.frame', [
        'type' => 'header',
        'text' => $sequence->header,
        'position' => $images->header_position,
        'src' => $images->image_header,
        'alt' => $images->image_header_alt
    ]); ?>

    <div id="<?= $selector; ?>" class="sq-items-container sq-<?= $params->get('align'); ?>">
        <?php 
        $justify = '';
        /** @var \ToKu\Module\Sequence\Site\Helper\ItemData $item */
        foreach($items as $key => $item)
        {
            $view = new ViewData($params, $item);
            // js values
            $target = "$selector-$key";
            $toggle = [];
            // line justification
            $justify = $view->getJustification($justify);
            // prepare css styles
            $styles = ['sq-item', $justify, $view->css, $params->get('item_css', ''), 'col-12'];
            array_push($styles, $view->line === 'center' ? 'col-md-6' : 'col-md');
            if ($view->expandable) {
                array_push($styles, 'sq-expandable');
                $toggle = [
                    'data-bs-toggle' => 'collapse',
                    'data-bs-target' => "#$target",
                    'aria-controls' => $target,
                    'aria-expanded' => $boolean($view->expanded)
                ];
            }
            if (!$view->control) {
                array_push($styles, 'no-label');
            }

            $data = [
                'item' => $item,
                'justify' => $justify,
                'selector' => $selector,
                'styles' => $styles,
                'target' => $target,
                'toggle' => $toggle,
                'view' => $view
            ];

            if ($sequence->type === 1) {
                echo LayoutHelper::render('toko.sequence.time', $data);
            }
            else {
                echo LayoutHelper::render('toku.sequence.item', $data);
            }
        } ?>
    </div>
    
    <?= LayoutHelper::render('toku.sequence.frame', [
        'type' => 'footer',
        'text' => $sequence->footer,
        'position' => $images->footer_position,
        'src' => $images->image_footer,
        'alt' => $images->image_footer_alt
    ]); ?>
</div>
