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
use Joomla\CMS\Uri\Uri;

class BookingController extends FormController
{
	protected $view_item = 'booking';
	protected $view_list = 'services';

	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		$this->checkToken();

		$app = \Joomla\CMS\Factory::getApplication();
		$model = $this->getModel('Booking');
		$data = $app->input->post->get('jform', [], 'array');

		if ($model->save($data)) {
			$app->enqueueMessage('Prenotazione inviata con successo! Ti contatteremo presto per confermare.', 'success');
			$this->setRedirect(Route::_('index.php?option=com_kokedama&view=services', false));
		} else {
			$app->enqueueMessage($model->getError(), 'error');
			$app->setUserState('com_kokedama.booking.data', $data);
			$this->setRedirect(Route::_('index.php?option=com_kokedama&view=booking', false));
		}

		return true;
	}
}
