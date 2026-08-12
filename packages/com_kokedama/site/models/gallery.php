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

class GalleryModel extends ListModel
{
	protected function getListQuery()
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('g.*, p.title as project_title, e.title as event_title')
			->from($db->quoteName('#__kokedama_gallery', 'g'))
			->leftJoin($db->quoteName('#__kokedama_projects', 'p') . ' ON p.id = g.project_id')
			->leftJoin($db->quoteName('#__kokedama_events', 'e') . ' ON e.id = g.event_id')
			->where($db->quoteName('g.published') . ' = 1')
			->order($db->quoteName('g.ordering') . ' ASC');
		return $query;
	}
}
