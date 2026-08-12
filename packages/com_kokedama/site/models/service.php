<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Kokedama\Component\Kokedama\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;

class ServiceModel extends ItemModel
{
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_kokedama.edit.service.data', []);
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	public function getItem($pk = null)
	{
		$pk = $pk ?? $this->getState('service.id', Factory::getApplication()->input->getInt('id', 0));
		if ($pk <= 0) return null;

		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kokedama_services'))
			->where($db->quoteName('id') . ' = ' . (int)$pk)
			->where($db->quoteName('published') . ' = 1');
		$db->setQuery($query);
		$item = $db->loadObject();

		if ($item && !empty($item->gallery)) {
			$item->gallery = json_decode($item->gallery);
		}

		// Increment hits
		if ($item) {
			$db->getQuery(true)
				->update($db->quoteName('#__kokedama_services'))
				->set($db->quoteName('hits') . ' = ' . $db->quoteName('hits') . ' + 1')
				->where($db->quoteName('id') . ' = ' . (int)$pk);
			$db->execute();
		}

		return $item;
	}
}
