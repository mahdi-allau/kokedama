<?php
/**
 * @package     Kokedama
 * @subpackage  com_kokedama
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

$app = Factory::getApplication();
$input = $app->input;
$task = $input->get('task', 'display');

$app->getLanguage()->load('com_kokedama', JPATH_BASE . '/components/com_kokedama');

$controller = BaseController::getInstance('Kokedama');
$controller->execute($task);
$controller->redirect();
