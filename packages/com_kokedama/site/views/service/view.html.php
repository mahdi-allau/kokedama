<?php
namespace Kokedama\Component\Kokedama\Site\View\Service;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
class HtmlView extends HtmlView
{
	protected $item;
	protected $params;
	public function display($tpl = null)
	{
		$this->item = $this->get('Item');
		$this->params = \Joomla\CMS\Factory::getApplication()->getParams('com_kokedama');
		$doc = \Joomla\CMS\Factory::getApplication()->getDocument();
		if ($this->item) {
			$doc->setTitle($this->item->meta_title ?: $this->item->title . ' — Servizi');
			$doc->setMetaData('description', $this->item->meta_desc ?: $this->params->get('meta_desc_default'));
		}
		return parent::display($tpl);
	}
}
