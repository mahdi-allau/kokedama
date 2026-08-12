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
use Joomla\CMS\MVC\Model\FormModel;

class ContactModel extends FormModel
{
	protected function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm('com_kokedama.contact', 'contact', ['control' => 'jform', 'load_data' => $loadData]);
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		return Factory::getApplication()->getUserState('com_kokedama.contact.data', []);
	}

	public function save($data)
	{
		$db = $this->getDatabase();
		$params = Factory::getApplication()->getParams('com_kokedama');

		$message = new \stdClass();
		$message->name = htmlspecialchars(strip_tags($data['name'] ?? ''), ENT_QUOTES, 'UTF-8');
		$message->email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
		$message->phone = htmlspecialchars(strip_tags($data['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
		$message->subject = htmlspecialchars(strip_tags($data['subject'] ?? ''), ENT_QUOTES, 'UTF-8');
		$message->message = htmlspecialchars(strip_tags($data['message'] ?? ''), ENT_QUOTES, 'UTF-8');
		$message->consent_gdpr = !empty($data['consent_gdpr']) ? 1 : 0;
		$message->ip_address = Factory::getApplication()->input->server->get('REMOTE_ADDR', '', 'STRING');
		$message->status = 'new';
		$message->created = Factory::getDate()->toSql();

		if (empty($message->email) || empty($message->name) || empty($message->message)) {
			$this->setError('Compila tutti i campi obbligatori.');
			return false;
		}

		$db->insertObject('#__kokedama_messages', $message);

		// Notify admin
		$app = Factory::getApplication();
		$mailer = Factory::getMailer();
		$adminEmail = $params->get('contact_email_to', $params->get('business_email', 'anda22@gmail.com'));
		$fromEmail = $app->get('mailfrom');
		$fromName = $params->get('business_name', 'Kokedama & Sculture Naturali');
		$subject = $params->get('contact_subject', 'Nuovo messaggio dal sito');

		$body = "Nuovo messaggio dal sito web:\n\n";
		$body .= "Da: " . $message->name . " <" . $message->email . ">\n";
		$body .= "Telefono: " . ($message->phone ?: 'N/D') . "\n";
		$body .= "Oggetto: " . ($message->subject ?: 'Nessuno') . "\n";
		$body .= "Messaggio:\n" . $message->message . "\n";

		$mailer->sendMail($fromEmail, $fromName, $adminEmail, $subject, $body);

		return true;
	}
}
