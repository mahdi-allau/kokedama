<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Kokedama\Component\Kokedama\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class DashboardModel extends BaseDatabaseModel
{
	public function getStats()
	{
		$db = $this->getDatabase();
		$stats = new \stdClass();

		// Pending bookings
		$query = $db->getQuery(true)->select('COUNT(*)')->from('#__kokedama_bookings')->where('status = ' . $db->quote('pending'));
		$db->setQuery($query);
		$stats->pending_bookings = (int) $db->loadResult();

		// Total bookings
		$query = $db->getQuery(true)->select('COUNT(*)')->from('#__kokedama_bookings');
		$db->setQuery($query);
		$stats->total_bookings = (int) $db->loadResult();

		// New messages
		$query = $db->getQuery(true)->select('COUNT(*)')->from('#__kokedama_messages')->where('status = ' . $db->quote('new'));
		$db->setQuery($query);
		$stats->new_messages = (int) $db->loadResult();

		// Total messages
		$query = $db->getQuery(true)->select('COUNT(*)')->from('#__kokedama_messages');
		$db->setQuery($query);
		$stats->total_messages = (int) $db->loadResult();

		// Upcoming events
		$query = $db->getQuery(true)->select('COUNT(*)')->from('#__kokedama_events')->where('published = 1')->where('event_date >= CURDATE()');
		$db->setQuery($query);
		$stats->upcoming_events = (int) $db->loadResult();

		// Total services
		$query = $db->getQuery(true)->select('COUNT(*)')->from('#__kokedama_services')->where('published = 1');
		$db->setQuery($query);
		$stats->published_services = (int) $db->loadResult();

		// Recent bookings
		$query = $db->getQuery(true)->select('*')->from('#__kokedama_bookings')->order('created DESC')->setLimit(5);
		$db->setQuery($query);
		$stats->recent_bookings = $db->loadObjectList();

		// Recent messages
		$query = $db->getQuery(true)->select('*')->from('#__kokedama_messages')->order('created DESC')->setLimit(5);
		$db->setQuery($query);
		$stats->recent_messages = $db->loadObjectList();

		return $stats;
	}
}
