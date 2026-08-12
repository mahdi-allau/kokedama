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

class BookingsModel extends ListModel
{
	public function __construct($config = [])
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id', 'status', 'booking_type', 'booking_date',
				'first_name', 'last_name', 'email', 'created'
			];
		}
		parent::__construct($config);
	}

	protected function getListQuery()
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('b.*, s.title as service_title, e.title as event_title')
			->from($db->quoteName('#__kokedama_bookings', 'b'))
			->leftJoin($db->quoteName('#__kokedama_services', 's') . ' ON s.id = b.service_id')
			->leftJoin($db->quoteName('#__kokedama_events', 'e') . ' ON e.id = b.event_id');

		// Filter: status
		$status = $this->getState('filter.status');
		if (!empty($status)) {
			$query->where($db->quoteName('b.status') . ' = ' . $db->quote($status));
		}

		// Filter: type
		$type = $this->getState('filter.booking_type');
		if (!empty($type)) {
			$query->where($db->quoteName('b.booking_type') . ' = ' . $db->quote($type));
		}

		// Search
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = '%' . $db->escape($search, true) . '%';
			$query->where('(b.first_name LIKE ' . $db->quote($search) . ' OR b.last_name LIKE ' . $db->quote($search) . ' OR b.email LIKE ' . $db->quote($search) . ')');
		}

		$orderCol = $this->state->get('list.ordering', 'b.created');
		$orderDir = $this->state->get('list.direction', 'DESC');
		$query->order($db->quoteName($orderCol) . ' ' . $db->escape($orderDir));

		return $query;
	}
}
