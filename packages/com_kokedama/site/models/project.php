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

class ProjectModel extends ItemModel
{
	public function getItem($pk = null)
	{
		$pk = $pk ?? $this->getState('project.id', Factory::getApplication()->input->getInt('id', 0));
		if ($pk <= 0) return null;

		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('p.*, s.title as service_title')
			->from($db->quoteName('#__kokedama_projects', 'p'))
			->leftJoin($db->quoteName('#__kokedama_services', 's') . ' ON s.id = p.service_id')
			->where($db->quoteName('p.id') . ' = ' . (int)$pk)
			->where($db->quoteName('p.published') . ' = 1');
		$db->setQuery($query);
		$item = $db->loadObject();

		if ($item && !empty($item->gallery)) {
			$item->gallery = json_decode($item->gallery);
		}
		return $item;
	}
}
