<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

namespace Kokedama\Component\Kokedama\Site\View\Services;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;

class HtmlView extends HtmlView
{
	protected $items;
	protected $params;
	protected $state;

	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->params = \Joomla\CMS\Factory::getApplication()->getParams('com_kokedama');
		$this->state = $this->get('State');

		// Set document title
		$document = \Joomla\CMS\Factory::getApplication()->getDocument();
		$document->setTitle('Servizi — ' . $this->params->get('business_name', 'Kokedama & Sculture Naturali'));
		$document->setMetaData('description', $this->params->get('meta_desc_default'));

		return parent::display($tpl);
	}
}
