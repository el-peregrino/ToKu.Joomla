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
 * @var \ToKu\Component\Sequence\Administrator\View\Items\HtmlView $this 
 */

/**
 * IGNORE ERROR PHP1416
 *      $this->items is injected via the PHP extract
 *      $this->state is injected via the PHP extract
 */

\defined('_JEXEC') or die;

// get the currently edited sequence
$sequence = $this->state->get('filter.sequence', '');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDir  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?= Route::_('index.php?option=com_sequence&view=items'); ?>" method="post" name="adminForm" id="adminForm">
    <?= LayoutHelper::render('joomla.searchtools.default', ['view' => $this, 'options' => ['selectorFieldName' => 'sequence']]); ?>
    <?php if (empty($this->items)): ?>
        <div class="alert alert-info">
            <span class="icon-info-circle" aria-hidden="true"></span>
            <?= Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <caption class="visually-hidden">
                <?= Text::_('COM_SQ_LIST_ITEMS_TITLE'); ?>,
                <span id="orderedBy"><?= Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                <span id="filteredBy"><?= Text::_('JGLOBAL_FILTERED_BY'); ?></span>
            </caption>
            <thead>
                <tr>
                    <th class="w-1 text-center">
                        <?= HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <?php if ($sequence) : ?>
                        <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', '', 'si.ordering', $listDir, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-sort'); ?>
                        </th>
                    <?php endif; ?>
                    <th scope="col" class="w-1 text-center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'si.published', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_SQ_GRID_HEADING', 'si.heading', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'si.title', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_SQ_GRID_SEQUENCE', 's.title', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_SEQUENCE_GRID_TYPE', 's.type', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col" class="w-10 d-none d-md-table-cell">
                        <?= HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDir, $listOrder); ?>
                    </th>
                    <th scope="col" class="w-5 d-none d-md-table-cell">
                        <?= HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDir, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                /** @var \ToKu\Component\Sequence\Administrator\Table\ItemTable $item */
                foreach ($this->items as $i => $item): 

                    // IGNORE ERROR PHP1416 ($this->ordering is injected via the PHP extract)
                    $orderKey = 0; //array_search($item->id, $this->ordering[$item->sequence_id]);
                    ?>
                    <tr>
                        <td><?= HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                        <?php if ($sequence) : ?>
                            <td class="text-center d-none d-md-table-cell">
                                

                                <!-- <span class="sortable-handler">
                                    <span class="icon-ellipsis-v" aria-hidden="true"></span>
                                </span>
                                <input type="text" class="hidden" name="order[]" size="5" value="<?= $orderKey + 1; ?>"> -->
                            </td>
                        <?php endif; ?>

                        <td class="center">
                            <?= HTMLHelper::_('jgrid.published', $item->published, $i, 'items.', true, 'cb'); ?>
                        </td>

                        <td scope="row">
                            <a href="<?= Route::_('index.php?option=com_sequence&task=item.edit&id=' . (int) $item->id); ?>" title="<?= Text::_('JACTION_EDIT'); ?> <?= $this->escape($item->heading); ?>">
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
                            <?php if($item->caption): ?>
                                <div>
                                    <span class="small">
                                        <?= $this->escape($item->caption); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td class="small d-none d-md-table-cell">
                            <a href="<?= Route::_('index.php?option=com_sequence&task=sequence.edit&id=' . (int) $item->sequence_id); ?>" title="<?= Text::_('JACTION_EDIT'); ?> <?= $this->escape($item->sequence_title); ?>">
                                <?= $this->escape($item->sequence_title); // sequence_title is a dynamic property ?>
                            </a>
                        </td>

                        <td class="small d-none d-md-table-cell">
                            <?= SequenceHelper::getSequenceType($item->sequence_type); // sequence_type is a dynamic property ?>
                        </td>

                        <td class="small d-none d-md-table-cell">
                            <?= $this->escape($item->access_level); // access_level is a dynamic property ?>
                        </td>

                        <td><?= $item->id; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?= $this->filterForm->renderControlFields(); ?>
</form>