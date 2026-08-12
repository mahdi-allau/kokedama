<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Kokedama\Component\Kokedama\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class BookingsController extends AdminController
{
	protected $text_prefix = 'COM_KOKEDAMA';

	public function getModel($name = 'Booking', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function confirm()
	{
		$this->changeStatus('confirmed');
	}

	public function reject()
	{
		$this->changeStatus('rejected');
	}

	public function cancelBooking()
	{
		$this->changeStatus('cancelled');
	}

	public function complete()
	{
		$this->changeStatus('completed');
	}

	protected function changeStatus($status)
	{
		$this->checkToken();
		$ids = $this->input->get('cid', [], 'array');
		if (empty($ids)) {
			\Joomla\CMS\Factory::getApplication()->enqueueMessage('Nessun elemento selezionato.', 'warning');
		} else {
			$model = $this->getModel();
			if ($model->changeStatus($ids, $status)) {
				\Joomla\CMS\Factory::getApplication()->enqueueMessage('Stato aggiornato con successo.', 'success');
			}
		}
		$this->setRedirect('index.php?option=com_kokedama&view=bookings');
	}
}
