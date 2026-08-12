<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Kokedama\Component\Kokedama\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

class MessageModel extends AdminModel
{
	protected $text_prefix = 'COM_KOKEDAMA';

	public function getTable($type = 'Message', $prefix = 'Table', $config = [])
	{
		return $this->getMVCFactory()->createTable($type, $prefix, $config);
	}

	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm('com_kokedama.message', 'message', ['control' => 'jform', 'load_data' => $loadData]);
		if (empty($form)) return false;
		return $form;
	}

	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_kokedama.edit.message.data', []);
		if (empty($data)) {
			$data = $this->getItem();
			// Mark as read when opened
			if ($data && $data->status === 'new') {
				$data->status = 'read';
				$data->read_at = Factory::getDate()->toSql();
				$this->getDatabase()->updateObject('#__kokedama_messages', $data, 'id');
			}
		}
		return $data;
	}

	public function changeStatus($ids, $status)
	{
		if (empty($ids)) return false;
		$allowed = ['new', 'read', 'replied', 'archived'];
		if (!in_array($status, $allowed)) return false;

		$db = $this->getDatabase();
		$updates = ['status' => $db->quote($status)];
		if ($status === 'read') {
			$updates['read_at'] = $db->quote(Factory::getDate()->toSql());
		}
		if ($status === 'replied') {
			$updates['replied_at'] = $db->quote(Factory::getDate()->toSql());
		}

		$query = $db->getQuery(true)
			->update($db->quoteName('#__kokedama_messages'))
			->set(array_map(fn($k, $v) => $db->quoteName($k) . ' = ' . $v, array_keys($updates), $updates))
			->whereIn($db->quoteName('id'), $ids);
		$db->setQuery($query);
		$db->execute();
		return true;
	}
}
