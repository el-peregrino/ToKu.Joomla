<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use ToKu\Library\Joomlib;

/**
 * The View class of the MVC pattern.
 * 
 * Note: 
 *      The properties of $this are injected into the template via the PHP extract.
 *      They are defined in the HtmlView class, but not used directly.
 * 
 * @var \ToKu\Component\JexContent\Administrator\View\Quote\HtmlView $this 
 */

/**
 * IGNORE ERROR PHP1416
 *      $this->form is injected via the PHP extract
 *      $this->item is injected via the PHP extract
 */

\defined('_JEXEC') or die;

Joomlib::useScripts('keepalive', 'form.validate');

HTMLHelper::_('script', 'system/toggle-help.js', ['version' => 'auto', 'relative' => true]);

?>

<form action="<?= Route::_('index.php?option=com_jexcontent&view=quote&layout=edit&id=' . (int) $this->item->id); ?>" method="post"
    name="adminForm" id="adminForm" class="form-validate">

    <div class="main-card">
        <div class="row">
            <div class="col-lg-9">
                <?= $this->form->renderFieldset('content'); ?>
            </div>
            <div class="col-lg-3">
                <?= $this->form->renderFieldset('general'); ?>
            </div>
        </div>
    </div>

    <?= $this->form->renderField('id', null, (int) $this->item->id); ?>
    <?= $this->form->renderControlFields(); ?>
</form>
