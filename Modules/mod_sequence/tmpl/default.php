<?php

namespace ToKu\Module\Sequence\Site\View;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\Registry\Registry;
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

<div class="toku-timeline tl-<?php echo $data->line; ?>">
    <div id="<?php echo $data->id; ?>">
        <?php 
        foreach ($data->groups as $group) {

            $caret = ($group->justify == 'right') ? 'left' : 'right';
            
            foreach ($group->items as $key => $item) {

                $target = $data->id . $group->indices[$key];

                $css = array('tl-item', $group->justify, $item->data->css, 'col-12');
                if ($data->line == 'center') {
                    array_push($css, 'col-md-6');
                } else {
                    array_push($css, 'col-md');
                }

                $toggle = '';
                if ($item->expandable) {
                    $toggle = ' data-bs-toggle="collapse" data-bs-target="#' . $target .'" aria-controls="'. $target .'" aria-expanded="' . ($item->expanded ? "true" : "false") . '" ';
                    array_push($css, 'tl-expandable');
                }

                if ($key > 0) {
                    array_push($css, 'no-time');
                }

                // print html
                ?>
                <div class="<?php echo implode(' ', array_filter($css)); ?>">
                    <div class="tl-control<?php if ($item->collapsed) echo ' collapsed'; ?>"<?php echo $toggle; ?>>
                        <?php if ($key == 0 && $item->expandable) : ?>
                            <i class="fa-solid fa-circle-plus" aria-hidden="true"></i>
                            <i class="fa-solid fa-circle-minus" aria-hidden="true"></i>
                        <?php elseif ($key == 0) : ?>
                            <i class="fa-solid fa-circle-dot" aria-hidden="true"></i>
                        <?php else : ?>
                            <i class="fa-solid fa-circle" aria-hidden="true"></i>
                        <?php endif; ?>
                    </div>
                    <?php if ($key == 0) : ?>
                        <div class="tl-time<?php if ($item->collapsed) echo ' collapsed'; ?>"<?php echo $toggle; ?>>
                            <span><?php echo $item->data->time; ?></span>
                            <?php if ($item->data->duration) : ?>
                                <span>(<?php echo $item->data->duration; ?>)</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="tl-body">
                        <i class="fa-solid fa-caret-<?php echo $caret; ?> tl-arrow" aria-hidden="true"></i>
                        <i class="fa-solid fa-caret-up tl-caret" aria-hidden="true"></i>
                        <div class="tl-body-header<?php if ($item->collapsed) echo ' collapsed'; ?><?php if ($item->data->icon) echo ' has-icon'; ?>" <?php echo $toggle; ?>>
                            <?php if ($item->data->icon) : ?>
                                <i class="<?php echo $item->data->icon; ?> tl-icon" aria-hidden="true"></i>
                            <?php endif; ?>
                            <div>
                                <h3>
                                    <?php echo $item->data->title; ?>
                                </h3>
                                <?php if ($item->data->subtitle) : ?>
                                    <span class="tl-subtitle"><?php echo $item->data->subtitle; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($item->body) : ?>
                            <div id="<?php echo $target; ?>"<?php if ($item->parent) echo ' data-bs-parent="#' . $data->id .'"'; ?> class="tl-body-content<?php if ($item->expandable) echo ' collapse'; if ($item->expanded) echo ' show'; ?>">
                                <?php echo $item->data->description; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php
            }
        }
        ?>
    </div>
</div>
