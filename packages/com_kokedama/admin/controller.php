<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Kokedama\Component\Kokedama\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
	protected $default_view = 'dashboard';

	public function display($cachable = false, $urlparams = [])
	{
		$view = $this->input->get('view', $this->default_view);
		$layout = $this->input->get('layout', 'default');
		$id = $this->input->getInt('id');

		// Check edit form view
		if ($view === 'service' && $layout === 'edit' && !$this->checkEditId('com_kokedama.edit.service', $id)) {
			$this->setMessage(\Joomla\CMS\Language\Text::_('JLIB_APPLICATION_ERROR_UNHELD_ID'), 'error');
			$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_kokedama&view=services', false));
			return false;
		}

		return parent::display($cachable, $urlparams);
	}
}
