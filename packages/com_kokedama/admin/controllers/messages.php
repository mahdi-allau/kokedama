<?php
namespace Kokedama\Component\Kokedama\Administrator\Controller;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\AdminController;
class MessagesController extends AdminController
{
	protected $text_prefix = 'COM_KOKEDAMA';
	public function getModel($name = 'Message', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}
	public function archive()
	{
		$this->changeStatus('archived');
	}
	public function markRead()
	{
		$this->changeStatus('read');
	}
	protected function changeStatus($status)
	{
		$this->checkToken();
		$ids = $this->input->get('cid', [], 'array');
		if (empty($ids)) {
			\Joomla\CMS\Factory::getApplication()->enqueueMessage('Nessun elemento selezionato.', 'warning');
		} else {
			$model = $this->getModel();
			if ($model->changeStatus($ids, $status)) {
				\Joomla\CMS\Factory::getApplication()->enqueueMessage('Stato aggiornato con successo.', 'success');
			}
		}
		$this->setRedirect('index.php?option=com_kokedama&view=messages');
	}
}
