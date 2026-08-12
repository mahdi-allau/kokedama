<?php
/**
 * @package     Kokedama
 * @subpackage  plg_system_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

class PlgSystemKokedama extends CMSPlugin
{
	protected $app;

	public function onAfterDispatch()
	{
		if (!$this->app) {
			$this->app = Factory::getApplication();
		}

		if ($this->app->isClient('administrator')) {
			return;
		}

		$option = $this->app->input->get('option');
		$view = $this->app->input->get('view');
		$id = $this->app->input->getInt('id');

		if ($option !== 'com_kokedama' || !$id) {
			return;
		}

		$doc = $this->app->getDocument();
		$db = Factory::getDbo();
		$table = '';
		$fields = [];

		switch ($view) {
			case 'service':
				$table = '#__kokedama_services';
				$fields = ['title', 'meta_title', 'meta_desc', 'image'];
				break;
			case 'project':
				$table = '#__kokedama_projects';
				$fields = ['title', 'meta_title', 'meta_desc', 'image'];
				break;
			case 'event':
				$table = '#__kokedama_events';
				$fields = ['title', 'meta_title', 'meta_desc', 'image'];
				break;
		}

		if (!$table) {
			return;
		}

		$query = $db->getQuery(true);
		$query->select($db->quoteName($fields))->from($db->quoteName($table))->where($db->quoteName('id') . ' = ' . (int)$id);
		$db->setQuery($query);
		$item = $db->loadObject();

		if ($item) {
			$title = $item->meta_title ?: $item->title . ' — Kokedama & Sculture Naturali';
			$desc = $item->meta_desc ?: '';
			$doc->setTitle($title);
			if ($desc) {
				$doc->setMetaData('description', $desc);
			}
			// Open Graph
			$doc->addCustomTag('<meta property="og:title" content="' . htmlspecialchars($title) . '">');
			$doc->addCustomTag('<meta property="og:description" content="' . htmlspecialchars($desc) . '">');
			if (!empty($item->image)) {
				$doc->addCustomTag('<meta property="og:image" content="' . \Joomla\CMS\Uri\Uri::root() . ltrim($item->image, '/') . '">');
			}
			$doc->addCustomTag('<meta property="og:type" content="article">');
		}
	}
}
