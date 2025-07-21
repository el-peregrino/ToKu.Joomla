<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Library\Field;

use Joomla\CMS\Form\Field\ListField;

\defined('_JEXEC') or die;

class CustomFieldSelectorField extends ListField
{
    protected $type = "customfieldselector";

    protected function getOptions(): array
    {
        $options = [];

        // get database
        $db = $this->getDatabase(); // parent is database aware trait

        // build query
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('f.id'),
                $db->quoteName('f.title'),
                $db->quoteName('f.type'),
                $db->quoteName('f.context'),
                $db->quoteName('fg.title') . ' AS group_title'
            ])
            ->from('#__fields AS f')
            ->join('LEFT', '#__fields_groups AS fg ON f.group_id = fg.id')
            ->where('f.state = 1'); // published

        // context filter
        $context = (string) $this->element['context'];
        if (!empty($context)) {
            $query->where('f.context = ' . $db->quote($context));
        }

        // field type filter
        $fieldtype = (string) $this->element['fieldtype'];
        if (!empty($fieldtype)) {
            $query->where('f.type = ' . $db->quote($fieldtype));
        }

        // group filter
        $groupId = (int) $this->element['group'];
        if ($groupId > 0) {
            $query->where('f.group_id = ' . $db->quote($groupId));
        }

        // order by field name
        $query->order('f.title ASC');

        $db->setQuery($query);

        // get items
        $fields = $db->loadObjectList();

        // process data
        foreach ($fields as $field) {
            $label = $field->title;
            $hint = "Type: {$field->type}, Group: {$field->group_title}, Context: {$field->context}";

            $options[] = [
                'value' => $field->id,
                'text' => $label,
                'option.attr.title' => $hint // adds HTML tooltip
            ];
        }

        return array_merge(parent::getOptions(), $options);
    }
}