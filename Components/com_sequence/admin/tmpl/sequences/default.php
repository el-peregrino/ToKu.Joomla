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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use ToKu\Component\Sequence\Administrator\Helper\SequenceHelper;

/**
 * The View class of the MVC pattern.
 * 
 * Note: 
 *      The properties of $this are injected into the template via the PHP extract.
 *      They are defined in the HtmlView class, but not used directly.
 * 
 * @var \ToKu\Component\Sequence\Administrator\View\Sequences\HtmlView $this 
 */

/**
 * IGNORE ERROR PHP1416
 *      $this->items is injected via the PHP extract
 *      $this->state is injected via the PHP extract
 */

\defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDir  = $this->escape($this->state->get('list.direction'));

?>

<form action="<?= Route::_('index.php?option=com_sequence&view=sequences'); ?>" method="post" name="adminForm" id="adminForm">
    <?= LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
    <?php if (empty($this->items)): ?>
        <div class="alert alert-info">
            <span class="icon-info-circle" aria-hidden="true"></span>
            <?= Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <caption class="visually-hidden">
                <?= Text::_('COM_SQ_LIST_SEQUENCES_TITLE'); ?>,
                <span id="orderedBy"><?= Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                <span id="filteredBy"><?= Text::_('JGLOBAL_FILTERED_BY'); ?></span>
            </caption>
            <thead>
                <tr>
                    <th class="w-1 text-center">
                        <?= HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th scope="col" class="w-1 text-center">
                        <?= HTMLHelper::_('searchtools.sort', 'JSTATUS', 's.published', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col">
                        <?= HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 's.title', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col">
                        <?= HTMLHelper::_('searchtools.sort', 'COM_SQ_GRID_TYPE', 's.type', $listDir, $listOrder); ?>
                    </th>
                    <th colspan="2" scope="col" class="w-5 text-center">
                        <?= Text::_('COM_SQ_GRID_ITEMS'); ?>
                    </th>
                    <th scope="col" class="w-10 text-center d-none d-md-table-cell">
                        <span class="icon-check" aria-hidden="true"></span>
                        <span class="d-none d-lg-inline"><?= Text::_('JPUBLISHED'); ?></span>
                    </th>
                    <th scope="col" class="w-10 text-center d-none d-md-table-cell">
                        <span class="icon-times" aria-hidden="true"></span>
                        <span class="d-none d-lg-inline"><?= Text::_('JUNPUBLISHED'); ?></span>
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
            <tbody>
                <?php foreach ($this->items as $i => $item): ?>
                    <?php /** @var \ToKu\Component\Sequence\Administrator\Table\SequenceTable $item */ ?>
                    <tr class="row<?= $i % 2; ?>">
                        <td><?= HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                        <td class="center">
                            <?= HTMLHelper::_('jgrid.published', $item->published, $i, 'sequences.', true, 'cb'); ?>
                        </td>

                        <td scope="row">
                            <a href="<?= Route::_('index.php?option=com_sequence&task=sequence.edit&id=' . (int) $item->id); ?>" title="<?= Text::_('JACTION_EDIT'); ?> <?= $this->escape($item->title); ?>">
                                <?= $this->escape($item->title); ?>
                            </a>
                            <div>
                                <span class="small">
                                    <?php if (empty($item->note)) : ?>
                                        <?= Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                    <?php else : ?>
                                        <?= Text::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note)); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </td>

                        <td class="small d-none d-md-table-cell">
                            <?= SequenceHelper::getSequenceType($item->type); ?>
                        </td>

                        <td class="text-center btns">
                            <a href="<?= Route::_("index.php?option=com_sequence&view=items&sequence=$item->id"); ?>" title="<?= Text::_('COM_SQ_LIST_ITEMS_TITLE'); ?>">
                                <span class="icon-list" aria-hidden="true"></span><span class="visually-hidden"><?= Text::_('COM_SQ_LIST_ITEMS_TITLE'); ?></span>
                            </a>
                        </td>

                        <td class="text-center btns">
                            <a href="<?= Route::_("index.php?option=com_sequence&task=item.add&sequence=$item->id"); ?>" title="<?= Text::_('COM_SQ_ADD_ITEM_TITLE'); ?>">
                                <span class="icon-plus" aria-hidden="true"></span><span class="visually-hidden"><?= Text::_('COM_SQ_ADD_ITEM_TITLE'); ?></span>
                            </a>
                        </td>

                        <td class="text-center btns d-none d-md-table-cell itemnumber">
                            <span class="btn btn-<?= $item->published_count ? 'success' : 'secondary'; ?>"><?= $item->published_count; // published_count is a dynamic property ?></span>
                        </td>

                        <td class="text-center btns d-none d-md-table-cell itemnumber">
                            <span class="btn btn-<?= $item->unpublished_count ? 'danger' : 'secondary'; ?>"><?= $item->unpublished_count; // unpublished_count is a dynamic property ?></span>
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