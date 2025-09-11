<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\Sequence\Administrator\View\Sequences;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use ToKu\Component\Sequence\Administrator\View\BaseHtmlView;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
    /**
     * List of loaded items.
     * @var array
     */
    protected $items;

    /**
     * State of the view.
     * @var \Joomla\Registry\Registry
     */
    protected $state;

    /**
     * Form object for search filters
     *
     * @var \Joomla\CMS\Form\Form
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var array
     */
    public $activeFilters;

    public function display($tpl = null)
    {
        /** @var \ToKu\Component\Sequence\Administrator\Model\SequencesModel $model */
        $model = $this->getModel();
        
        $this->items = $model->getItems();
        $this->state = $model->getState();

        // add filters
        $this->filterForm = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();

        // add control fields
        $this->filterForm
            ->addControlField('task', '')
            ->addControlField('boxchecked', 0);

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_SQ') . ': ' . Text::_('COM_SQ_LIST_SEQUENCES_TITLE'));
        ToolbarHelper::addNew('sequence.add');
        ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'sequences.delete');
        ToolbarHelper::publish('sequences.publish', 'JTOOLBAR_PUBLISH', true);
        ToolbarHelper::unpublish('sequences.unpublish', 'JTOOLBAR_UNPUBLISH', true);
    }
}