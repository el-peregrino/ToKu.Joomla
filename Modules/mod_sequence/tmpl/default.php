<?php

namespace ToKu\Module\Sequence\Site\View;

use ToKu\Library\Closure;
use ToKu\Library\Html;
use ToKu\Library\Joomla;
use ToKu\Library\JooToKu;
use ToKu\Module\Sequence\Site\Helper\SequenceHelper;
use ToKu\Module\Sequence\Site\Helper\ViewData;

\defined('_JEXEC') or die;

JooToKu::registerExtensionFile(SequenceHelper::MODULE);
$wa = JooToKu::useStyles(SequenceHelper::MODULE . '.style');

/**
 * @var \Joomla\Registry\Registry $params Module parameters
 * @var \ToKu\Module\Sequence\Site\Helper\SequenceData $sequence
 * @var array $items
 */

$isTrue = Closure::isTrue($params);
$param = Closure::param($params);

if ($isTrue('module_style_enabled')) {
    $wa->addInlineStyle($params->get('module_style_css') ?? '');
}

$images = json_decode($sequence->images);
$selector = JooToKu::getUniqueId();
?>

<div class="<?= JooToKu::getModuleClass(SequenceHelper::NAME); ?><?= $param('module_class'); ?>">

    <?= JooToKu::render('module.frame', [
        'name' => 'sequence',
        'type' => 'header',
        'text' => $isTrue('keep_header_text') ? $sequence->header : $params->get('module_header_text'),
        'position' => $isTrue('keep_header_image') ? $images->header_position : $params->get('module_header_position'),
        'src' => $isTrue('keep_header_image') ? $images->image_header : $params->get('module_header_image'),
        'alt' => $isTrue('keep_header_image') ? $images->image_header_alt : $params->get('module_header_alt'),
        'css' => $params->get('module_header_css')
    ]); ?>

    <div class="sq-sequence">
        
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
                $styles = ['sq-item', $justify, $view->css, $params->get('box_class', ''), 'col-12'];
                array_push($styles, $view->line === 'center' ? 'col-md-6' : 'col-md');
                if ($view->expandable) {
                    array_push($styles, 'sq-expandable');
                    $toggle = [
                        'data-bs-toggle' => 'collapse',
                        'data-bs-target' => "#$target",
                        'aria-controls' => $target,
                        'aria-expanded' => Html::boolean($view->expanded)
                    ];
                }
            
                if (!$view->control) {
                    array_push($styles, 'no-label');
                }

                $data = [
                    'format' => $params->get('datetime_format'),
                    'item' => $item,
                    'justify' => $justify,
                    'selector' => $selector,
                    'styles' => $styles,
                    'target' => $target,
                    'toggle' => $toggle,
                    'view' => $view
                ];

                if ($sequence->type === 1) {
                    echo JooToKu::render('sequence.time', $data, SequenceHelper::MODULE);
                }
                else {
                    echo JooToKu::render('sequence.item', $data, SequenceHelper::MODULE);
                }
            } ?>
        </div>
    
    </div>

    <?= JooToKu::render('module.frame', [
        'name' => 'sequence',
        'type' => 'footer',
        'text' => $isTrue('keep_footer_text') ? $sequence->footer : $params->get('module_footer_text'),
        'position' => $isTrue('keep_footer_image') ? $images->footer_position : $params->get('module_footer_position'),
        'src' => $isTrue('keep_footer_image') ? $images->image_footer : $params->get('module_footer_image'),
        'alt' => $isTrue('keep_footer_image') ? $images->image_footer_alt : $params->get('module_footer_alt'),
        'css' => $params->get('module_footer_css')
    ]); ?>
</div>