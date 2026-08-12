<?php
/**
 * @package     Kokedama
 * @subpackage  mod_kokedama_services
 * @copyright   Copyright (C) 2026 Kokedama Sculture Naturali. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/helper.php';

$items = ModKokedamaServicesHelper::getItems($params);
require \Joomla\CMS\Helper\ModuleHelper::getLayoutPath('mod_kokedama_services', $params->get('layout', 'default'));
