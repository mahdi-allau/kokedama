<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Kokedama\Component\Kokedama\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

class ContactController extends FormController
{
	public function save($key = null, $urlVar = null)
	{
		$this->checkToken();
		$app = \Joomla\CMS\Factory::getApplication();
		$model = $this->getModel('Contact');
		$data = $app->input->post->get('jform', [], 'array');

		if ($model->save($data)) {
			$app->enqueueMessage('Messaggio inviato con successo! Ti risponderemo al più presto.', 'success');
			$this->setRedirect(Route::_('index.php?option=com_kokedama&view=contact', false));
		} else {
			$app->enqueueMessage($model->getError(), 'error');
			$app->setUserState('com_kokedama.contact.data', $data);
			$this->setRedirect(Route::_('index.php?option=com_kokedama&view=contact', false));
		}
		return true;
	}
}
