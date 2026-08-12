<?php
namespace Kokedama\Component\Kokedama\Administrator\View\Dashboard;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
class HtmlView extends HtmlView
{
	protected $stats;
	public function display($tpl = null)
	{
		$this->stats = $this->get('Stats');
		return parent::display($tpl);
	}
}
