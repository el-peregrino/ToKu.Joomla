<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  upcomingeventfilter
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Plugin\System\UpcomingEventFilter\Extension;

use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\EventInterface;
use Joomla\Event\SubscriberInterface;
use ToKu\Library\Joomla;
use ToKu\Library\MVC\MVCFactory;

\defined('_JEXEC') or die;

final class UpcomingEventFilterPlugin extends CMSPlugin implements SubscriberInterface
{
    // TYPED CONSTANTS ARE SUPPORTED SINCE PHP 8.3

    public const NAME = 'UpcomingEventFilter';
    public const ELEMENT = 'upcomingeventfilter';

    public function __construct($config = [])
    {
        $this->autoloadLanguage = true;
        parent::__construct($config);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterExtensionBoot' => 'onAfterExtensionBoot',
            'onContentPrepareForm' => 'onContentPrepareForm'
        ];
    }

    /**
     * Injects event filter parameters to the category menu items.
     */
    public function onContentPrepareForm(EventInterface $event): void
    {
        /** @var \Joomla\CMS\Form\Form */
        $form = $event->getArgument('form');
        $data = $event->getArgument('data');

        // only apply to menu item forms in the administrator
        if (!$this->getApplication()->isClient('administrator') || $form->getName() !== 'com_menus.item') {
            return;
        }

        // check we're editing a menu item that links to a category or featured view
        $link = $data->link ?? '';
        if (!str_contains($link, 'option=com_content') || !(str_contains($link, 'view=category') || str_contains($link, 'view=featured'))) {
            return;
        }

        // load field definitions
        $form->loadFile(Joomla::getPath(JPATH_PLUGINS, Joomla::SYSTEM, self::ELEMENT, Joomla::FORMS, 'params.xml'));
    }

    /**
     * OVERRIDE MVC FACTORY
     * 
     * This is a terrifying hack to enable query interception, model replacement and many other stuff that can break the system.
     * The reason for this is that some components bypass DI and instantiate classes directly or by convention.
     */
    public function onAfterExtensionBoot(EventInterface $event): void
    {
        // detect if the replacement is really needed
        if ($event->getArgument('type') !== ComponentInterface::class)
            return;
        if ($event->getArgument('extensionName') !== 'content')
            return;

        $container = $event->getArgument('container');

        if (!$container->has(MVCFactoryInterface::class))
            return;

        // replace MVC Factory
        $container->extend(
            MVCFactoryInterface::class,
            fn($factory) => new MVCFactory($factory)
        );
    }
}