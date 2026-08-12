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

class EventModel extends ItemModel
{
	public function getItem($pk = null)
	{
		$pk = $pk ?? $this->getState('event.id', Factory::getApplication()->input->getInt('id', 0));
		if ($pk <= 0) return null;

		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kokedama_events'))
			->where($db->quoteName('id') . ' = ' . (int)$pk)
			->where($db->quoteName('published') . ' = 1');
		$db->setQuery($query);
		return $db->loadObject();
	}
}
