<?php
namespace Kokedama\Component\Kokedama\Administrator\Table;
defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
class MessageTable extends Table
{
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__kokedama_messages', 'id', $db);
	}
}
