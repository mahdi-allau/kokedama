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
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Mail\MailTemplate;

class BookingModel extends FormModel
{
	protected function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm('com_kokedama.booking', 'booking', ['control' => 'jform', 'load_data' => $loadData]);
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_kokedama.booking.data', []);
		return $data;
	}

	public function save($data)
	{
		$db = $this->getDatabase();
		$params = Factory::getApplication()->getParams('com_kokedama');

		// Sanitize and validate
		$booking = new \stdClass();
		$booking->booking_type = in_array($data['booking_type'] ?? '', ['servizio', 'evento']) ? $data['booking_type'] : 'servizio';
		$booking->service_id = !empty($data['service_id']) ? (int)$data['service_id'] : null;
		$booking->event_id = !empty($data['event_id']) ? (int)$data['event_id'] : null;
		$booking->booking_date = $data['booking_date'] ?? null;
		$booking->booking_time = $data['booking_time'] ?? null;
		$booking->participants = max(1, min(100, (int)($data['participants'] ?? 1)));
		$booking->first_name = htmlspecialchars(strip_tags($data['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
		$booking->last_name = htmlspecialchars(strip_tags($data['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
		$booking->email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
		$booking->phone = htmlspecialchars(strip_tags($data['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
		$booking->notes = htmlspecialchars(strip_tags($data['notes'] ?? ''), ENT_QUOTES, 'UTF-8');
		$booking->consent_gdpr = !empty($data['consent_gdpr']) ? 1 : 0;
		$booking->ip_address = Factory::getApplication()->input->server->get('REMOTE_ADDR', '', 'STRING');
		$booking->status = 'pending';
		$booking->created = Factory::getDate()->toSql();

		if (empty($booking->email) || empty($booking->first_name) || empty($booking->last_name)) {
			$this->setError('Dati obbligatori mancanti.');
			return false;
		}

		if ($booking->booking_type === 'evento' && empty($booking->event_id)) {
			$this->setError('Seleziona un evento.');
			return false;
		}
		if ($booking->booking_type === 'servizio' && empty($booking->service_id)) {
			$this->setError('Seleziona un servizio.');
			return false;
		}

		// Check event availability
		if ($booking->booking_type === 'evento' && $booking->event_id) {
			$query = $db->getQuery(true);
			$query->select('max_participants')
				->from($db->quoteName('#__kokedama_events'))
				->where($db->quoteName('id') . ' = ' . (int)$booking->event_id);
			$db->setQuery($query);
			$max = (int)$db->loadResult();

			$query = $db->getQuery(true);
			$query->select('SUM(participants)')
				->from($db->quoteName('#__kokedama_bookings'))
				->where($db->quoteName('event_id') . ' = ' . (int)$booking->event_id)
				->where($db->quoteName('status') . ' IN ("pending", "confirmed")');
			$db->setQuery($query);
			$booked = (int)$db->loadResult();

			if (($booked + $booking->participants) > $max) {
				$this->setError('Posti insufficienti per questo evento.');
				return false;
			}
		}

		// Insert
		$db->insertObject('#__kokedama_bookings', $booking);
		$booking->id = $db->insertid();

		// Send emails
		$this->sendNotificationEmails($booking, $params);

		return true;
	}

	protected function sendNotificationEmails($booking, $params)
	{
		$app = Factory::getApplication();
		$mailer = Factory::getMailer();

		$adminEmail = $params->get('booking_email_to', $params->get('business_email', 'anda22@gmail.com'));
		$fromEmail = $params->get('booking_email_from', $app->get('mailfrom'));
		$fromName = $params->get('business_name', 'Kokedama & Sculture Naturali');

		$subjectAdmin = $params->get('booking_subject_admin', 'Nuova prenotazione ricevuta');
		$subjectCustomer = $params->get('booking_subject_customer', 'Conferma ricezione prenotazione');

		// Admin notification
		$bodyAdmin = "Nuova prenotazione ricevuta:\n\n";
		$bodyAdmin .= "Tipo: " . ucfirst($booking->booking_type) . "\n";
		$bodyAdmin .= "Cliente: " . $booking->first_name . " " . $booking->last_name . "\n";
		$bodyAdmin .= "Email: " . $booking->email . "\n";
		$bodyAdmin .= "Telefono: " . ($booking->phone ?: 'N/D') . "\n";
		$bodyAdmin .= "Data: " . $booking->booking_date . " " . ($booking->booking_time ?: '') . "\n";
		$bodyAdmin .= "Partecipanti: " . $booking->participants . "\n";
		$bodyAdmin .= "Note: " . ($booking->notes ?: 'Nessuna') . "\n";
		$bodyAdmin .= "\nGestisci la prenotazione dall'area amministrativa.";

		$mailer->sendMail($fromEmail, $fromName, $adminEmail, $subjectAdmin, $bodyAdmin);

		// Customer confirmation
		$bodyCustomer = "Gentile " . $booking->first_name . ",\n\n";
		$bodyCustomer .= "Abbiamo ricevuto la tua richiesta di prenotazione.\n\n";
		$bodyCustomer .= "Riepilogo:\n";
		$bodyCustomer .= "Tipo: " . ucfirst($booking->booking_type) . "\n";
		$bodyCustomer .= "Data richiesta: " . $booking->booking_date . " " . ($booking->booking_time ?: '') . "\n";
		$bodyCustomer .= "Partecipanti: " . $booking->participants . "\n";
		$bodyCustomer .= "\nTi contatteremo presto per confermare la prenotazione.\n\n";
		$bodyCustomer .= "Cordiali saluti,\n" . $fromName;

		$mailer->sendMail($fromEmail, $fromName, $booking->email, $subjectCustomer, $bodyCustomer);
	}
}
