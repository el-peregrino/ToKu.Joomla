<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Categories\CategoryFactory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use ToKu\Component\JexContent\Administrator\Helper\RecordHelper;
use ToKu\Library\Html;

/**
 * The View class of the MVC pattern.
 * 
 * Note: 
 *      The properties of $this are injected into the template via the PHP extract.
 *      They are defined in the HtmlView class, but not used directly.
 * 
 * @var \ToKu\Component\JexContent\Administrator\View\Records\HtmlView $this 
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
$manualOrder = $listOrder == 's.ordering' && count($this->items) > 1;

/**
 * Table attributes to support manual (drag and drop) ordering.
 * @var string $attributes
 */
$attributes = '';
if ($manualOrder) {
    // draggable list support
    HTMLHelper::_('draggablelist.draggable');

    $draggable = [
        'class' => 'js-draggable',
        'data-url' => 'index.php?option=com_jexcontent&task=records.saveOrderAjax&' . Session::getFormToken() . '=1',
        'data-direction' => strtolower($listDir),
        'data-nested' => 'false'
    ];
    $attributes = implode(' ', array_map(fn($key, $value) => "$key=\"$value\"", array_keys($draggable), $draggable));
}
?>

<form action="<?= Route::_('index.php?option=com_jexcontent&view=records'); ?>" method="post" name="adminForm" id="adminForm">
    <?= LayoutHelper::render('joomla.searchtools.default', ['view' => $this, 'options' => ['selectorFieldName' => 'catid']]); ?>
    <?php if (empty($this->items)): ?>
        <div class="alert alert-info">
            <span class="icon-info-circle" aria-hidden="true"></span>
            <?= Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <caption class="visually-hidden">
                <?= Text::_('COM_JEX_LIST_RECORDS_TITLE'); ?>,
                <span id="orderedBy"><?= Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                <span id="filteredBy"><?= Text::_('JGLOBAL_FILTERED_BY'); ?></span>
            </caption>
            <thead>
                <tr>
                    <th class="w-1 text-center">
                        <?= HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                        <?= HTMLHelper::_('searchtools.sort', '', 's.ordering', $listDir, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-sort'); ?>
                    </th>
                    <th scope="col" class="w-1 text-center">
                        <?= HTMLHelper::_('searchtools.sort', 'JSTATUS', 's.published', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col">
                        <?= HTMLHelper::_('searchtools.sort', 'COM_JEX_GRID_HEADING', 's.heading', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col">
                        <?= HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'c.title', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col">
                        <?= HTMLHelper::_('searchtools.sort', 'JCATEGORY', 's.catid', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col" class="w-10 d-none d-md-table-cell">
                        <?= HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col" class="w-10 d-none d-md-table-cell">
                        <?= HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col" class="w-5 d-none d-md-table-cell">
                        <?= HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDir, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody<?= Html::append($attributes); ?>>
                <?php foreach ($this->items as $i => $item): ?>
                    <?php /** @var \ToKu\Component\JexContent\Administrator\Table\RecordTable $item */ ?>
                    <tr<?= Html::attribute('data-draggable-group', $item->catid, $manualOrder); ?>>

                        <td><?= HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                        
                        <td class="text-center d-none d-md-table-cell">
                            <span class="sortable-handler<?= Html::append('inactive', !$manualOrder); ?>">
                                <span class="icon-ellipsis-v" aria-hidden="true"></span>
                            </span>
                            <?php if ($manualOrder): ?>
                                <input type="text" class="hidden" name="order[]" size="5" value="<?= $item->ordering; ?>">
                            <?php endif; ?>
                        </td>

                        <td class="center">
                            <?= HTMLHelper::_('jgrid.published', $item->published, $i, 'records.', true, 'cb'); ?>
                        </td>

                        <td scope="row">
                            <a href="<?= Route::_('index.php?option=com_jexcontent&task=record.edit&id=' . (int) $item->id); ?>" 
                                title="<?= Text::_('JACTION_EDIT'); ?> <?= $this->escape($item->heading); ?>">
                                <?= $this->escape($item->heading); ?>
                            </a>
                            <?php if ($item->subheading): ?>
                                <div>
                                    <span class="small">
                                        <?= $this->escape($item->subheading); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td scope="row">
                            <div>
                                <?= $this->escape($item->title); ?>
                            </div>
                            <?php if ($item->subtitle || $item->timeline): ?>
                                <div>
                                    <?php if ($item->subtitle): ?>
                                        <span class="small">
                                            <?= $this->escape($item->subtitle); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($item->timeline): ?>
                                        <span class="small">
                                            <?php $date = new Date($item->date); ?>
                                            <?= $date->format(Text::_('DATE_FORMAT_LC2')); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td class="small d-none d-md-table-cell">
                            <a href="<?= Route::_('index.php?option=com_categories&extension=com_jexcontent&task=category.edit&id=' . (int) $item->catid); ?>"
                                title="<?= Text::_('JACTION_EDIT'); ?> <?= $this->escape($item->category); ?>">
                                <?= $this->escape($item->category); // category is a dynamic property ?>
                            </a>
                        </td>

                        <td class="small d-none d-md-table-cell">
                            <?= $this->escape($item->access_level); // access_level is a dynamic property ?>
                        </td>

                        <td class="small d-none d-md-table-cell">
                            <?= LayoutHelper::render('joomla.content.language', $item); ?>
                        </td>

                        <td><?= $item->id; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?= $this->filterForm->renderControlFields(); ?>
</form>