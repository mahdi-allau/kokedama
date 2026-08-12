<?php
namespace Kokedama\Component\Kokedama\Administrator\View\Messages;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\ListView;
class HtmlView extends ListView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $filterForm;
	protected $activeFilters;
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		if (count($errors = $this->get('Errors'))) {
			throw new \Exception(implode("\n", $errors));
		}
		$this->addToolbar();
		return parent::display($tpl);
	}
	protected function addToolbar()
	{
		$toolbar = \Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar');
		$toolbar->title('COM_KOKEDAMA_MESSAGES', 'envelope');
	}
}
