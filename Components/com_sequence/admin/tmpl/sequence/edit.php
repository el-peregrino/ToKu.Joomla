<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use ToKu\Library\JooToKu;

/**
 * The View class of the MVC pattern.
 * 
 * Note: 
 *      The properties of $this are injected into the template via the PHP extract.
 *      They are defined in the HtmlView class, but not used directly.
 * 
 * @var \ToKu\Component\Sequence\Administrator\View\Sequence\HtmlView $this 
 */

/**
 * IGNORE ERROR PHP1416
 *      $this->form is injected via the PHP extract
 *      $this->item is injected via the PHP extract
 */

\defined('_JEXEC') or die;

JooToKu::useScripts('keepalive', 'form.validate');

HTMLHelper::_('script', 'system/toggle-help.js', ['version' => 'auto', 'relative' => true]);

?>

<form action="<?= Route::_('index.php?option=com_sequence&view=sequence&layout=edit&id=' . (int) $this->item->id); ?>" method="post"
    name="adminForm" id="adminForm" class="form-validate">
    <div class="row title-alias form-vertical mb-3">
        <div class="col-12 col-md-6">
            <?= $this->form->renderField('title'); ?>
        </div>
        <div class="col-12 col-md-6">
            <?= $this->form->renderField('alias'); ?>
        </div>
    </div>

    <div class="main-card">

        <div class="row">
            <div class="col-lg-9">
                <?= HTMLHelper::_('uitab.startTabSet', 'myTabs', ['active' => 'header']); ?>
                    <?= HTMLHelper::_('uitab.addTab', 'myTabs', 'header', Text::_('COM_SQ_TAB_HEADER')); ?>

                    <div class="hide-aware-inline-help d-none"><?= Text::_('COM_SQ_HEADER_HELP'); ?></div>

                    <fieldset class="adminform m-3">
                        <?= $this->form->getLabel('header'); ?>
                        <?= $this->form->getInput('header'); ?>
                    </fieldset>

                    <fieldset id="fieldset-image-header" class="options-form">
                        <legend><?= Text::_('COM_SQ_HEADER_IMAGE'); ?></legend>
                        <div>
                        <?= $this->form->renderFieldset('image-header'); ?>
                        </div>
                    </fieldset>
                    <?= HTMLHelper::_('uitab.endTab'); ?>

                    <?= HTMLHelper::_('uitab.addTab', 'myTabs', 'footer', Text::_('COM_SQ_TAB_FOOTER')); ?>
                    
                    <div class="hide-aware-inline-help d-none"><?= Text::_('COM_SQ_FOOTER_HELP'); ?></div>
                    
                    <fieldset class="adminform m-3">
                        <?= $this->form->getLabel('footer'); ?>
                        <?= $this->form->getInput('footer'); ?>
                    </fieldset>

                    <fieldset id="fieldset-image-footer" class="options-form">
                        <legend><?= Text::_('COM_SQ_FOOTER_IMAGE'); ?></legend>
                        <div>
                        <?= $this->form->renderFieldset('image-footer'); ?>
                        </div>
                    </fieldset>
                    <?= HTMLHelper::_('uitab.endTab'); ?>

                <?= HTMLHelper::_('uitab.endTabSet'); ?>
            </div>
            <div class="col-lg-3">
                <fieldset class="form-vertical m-3">
                    <?= $this->form->renderFieldset('general'); ?>
                </fieldset>            
            </div>
        </div>

    </div>

    <?= $this->form->renderField('id', null, (int) $this->item->id); ?>
    <?= $this->form->renderControlFields(); ?>
</form>