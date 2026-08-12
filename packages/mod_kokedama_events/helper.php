<?php
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
class ModKokedamaEventsHelper
{
	public static function getItems($params)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__kokedama_events')
			->where('published = 1')
			->where('event_date >= CURDATE()')
			->order('event_date ASC, event_time ASC')
			->setLimit((int)$params->get('count', 3));
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
