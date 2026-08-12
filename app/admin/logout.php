<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
adminLogout();
header('Location: ' . APP_URL . '/admin/');
exit;
