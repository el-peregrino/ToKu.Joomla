<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\View\Carousel;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use ToKu\Component\JexContent\Administrator\View\BaseHtmlView;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
    /** @var \Joomla\CMS\Form\Form $form */
    protected $form;

    /** @var \ToKu\Component\JexContent\Administrator\Table\CarouselTable $item */
    protected $item;

    public function display($tpl = null)
    {
        /** @var \ToKu\Component\JexContent\Administrator\Model\CarouselModel $model */
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

        ToolbarHelper::title(Text::_('COM_JEX') . ': ' . Text::_($this->item->id ? 'COM_JEX_EDIT_CAROUSEL_TITLE' : 'COM_JEX_ADD_CAROUSEL_TITLE'));

        ToolbarHelper::apply('carousel.apply');
        ToolbarHelper::save('carousel.save');
        ToolbarHelper::save2copy('carousel.save2copy');
        ToolbarHelper::cancel('carousel.cancel', 'JTOOLBAR_CLOSE');

        $toolbar->inlinehelp();
    }
}
