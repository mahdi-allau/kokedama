<?php
namespace Kokedama\Component\Kokedama\Site\View\Contact;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
class HtmlView extends HtmlView
{
	protected $form;
	protected $params;
	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->params = \Joomla\CMS\Factory::getApplication()->getParams('com_kokedama');
		$doc = \Joomla\CMS\Factory::getApplication()->getDocument();
		$doc->setTitle('Contatti — Kokedama & Sculture Naturali');
		return parent::display($tpl);
	}
}
