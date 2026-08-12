<?php
namespace Kokedama\Component\Kokedama\Site\View\Gallery;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
class HtmlView extends HtmlView
{
	protected $items;
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$doc = \Joomla\CMS\Factory::getApplication()->getDocument();
		$doc->setTitle('Galleria — Kokedama & Sculture Naturali');
		return parent::display($tpl);
	}
}
