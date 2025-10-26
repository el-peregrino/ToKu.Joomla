<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\Table;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

class SequenceTable extends BaseTable
{
    /**
     * Category Id.
     * @var int
     */
    public $catid;

    /**
     * Title of the item.
     * @var string
     */
    public $title;

    /**
     * Subtitle of the item.
     * @var string
     */
    public $subtitle;

    /**
     * Heading of the item.
     * @var string
     */
    public $heading;

    /**
     * Subheading of the item.
     * @var string
     */
    public $subheading;

    /**
     * Timeline flag of the item.
     * @var bool
     */
    public $timeline;

    /**
     * Date of the timeline sequence item.
     */
    public $date;

    /**
     * Header of the sequence item.
     * @var string
     */
    public $header;

    /**
     * Body of the sequence item.
     * @var string
     */
    public $body;

    /**
     * Footer of the sequence item.
     * @var string
     */
    public $footer;

    /**
     * Item images. Contains JSON structure.
     * @var string
     */
    public $images;

    /**
     * Item links. Contains JSON structure.
     * @var string
     */
    public $links;

    /**
     * Access level of the item.
     * @var int
     */
    public $access;

    /**
     * Language of the item.
     * @var string
     */
    public $language;

    /**
     * State of the item.
     * @var int
     */
    public $published;

    /**
     * Note about the item.
     * @var string
     */
    public $note;

    /**
     * Sequence item parameters. Contains JSON structure.
     * @var string
     */
    public $params;

    /**
     * Item ordering.
     * @var int
     */
    public $ordering;

    /**
     * The ordering filter field. Needed for grouped ordering (by catid).
     */
    protected $orderingFilter = 'catid';

    /**
     * Constructor
     *
     * @param   DatabaseDriver        $db          Database connector object
     * @param   ?DispatcherInterface  $dispatcher  Event dispatcher for this table
     */
    public function __construct(DatabaseDriver $db, ?DispatcherInterface $dispatcher = null)
    {
        parent::__construct('#__jex_sequences', 'id', $db, $dispatcher);

        $this->typeAlias = 'com_jexcontent.sequence';
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
        // check inherited fields
        try {
            parent::check();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        // check sequence item data

        // TODO enhance checks
        if (trim($this->heading) == '') {
            $this->setError(Text::_('COM_JEX_ERROR_HEADING_REQUIRED'));

            return false;
        }

        if ($this->timeline && !$this->date) {
            $this->setError(Text::_('COM_JEX_ERROR_DATE_REQUIRED'));
        }

        /* not used now */

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

        return parent::store($updateNulls);
    }
}