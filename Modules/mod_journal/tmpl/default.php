<?php

namespace ToKu\Module\Journal\Site\View;

use ToKu\Library\Closure;
use ToKu\Library\Html;
use ToKu\Library\Joomla;
use ToKu\Library\Joomlib;
use ToKu\Module\Journal\Site\Helper\JournalHelper;
use ToKu\Module\Journal\Site\Helper\RecordView;

\defined('_JEXEC') or die;

Joomlib::registerExtensionFile(JournalHelper::MODULE);
$wa = Joomlib::useStyles(JournalHelper::MODULE . '.style');

/**
 * @var \Joomla\Registry\Registry $params Module parameters
 * @var array $records
 */

$isTrue = Closure::isTrue($params);
$param = Closure::param($params);

if ($isTrue('module_style_enabled')) {
    $wa->addInlineStyle($params->get('module_style_css') ?? '');
}

$selector = uniqid('jex-');
?>

<div class="<?= Joomlib::getModuleClass(JournalHelper::NAME); ?><?= $param('module_class'); ?>">

    <?= Joomlib::render('module.frame', [
        'name' => 'journal',
        'type' => 'header',
        'text' => $params->get('module_header_text'),
        'position' => $params->get('module_header_position'),
        'src' => $params->get('module_header_image'),
        'alt' => $params->get('module_header_alt'),
        'css' => $params->get('module_header_css')
    ]); ?>

    <div class="jex-journal">
        
        <div id="<?= $selector; ?>" class="jex-records-container jex-record-<?= $params->get('align'); ?>">
            <?php 
            $justify = '';
            /** @var \ToKu\Module\Journal\Site\Helper\RecordData $record */
            foreach($records as $key => $record)
            {
                $view = new RecordView($params, $record);
                // js values
                $target = "$selector-$key";
                $toggle = [];
                // line justification
                $justify = $view->getJustification($justify);
                // prepare css styles
                $styles = ['jex-record', $justify, $view->css, $params->get('box_class', ''), 'col-12'];
                array_push($styles, $view->line === 'center' ? 'col-md-6' : 'col-md');
                if ($view->expandable) {
                    array_push($styles, 'jex-expandable');
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
                    'item' => $record,
                    'justify' => $justify,
                    'selector' => $selector,
                    'styles' => $styles,
                    'target' => $target,
                    'toggle' => $toggle,
                    'view' => $view
                ];

                echo Joomlib::render('journal.record', $data, JournalHelper::MODULE);
            } ?>
        </div>
    
    </div>

    <?= Joomlib::render('module.frame', [
        'name' => 'journal',
        'type' => 'footer',
        'text' => $params->get('module_footer_text'),
        'position' => $params->get('module_footer_position'),
        'src' => $params->get('module_footer_image'),
        'alt' => $params->get('module_footer_alt'),
        'css' => $params->get('module_footer_css')
    ]); ?>
</div>