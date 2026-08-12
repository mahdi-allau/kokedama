<?php
namespace Kokedama\Component\Kokedama\Site\View\Event;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
class HtmlView extends HtmlView
{
	protected $item;
	public function display($tpl = null)
	{
		$this->item = $this->get('Item');
		$doc = \Joomla\CMS\Factory::getApplication()->getDocument();
		if ($this->item) {
			$doc->setTitle($this->item->title . ' — Eventi');
		}
		return parent::display($tpl);
	}
}
