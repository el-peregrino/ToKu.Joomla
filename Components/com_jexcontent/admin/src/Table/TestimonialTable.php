<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\Table;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

class TestimonialTable extends BaseTable
{
    public $catid;
    public $title;
    public $header;
    public $body;
    public $footer;
    public $author;
    public $about;
    public $images;
    public $links;
    public $access;
    public $language;
    public $published;
    public $note;
    public $params;
    public $ordering;

    protected $orderingFilter = 'catid';

    public function __construct(DatabaseDriver $db, ?DispatcherInterface $dispatcher = null)
    {
        parent::__construct('#__jex_testimonials', 'id', $db, $dispatcher);

        $this->typeAlias = 'com_jexcontent.testimonial';
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

        // Ensure required fields
        if (trim((string) $this->author) === '') {
            $this->setError(Text::_('COM_JEX_ERROR_AUTHOR_REQUIRED'));
            return false;
        }

        if (trim((string) $this->body) === '') {
            $this->setError(Text::_('COM_JEX_ERROR_BODY_REQUIRED'));
            return false;
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
            $this->modified_by = $user->get('id');
            $this->modified = $date;
        } else {
            if (!$this->modified) {
                $this->modified = $this->created;
            }

            if (empty($this->modified_by)) {
                $this->modified_by = $this->created_by;
            }
        }

        return parent::store($updateNulls);
    }
}
