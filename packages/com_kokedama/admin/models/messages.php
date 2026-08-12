<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Kokedama\Component\Kokedama\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class MessagesModel extends ListModel
{
	public function __construct($config = [])
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = ['id', 'status', 'name', 'email', 'created'];
		}
		parent::__construct($config);
	}

	protected function getListQuery()
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->quoteName('#__kokedama_messages'));

		$status = $this->getState('filter.status');
		if (!empty($status)) {
			$query->where($db->quoteName('status') . ' = ' . $db->quote($status));
		}

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = '%' . $db->escape($search, true) . '%';
			$query->where('(name LIKE ' . $db->quote($search) . ' OR email LIKE ' . $db->quote($search) . ' OR subject LIKE ' . $db->quote($search) . ')');
		}

		$orderCol = $this->state->get('list.ordering', 'created');
		$orderDir = $this->state->get('list.direction', 'DESC');
		$query->order($db->quoteName($orderCol) . ' ' . $db->escape($orderDir));

		return $query;
	}
}
