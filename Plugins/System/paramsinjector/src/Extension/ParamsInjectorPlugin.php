<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  paramsinjector
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Plugin\System\ParamsInjector\Extension;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\EventInterface;
use Joomla\Event\SubscriberInterface;
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
        /** @var \Joomla\CMS\Form\Form */
        $form = $event->getArgument('form');
        $data = $event->getArgument('data');

        // only apply to module forms in the administrator
        if (!$this->getApplication()->isClient('administrator') || $form->getName() !== 'com_modules.module') {
            return;
        }

        // only apply to supported modules
        $modules = [
            'mod_articlecarousel', 
            'mod_carousel', 
            'mod_sequence', 
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