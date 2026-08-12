<?php
namespace Kokedama\Component\Kokedama\Site\View\Booking;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
class HtmlView extends HtmlView
{
	protected $form;
	protected $params;
	protected $services;
	protected $events;
	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->params = \Joomla\CMS\Factory::getApplication()->getParams('com_kokedama');
		// Load services and events for dropdowns
		$db = \Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(true)->select('id, title')->from('#__kokedama_services')->where('published = 1')->order('ordering');
		$db->setQuery($query);
		$this->services = $db->loadObjectList();
		$query = $db->getQuery(true)->select('id, title, event_date')->from('#__kokedama_events')->where('published = 1')->where('event_date >= CURDATE()')->order('event_date');
		$db->setQuery($query);
		$this->events = $db->loadObjectList();
		$doc = \Joomla\CMS\Factory::getApplication()->getDocument();
		$doc->setTitle('Prenota — Kokedama & Sculture Naturali');
		return parent::display($tpl);
	}
}
