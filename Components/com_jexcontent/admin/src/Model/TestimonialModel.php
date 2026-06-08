<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Component\JexContent\Administrator\Model;

use Joomla\Database\ParameterType;
use ToKu\Component\JexContent\Administrator\Table\TestimonialTable;
use ToKu\Library\Joomlib;

\defined('_JEXEC') or die;

class TestimonialModel extends BaseContentModel
{
    public function getTable($type = 'Testimonial', $prefix = 'Table', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = [], $loadData = true): mixed
    {
        return $this->getXmlForm('testimonial', $data, $loadData);
    }

    public function getItem($pk = null)
    {
        return $this->getItemWithTags('testimonial', $pk);
    }

    protected function loadFormData(): array
    {
        return $this->loadFormContentData('testimonial');
    }

    protected function prepareTable($table)
    {
        $app = Joomlib::getApp();
        $task = $app->getInput()->getCmd('task');
        if ($task === 'save2copy') {
            // reset ID so Joomla treats it as a new record
            $table->id = 0;
            // make unpublished
            $table->published = 0;

            // modify title and alias to avoid duplicates
            $origTitle = $table->title;
            $table->title = "$origTitle (Copy)";
            $table->alias = '';
        }

        if (!$table->id && $table instanceof TestimonialTable) {
            $table->ordering = $this->getNextOrdering($table);
        }

        parent::prepareTable($table);
    }

    private function getNextOrdering(TestimonialTable $table): int
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('MAX(ordering)')
              ->from($db->quoteName('#__jex_testimonials'))
              ->where($db->quoteName('catid') . ' = :category')
              ->bind(':category', $table->catid, ParameterType::INTEGER);

        $db->setQuery($query);
        $max = (int) $db->loadResult();

        return $max + 1;
    }

    public function save($data): mixed
    {
        return $this->saveData('testimonial', $data);
    }
}
