<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_sequence
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\Sequence\Administrator\Table;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

class SequenceTable extends BaseTable
{
    /**
     * Title of the item.
     * @var string
     */
    public $title;

    /**
     * Title alias of the item.
     * @var string
     */
    public $alias;

    /**
     * Access level of the item.
     * @var int
     */
    public $access;

    /**
     * State of the item.
     * @var int
     */
    public $published;

    /**
     * [remove?] Type of the sequence.
     * What is the purpose?
     * @var string
     */
    public $type;

    /**
     * Header description of the sequence.
     * @var string
     */
    public $header;

    /**
     * Footer of the sequence.
     * @var string
     */
    public $footer;

    /**
     * Language of the item.
     * @var string
     */
    public $language;

    /**
     * Note about the item.
     * @var string
     */
    public $note;

    /**
     * Sequence parameters. Contains JSON structure.
     * @var string
     */
    public $params;

    /**
     * Sequence images. Contains JSON structure.
     * @var string
     */
    public $images;

    /**
     * Constructor
     *
     * @param   DatabaseDriver        $db          Database connector object
     * @param   ?DispatcherInterface  $dispatcher  Event dispatcher for this table
     */
    public function __construct(DatabaseDriver $db, ?DispatcherInterface $dispatcher = null)
    {
        parent::__construct('#__sequences', 'id', $db, $dispatcher);

        $this->typeAlias = 'com_sequence.sequence';
    }

    public function bind($array, $ignore = '')
    {
        if (isset($array['attribs']) && \is_array($array['attribs'])) {
            $registry = new Registry($array['attribs']);
            $array['attribs'] = (string) $registry;
        }

        return parent::bind($array, $ignore);
    }

    public function check(): bool
    {
        try {
            parent::check();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        if (trim($this->title) == '') {
            $this->setError('Title (title) is not set.');

            return false;
        }

        if (trim($this->alias) == '') {
            $this->alias = $this->title;
        }

        $this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

        // Ensure any new items have compulsory fields set
        if (!$this->id) {
            // Hits must be zero on a new item
            $this->hits = 0;
        }

        // Set publish_up to null if not set
        if (!$this->publish_up) {
            $this->publish_up = null;
        }

        // Set publish_down to null if not set
        if (!$this->publish_down) {
            $this->publish_down = null;
        }

        // Check the publish down date is not earlier than publish up.
        if ($this->publish_up !== null && $this->publish_down !== null && $this->publish_down < $this->publish_up) {
            // Swap the dates
            $temp = $this->publish_up;
            $this->publish_up = $this->publish_down;
            $this->publish_down = $temp;
        }

        return true;
    }

    public function store($updateNulls = true): mixed
    {
        $app = Factory::getApplication();
        $date = Factory::getDate()->toSql();
        $user = $app->getIdentity();

        if (!$this->created) {
            $this->created = $date;
        }

        if (!$this->created_by) {
            $this->created_by = $user->get('id');
        }

        if ($this->id) {
            // Existing item
            $this->modified_by = $user->get('id');
            $this->modified = $date;
        } else {
            // Set modified to created date if not set
            if (!$this->modified) {
                $this->modified = $this->created;
            }

            // Set modified_by to created_by user if not set
            if (empty($this->modified_by)) {
                $this->modified_by = $this->created_by;
            }
        }

        // Verify that the alias is unique
        
        /** @var \Joomla\CMS\MVC\Factory\MVCFactoryServiceInterface $component */
        $component = $app->bootComponent('com_sequence');
        $table = $component->getMVCFactory()->createTable('Sequence', 'Administrator');
        if ($table->load(['alias' => $this->alias]) && ($table->id != $this->id || $this->id == 0)) {
            $this->setError('Alias is not unique.');

            if ($table->state == -2) {
                $this->setError('Alias is not unique. The item is in Trash.');
            }

            return false;
        }

        return parent::store($updateNulls);
    }
}