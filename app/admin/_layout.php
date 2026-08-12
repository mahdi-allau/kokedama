<?php
/**
 * Layout condiviso del pannello di gestione.
 *
 * Uso:
 *   $adminTitle = 'Prenotazioni';
 *   $adminPage  = 'bookings';
 *   require __DIR__ . '/_layout.php';   // apre la pagina
 *   ... contenuto ...
 *   require __DIR__ . '/_layout_end.php';
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
initDatabase();

$db = getDB();

// Contatori mostrati come pallini rossi nel menu: fanno capire a colpo
// d'occhio dove c'è qualcosa da fare.
$badgeBookings = (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$badgeMessages = (int)$db->query("SELECT COUNT(*) FROM messages WHERE status = 'new'")->fetchColumn();

$adminNav = [
    'contenuti' => [
        'dashboard' => ['dashboard.php', 'fas fa-gauge-high', 'Riepilogo', 0],
        'bookings'  => ['bookings.php',  'fas fa-calendar-check', 'Prenotazioni', $badgeBookings],
        'messages'  => ['messages.php',  'fas fa-envelope', 'Messaggi', $badgeMessages],
    ],
    'sito' => [
        'services'  => ['services.php', 'fas fa-spa', 'Servizi', 0],
        'events'    => ['events.php',   'fas fa-calendar-day', 'Eventi & workshop', 0],
        'projects'  => ['projects.php', 'fas fa-seedling', 'Progetti', 0],
        'gallery'   => ['gallery.php',  'fas fa-images', 'Galleria foto', 0],
    ],
    'configurazione' => [
        'settings'  => ['settings.php', 'fas fa-sliders', 'Impostazioni', 0],
    ],
];

$adminPage  = $adminPage ?? '';
$adminTitle = $adminTitle ?? 'Pannello';
$flash = takeFlash();
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?php echo e($adminTitle); ?> — Gestione Kokedama</title>
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%F0%9F%8C%B1%3C/text%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/admin.css?v=3">
</head>
<body>
<div class="admin-shell">

  <aside class="sidebar" id="sidebar">
    <a href="dashboard.php" class="sidebar-brand">
      <span class="mark"><i class="fas fa-leaf"></i></span>
      <span>
        <strong>Kokedama</strong>
        <small>Gestione sito</small>
      </span>
    </a>

    <?php foreach ($adminNav as $groupLabel => $links): ?>
    <div>
      <div class="sidebar-label"><?php echo e(ucfirst($groupLabel)); ?></div>
      <nav>
        <?php foreach ($links as $key => [$href, $icon, $label, $count]): ?>
        <a href="<?php echo e($href); ?>"<?php echo $adminPage === $key ? ' class="active" aria-current="page"' : ''; ?>>
          <i class="<?php echo e($icon); ?>"></i>
          <span><?php echo e($label); ?></span>
          <?php if ($count > 0): ?><span class="count"><?php echo $count; ?></span><?php endif; ?>
        </a>
        <?php endforeach; ?>
      </nav>
    </div>
    <?php endforeach; ?>

    <div class="sidebar-foot">
      <a href="<?php echo APP_URL; ?>/" target="_blank"><i class="fas fa-arrow-up-right-from-square"></i> Vedi il sito</a>
      <a href="logout.php"><i class="fas fa-right-from-bracket"></i> Esci</a>
    </div>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="Apri il menu"><i class="fas fa-bars"></i></button>
      <h1><?php echo e($adminTitle); ?></h1>
      <div class="spacer"></div>
      <?php if (!empty($adminAction)): ?><?php echo $adminAction; ?><?php endif; ?>
    </header>

    <div class="admin-content">
      <?php if ($flash): ?>
      <div class="alert alert-<?php echo e($flash['type']); ?>" role="status">
        <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-circle-check' : ($flash['type'] === 'error' ? 'fa-circle-exclamation' : 'fa-circle-info'); ?>"></i>
        <span><?php echo e($flash['message']); ?></span>
      </div>
      <?php endif; ?>
