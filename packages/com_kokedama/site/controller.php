<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Kokedama\Component\Kokedama\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
	protected $default_view = 'services';

	public function display($cachable = false, $urlparams = [])
	{
		$view = $this->input->get('view', $this->default_view);
		$layout = $this->input->get('layout', 'default');

		return parent::display($cachable, $urlparams);
	}
}
