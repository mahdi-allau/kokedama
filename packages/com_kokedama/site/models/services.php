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
use Joomla\CMS\MVC\Model\ListModel;

class ServicesModel extends ListModel
{
	protected function getListQuery()
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__kokedama_services'))
			->where($db->quoteName('published') . ' = 1')
			->order($db->quoteName('ordering') . ' ASC');

		return $query;
	}

	public function getItems()
	{
		$items = parent::getItems();
		foreach ($items as &$item) {
			if (!empty($item->gallery)) {
				$item->gallery = json_decode($item->gallery);
			}
		}
		return $items;
	}
}
