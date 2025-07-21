<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;

\defined('_JEXEC') or die;

return new class () implements InstallerScriptInterface {

    private string $minimumJoomla = '5.3.0';
    private string $minimumPhp = '8.2.0';
    private string $minimumToKu = "1.0.3";

    public function install(InstallerAdapter $adapter): bool
    {
        return true;
    }

    public function update(InstallerAdapter $adapter): bool
    {
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
            Factory::getApplication()->enqueueMessage(sprintf(Text::_('JLIB_INSTALLER_MINIMUM_PHP'), $this->minimumPhp), 'error');
            return false;
        }

        // joomla version
        if (version_compare(JVERSION, $this->minimumJoomla, '<')) {
            Factory::getApplication()->enqueueMessage(sprintf(Text::_('JLIB_INSTALLER_MINIMUM_JOOMLA'), $this->minimumJoomla), 'error');
            return false;
        }

        // dependency check
        if (!class_exists('ToKu\Library\JToKu')) {
            Factory::getApplication()->enqueueMessage(sprintf("JToKu Library version %s is required.", $this->minimumToKu), 'error');
            return false;
        }

        $version = \ToKu\Library\JToKu::VERSION ?? null;
        if (version_compare($version, $this->minimumToKu, '<')) {
            Factory::getApplication()->enqueueMessage(sprintf("JToKu Library version %s is required.", $this->minimumToKu), 'error');
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

        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            try {
                File::delete(JPATH_ROOT . $file);
            } catch (\FilesystemException $e) { // global namespace
                // \Joomla\Filesystem\Exception\FilesystemException
                echo Text::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $file) . '<br>';
            }
        }
    }
};