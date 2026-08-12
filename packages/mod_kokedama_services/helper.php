<?php
/**
 * @package     Kokedama
 * @subpackage  mod_kokedama_services
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class ModKokedamaServicesHelper
{
	public static function getItems($params)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__kokedama_services')
			->where('published = 1')
			->where('featured = 1')
			->order('ordering ASC')
			->setLimit((int)$params->get('count', 3));
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
