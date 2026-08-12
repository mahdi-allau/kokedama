<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

// Check access
$app = Factory::getApplication();
$user = $app->getIdentity();

if (!$user->authorise('core.manage', 'com_kokedama')) {
	throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

// Get input
$input = $app->input;
$task = $input->get('task', 'display');

// Load backend language
$app->getLanguage()->load('com_kokedama', JPATH_ADMINISTRATOR);

// Execute controller
$controller = BaseController::getInstance('Kokedama');
$controller->execute($task);
$controller->redirect();
