<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;

class Com_KokedamaInstallerScript
{
	protected $minimumPhp = '8.1.0';
	protected $minimumJoomla = '4.4.0';

	public function install(InstallerAdapter $parent): bool
	{
		$this->createUploadFolder();
		Factory::getApplication()->enqueueMessage(Text::_('COM_KOKEDAMA_INSTALL_SUCCESS'), 'success');
		return true;
	}

	public function update(InstallerAdapter $parent): bool
	{
		$this->createUploadFolder();
		Factory::getApplication()->enqueueMessage(Text::_('COM_KOKEDAMA_UPDATE_SUCCESS'), 'success');
		return true;
	}

	public function uninstall(InstallerAdapter $parent): bool
	{
		$this->removeUploadFolder();
		return true;
	}

	public function preflight(string $type, InstallerAdapter $parent): bool
	{
		if (version_compare(PHP_VERSION, $this->minimumPhp, '<')) {
			Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPhp), Log::WARNING, 'jerror');
			return false;
		}

		if (version_compare(JVERSION, $this->minimumJoomla, '<')) {
			Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomla), Log::WARNING, 'jerror');
			return false;
		}

		return true;
	}

	protected function createUploadFolder(): void
	{
		$folder = JPATH_ROOT . '/images/kokedama';
		if (!is_dir($folder)) {
			mkdir($folder, 0755, true);
		}
		$subfolders = ['services', 'projects', 'gallery', 'events'];
		foreach ($subfolders as $sub) {
			$path = $folder . '/' . $sub;
			if (!is_dir($path)) {
				mkdir($path, 0755, true);
			}
		}
	}

	protected function removeUploadFolder(): void
	{
		$folder = JPATH_ROOT . '/images/kokedama';
		if (is_dir($folder)) {
			$this->recursiveDelete($folder);
		}
	}

	protected function recursiveDelete(string $dir): void
	{
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (is_dir($dir . "/" . $object)) {
						$this->recursiveDelete($dir . "/" . $object);
					} else {
						unlink($dir . "/" . $object);
					}
				}
			}
			rmdir($dir);
		}
	}
}
