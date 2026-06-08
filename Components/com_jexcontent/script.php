<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  com_jexcontent
 *
 * @copyright   (C) 2026 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\File;
use Joomla\Database\ParameterType;
use ToKu\Library\Joomlib;

\defined('_JEXEC') or die;

return new class () implements InstallerScriptInterface {

    private string $minimumJoomla = '5.3.0';
    private string $minimumPhp = '8.2.0';
    private string $minimumToKu = "1.0.16";

    public function install(InstallerAdapter $adapter): bool
    {
        $this->registerContentType();
        return true;
    }

    public function update(InstallerAdapter $adapter): bool
    {
        $this->registerContentType();
        return true;
    }

    public function uninstall(InstallerAdapter $adapter): bool
    {
        return true;
    }

    /**
     * Pre installation check
     */
    public function preflight(string $type, InstallerAdapter $adapter): bool
    {
        // php version
        if (version_compare(PHP_VERSION, $this->minimumPhp, '<')) {
            Factory::getApplication()->enqueueMessage(Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPhp), 'error');
            return false;
        }

        // joomla version
        if (version_compare(JVERSION, $this->minimumJoomla, '<')) {
            Factory::getApplication()->enqueueMessage(Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomla), 'error');
            return false;
        }

        // dependency check
        if (!class_exists('ToKu\Library\Joomlib')) {
            Factory::getApplication()->enqueueMessage(Text::sprintf('COM_JEXCONTENT_LIBRARY_ERROR', $this->minimumToKu), 'error');
            return false;
        }

        $version = Joomlib::VERSION ?? null;
        if (version_compare($version, $this->minimumToKu, '<')) {
            Factory::getApplication()->enqueueMessage(Text::sprintf('COM_JEXCONTENT_LIBRARY_ERROR', $this->minimumToKu), 'error');
            return false;
        }

        return true;
    }

    /**
     * Post installation actions
     */
    public function postflight(string $type, InstallerAdapter $adapter): bool
    {
        $this->deleteFiles();
        return true;
    }

    /**
     * Safely removes files which are no longer needed.
     */
    private function deleteFiles(): void
    {
        $files = [];  // overwrite this line with your files to delete

        if (empty($files))
            return;

        foreach ($files as $file) {
            try {
                File::delete(JPATH_ROOT . $file);
            } 
            catch (\FilesystemException $e) { // global namespace
                /** @var \Joomla\Filesystem\Exception\FilesystemException $e */
                echo Text::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $file) . '<br>';
            }
        }
    }

    /**
     * Registers the content type for tagging support
     */
    private function registerContentType(): void
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class); 
        $app = Factory::getApplication();

        $types = [
            [
                'type_title' => 'JEX Content Record',
                'type_alias' => 'com_jexcontent.record',
                'table' => ['special' => ['dbtable' => '#__jex_records', 'key' => 'id', 'type' => 'RecordTable', 'prefix' => 'ToKu\\\\Component\\\\JexContent\\\\Administrator\\\\Table\\\\']],
                'field_mappings' => ['common' => ['core_content_item_id' => 'id', 'core_title' => 'heading', 'core_state' => 'published', 'core_created_time' => 'created', 'core_modified_time' => 'modified', 'core_created_user_id' => 'created_by', 'core_modified_user_id' => 'modified_by', 'core_ordering' => 'ordering'] ],
                'formAction' => 'index.php?option=com_jexcontent&view=record&layout=edit',
            ],
            [
                'type_title' => 'JEX Content Carousel',
                'type_alias' => 'com_jexcontent.carousel',
                'table' => ['special' => ['dbtable' => '#__jex_carousels', 'key' => 'id', 'type' => 'CarouselTable', 'prefix' => 'ToKu\\\\Component\\\\JexContent\\\\Administrator\\\\Table\\\\']],
                'field_mappings' => ['common' => ['core_content_item_id' => 'id', 'core_title' => 'title', 'core_state' => 'published', 'core_created_time' => 'created', 'core_modified_time' => 'modified', 'core_created_user_id' => 'created_by', 'core_modified_user_id' => 'modified_by', 'core_ordering' => 'ordering'] ],
                'formAction' => 'index.php?option=com_jexcontent&view=carousel&layout=edit',
            ],
            [
                'type_title' => 'JEX Content Testimonial',
                'type_alias' => 'com_jexcontent.testimonial',
                'table' => ['special' => ['dbtable' => '#__jex_testimonials', 'key' => 'id', 'type' => 'TestimonialTable', 'prefix' => 'ToKu\\\\Component\\\\JexContent\\\\Administrator\\\\Table\\\\']],
                'field_mappings' => ['common' => ['core_content_item_id' => 'id', 'core_title' => 'title', 'core_state' => 'published', 'core_created_time' => 'created', 'core_modified_time' => 'modified', 'core_created_user_id' => 'created_by', 'core_modified_user_id' => 'modified_by', 'core_ordering' => 'ordering'] ],
                'formAction' => 'index.php?option=com_jexcontent&view=testimonial&layout=edit',
            ],
            [
                'type_title' => 'JEX Content Quote',
                'type_alias' => 'com_jexcontent.quote',
                'table' => ['special' => ['dbtable' => '#__jex_quotes', 'key' => 'id', 'type' => 'QuoteTable', 'prefix' => 'ToKu\\\\Component\\\\JexContent\\\\Administrator\\\\Table\\\\']],
                'field_mappings' => ['common' => ['core_content_item_id' => 'id', 'core_title' => 'title', 'core_state' => 'published', 'core_created_time' => 'created', 'core_modified_time' => 'modified', 'core_created_user_id' => 'created_by', 'core_modified_user_id' => 'modified_by', 'core_ordering' => 'ordering'] ],
                'formAction' => 'index.php?option=com_jexcontent&view=quote&layout=edit',
            ],
        ];

        foreach ($types as $type) {
            try {
                $query = $db->getQuery(true)
                    ->select($db->quoteName('type_id'))
                    ->from($db->quoteName('#__content_types'))
                    ->where($db->quoteName('type_alias') . ' = ' . $db->quote($type['type_alias']));

                $db->setQuery($query);
                $exists = $db->loadResult();

                if ($exists) {
                    continue;
                }

                $data = [
                    'type_title' => $type['type_title'],
                    'type_alias' => $type['type_alias'],
                    'table' => json_encode($type['table']),
                    'field_mappings' => json_encode($type['field_mappings']),
                    'rules' => '',
                    'router' => '',
                    'content_history_options' => json_encode([
                        'formAction' => $type['formAction'],
                        'hideFields' => ['checked_out', 'checked_out_time', 'version'],
                        'ignoreChanges' => ['modified_by', 'modified', 'checked_out', 'checked_out_time'],
                        'convertToInt' => ['published', 'ordering', 'access', 'hits'],
                        'textFields' => ['header', 'body', 'footer'],
                    ]),
                ];

                $cols = [];
                $vals = [];
                foreach ($data as $col => $val) {
                    $cols[] = $db->quoteName($col);
                    $vals[] = $db->quote($val);
                }

                $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__content_types'))
                    ->columns($cols)
                    ->values(implode(',', $vals));

                $db->setQuery($query)->execute();
            } catch (\Exception $e) {
                $app->enqueueMessage(Text::sprintf('COM_JEXCONTENT_CONTENTTYPE_CREATE_FAILED', $type['type_alias'] . ': ' . $e->getMessage()), 'warning');
            }
        }
    }
};