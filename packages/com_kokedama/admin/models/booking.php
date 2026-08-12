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
use Joomla\CMS\Mail\MailerFactoryInterface;

class BookingModel extends AdminModel
{
	protected $text_prefix = 'COM_KOKEDAMA';

	public function getTable($type = 'Booking', $prefix = 'Table', $config = [])
	{
		return $this->getMVCFactory()->createTable($type, $prefix, $config);
	}

	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm('com_kokedama.booking', 'booking', ['control' => 'jform', 'load_data' => $loadData]);
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_kokedama.edit.booking.data', []);
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	public function changeStatus($ids, $status)
	{
		if (empty($ids)) return false;
		$allowed = ['pending', 'confirmed', 'rejected', 'cancelled', 'completed'];
		if (!in_array($status, $allowed)) return false;

		$db = $this->getDatabase();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__kokedama_bookings'))
			->set($db->quoteName('status') . ' = ' . $db->quote($status))
			->set($db->quoteName('modified') . ' = ' . $db->quote(Factory::getDate()->toSql()))
			->whereIn($db->quoteName('id'), $ids);
		$db->setQuery($query);
		$db->execute();

		// Send email notification on confirm/reject
		if (in_array($status, ['confirmed', 'rejected'])) {
			$this->notifyCustomer($ids, $status);
		}

		return true;
	}

	protected function notifyCustomer($ids, $status)
	{
		$params = Factory::getApplication()->getParams('com_kokedama');
		$db = $this->getDatabase();
		$query = $db->getQuery(true)->select('*')->from('#__kokedama_bookings')->whereIn('id', $ids);
		$db->setQuery($query);
		$bookings = $db->loadObjectList();

		$fromEmail = Factory::getApplication()->get('mailfrom');
		$fromName = $params->get('business_name', 'Kokedama & Sculture Naturali');

		foreach ($bookings as $booking) {
			$subject = $status === 'confirmed' ? 'Prenotazione confermata' : 'Prenotazione non confermata';
			$body = "Gentile " . $booking->first_name . ",\n\n";
			if ($status === 'confirmed') {
				$body .= "La tua prenotazione è stata confermata!\n\n";
				$body .= "Dettagli:\n";
				$body .= "Data: " . $booking->booking_date . " " . ($booking->booking_time ?: '') . "\n";
				$body .= "Partecipanti: " . $booking->participants . "\n";
				$body .= "\nTi aspettiamo!\n";
			} else {
				$body .= "Purtroppo non siamo in grado di confermare la tua prenotazione per la data richiesta.\n";
				$body .= "Ti invitiamo a contattarci per trovare un'alternativa.\n";
			}
			$body .= "\nCordiali saluti,\n" . $fromName;

			$mailer = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
			$mailer->setSender([$fromEmail, $fromName]);
			$mailer->addRecipient($booking->email);
			$mailer->setSubject($subject);
			$mailer->setBody($body);
			$mailer->Send();
		}
	}
}
