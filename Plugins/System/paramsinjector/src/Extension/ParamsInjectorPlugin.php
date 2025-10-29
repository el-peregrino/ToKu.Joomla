<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  paramsinjector
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Plugin\System\ParamsInjector\Extension;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\EventInterface;
use Joomla\Event\SubscriberInterface;
use ToKu\Library\Closure;
use ToKu\Library\Joomla;
use ToKu\Library\JooToKu;

\defined('_JEXEC') or die;

final class ParamsInjectorPlugin extends CMSPlugin implements SubscriberInterface
{
    // TYPED CONSTANTS ARE SUPPORTED SINCE PHP 8.3

    public const NAME = 'ParamsInjector';
    public const ELEMENT = 'paramsinjector';

    public function __construct($config = [])
    {
        $this->autoloadLanguage = true;
        parent::__construct($config);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'onContentPrepareForm' => 'onContentPrepareForm'
        ];
    }

    /**
     * Injects parameters to the extension configuration.
     */
    public function onContentPrepareForm(EventInterface $event): void
    {
        // only apply to module forms in the administrator
        if (!$this->getApplication()->isClient('administrator')) {
            return;
        }

        /** @var \Joomla\CMS\Form\Form */
        $form = $event->getArgument('form');
        $data = $event->getArgument('data');

        $isTrue = Closure::isTrue($this->params);

        // try add module params
        if ($isTrue('module_frame') && $form->getName() === 'com_modules.module') {
            $this->loadModuleParams($form, $data);
        }

        // try add menu params
        if ($isTrue('menu_item') && $form->getName() === 'com_menus.item') {
            $form->loadFile(Joomla::getPath(JPATH_PLUGINS, Joomla::SYSTEM, self::ELEMENT, Joomla::FORMS, 'menu.xml'));            
        }
    }

    private function loadModuleParams(Form &$form, $data): void
    {
        // only apply to supported modules
        $modules = [
            'mod_articlecarousel', 
            'mod_carousel', 
            'mod_journal', 
            'mod_upcomingevent'
        ];
        $module = $data->module ?? JooToKu::getConfigModule($form);
        if (!$module || !in_array($module, $modules, true)) {
            return;
        }

        // load field definitions
        $form->loadFile(Joomla::getPath(JPATH_PLUGINS, Joomla::SYSTEM, self::ELEMENT, Joomla::FORMS, 'module.xml'));
    }
}