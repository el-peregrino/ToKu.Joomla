<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\Sequence\Administrator\Table;

use Joomla\CMS\Table\Table;

\defined('_JEXEC') or die;

abstract class BaseTable extends Table
{
    /**
     * Record id (PK).
     * @var int
     */
    public $id;

    /**
     * Item creation timestamp.
     * @var string
     */
    public $created;

    /**
     * Id of the user who created the item.
     * @var int
     */
    public $created_by;

    /**
     * Item modification timestamp.
     * @var string
     */
    public $modified;

    /**
     * Id of the user who modified the item.
     * @var int
     */
    public $modified_by;
}