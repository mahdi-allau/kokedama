<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Kokedama\Component\Kokedama\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class EventsModel extends ListModel
{
	protected function getListQuery()
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__kokedama_events'))
			->where($db->quoteName('published') . ' = 1')
			->where($db->quoteName('event_date') . ' >= CURDATE()')
			->order($db->quoteName('event_date') . ' ASC, ' . $db->quoteName('event_time') . ' ASC');
		return $query;
	}
}
