<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\View\Testimonial;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use ToKu\Component\JexContent\Administrator\View\BaseHtmlView;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
    /** @var \Joomla\CMS\Form\Form $form */
    protected $form;

    /** @var \ToKu\Component\JexContent\Administrator\Table\TestimonialTable $item */
    protected $item;

    public function display($tpl = null)
    {
        /** @var \ToKu\Component\JexContent\Administrator\Model\TestimonialModel $model */
        $model = $this->getModel();

        $this->form = $model->getForm();
        $this->item = $model->getItem();

        /** @var \Joomla\Input\Input */
        $input = $this->getApp()->getInput();

        // hide main menu (Joomla left panel)
        $input->set('hidemainmenu', true);

        $this->form->addControlField('task', '');

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        /** @var \Joomla\CMS\Toolbar\Toolbar */
        $toolbar = $this->getToolbar();

        ToolbarHelper::title(Text::_('COM_JEX') . ': ' . Text::_($this->item->id ? 'COM_JEX_EDIT_TESTIMONIAL_TITLE' : 'COM_JEX_ADD_TESTIMONIAL_TITLE'));

        ToolbarHelper::apply('testimonial.apply');
        ToolbarHelper::save('testimonial.save');
        ToolbarHelper::save2copy('testimonial.save2copy');
        ToolbarHelper::cancel('testimonial.cancel', 'JTOOLBAR_CLOSE');

        $toolbar->inlinehelp();
    }
}
