<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\Sequence\Administrator\View\Item;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use ToKu\Component\Sequence\Administrator\View\BaseHtmlView;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
    /** @var \Joomla\CMS\Form\Form $form */
    protected $form;

    /** @var \ToKu\Component\Sequence\Administrator\Table\ItemTable $item */
    protected $item;

    public function display($tpl = null)
    {
        /** @var \ToKu\Component\Sequence\Administrator\Model\ItemModel $model */
        $model = $this->getModel();

        $this->form = $model->getForm();
        $this->item = $model->getItem();

        /** @var \Joomla\Input\Input */
        $input = $this->getApp()->getInput();

        // hide main menu (Joomla left panel)
        $input->set('hidemainmenu', true);

        $this->form
            ->addControlField('task', '')
            ->addControlField('sequence', $input->get('sequence', ''));

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        /** @var \Joomla\CMS\Toolbar\Toolbar */
        $toolbar = $this->getToolbar();

        ToolbarHelper::title(Text::_('COM_SQ') . ': ' . Text::_('COM_SQ_ADD_ITEM_TITLE'));
        ToolbarHelper::apply('item.apply');
        ToolbarHelper::save('item.save');
        ToolbarHelper::save2copy('item.save2copy');
        ToolbarHelper::cancel('item.cancel', 'JTOOLBAR_CLOSE');

        // help button
        $toolbar->inlinehelp();
    }
}