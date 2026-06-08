<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use ToKu\Library\Html;

/**
 * The View class of the MVC pattern.
 * 
 * Note: 
 *      The properties of $this are injected into the template via the PHP extract.
 *      They are defined in the HtmlView class, but not used directly.
 * 
 * @var \ToKu\Component\JexContent\Administrator\View\Quotes\HtmlView $this 
 */

/**
 * IGNORE ERROR PHP1416
 *      $this->items is injected via the PHP extract
 *      $this->state is injected via the PHP extract
 */

\defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDir  = $this->escape($this->state->get('list.direction'));

/**
 * Manual ordering is enabled only if the list is ordered by the ordering columns and there are items to order.
 * @var bool $manualOrder
 */
$manualOrder = $listOrder == 'r.ordering' && count($this->items) > 1;

?>

<form action="<?= Route::_('index.php?option=com_jexcontent&view=quotes'); ?>" method="post" name="adminForm" id="adminForm">
    <?= LayoutHelper::render('joomla.searchtools.default', ['view' => $this, 'options' => ['selectorFieldName' => 'catid']]); ?>

    <?php if (empty($this->items)): ?>
        <div class="alert alert-info">
            <?= Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><?= HTMLHelper::_('grid.checkall'); ?></th>
                    <th><?= Text::_('JGRID_HEADING_ORDERING'); ?></th>
                    <th><?= Text::_('JSTATUS'); ?></th>
                    <th><?= Text::_('COM_JEX_FIELD_AUTHOR'); ?></th>
                    <th><?= Text::_('JGLOBAL_TITLE'); ?></th>
                    <th><?= Text::_('JCATEGORY'); ?></th>
                    <th><?= Text::_('JGRID_HEADING_ACCESS'); ?></th>
                    <th><?= Text::_('JGRID_HEADING_LANGUAGE'); ?></th>
                    <th><?= Text::_('JGRID_HEADING_ID'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $i => $item): ?>
                    <tr>
                        <td><?= HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                        <td><?= $item->ordering; ?></td>
                        <td><?= HTMLHelper::_('jgrid.published', $item->published, $i, 'quotes.'); ?></td>
                        <td><a href="<?= Route::_('index.php?option=com_jexcontent&task=quote.edit&id=' . (int) $item->id); ?>"><?= $this->escape($item->author); ?></a></td>
                        <td><?= $this->escape($item->title); ?></td>
                        <td><?= $this->escape($item->category); ?></td>
                        <td><?= $this->escape($item->access_level); ?></td>
                        <td><?= $this->escape($item->language_title); ?></td>
                        <td><?= $item->id; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?= $this->filterForm->renderControlFields(); ?>
</form>
