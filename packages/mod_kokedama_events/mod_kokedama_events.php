<?php defined('_JEXEC') or die;
require_once __DIR__ . '/helper.php';
$items = ModKokedamaEventsHelper::getItems($params);
require \Joomla\CMS\Helper\ModuleHelper::getLayoutPath('mod_kokedama_events', $params->get('layout', 'default'));
