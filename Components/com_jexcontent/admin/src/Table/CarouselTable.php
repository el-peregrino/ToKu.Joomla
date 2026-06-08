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

class CarouselTable extends BaseTable
{
    public $catid;
    public $title;
    public $subtitle;
    public $header;
    public $body;
    public $footer;
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
        parent::__construct('#__jex_carousels', 'id', $db, $dispatcher);

        $this->typeAlias = 'com_jexcontent.carousel';
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

        // Carousel-specific checks can go here. Keep minimal: ensure title or header/body exists.
        if (trim((string) $this->title) === '' && trim((string) $this->header) === '' && trim((string) $this->body) === '') {
            $this->setError(Text::_('COM_JEX_ERROR_TITLE_OR_CONTENT_REQUIRED'));
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
