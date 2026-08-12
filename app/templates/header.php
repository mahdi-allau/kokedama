<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
initDatabase();

/**
 * ATTENZIONE — le variabili di questo file usano tutte il prefisso "tpl".
 * Il template viene incluso nel bel mezzo delle pagine, quindi condivide le
 * loro variabili: un nome generico come $item o $row qui sovrascriverebbe
 * quello della pagina, svuotando i dati subito sotto (è già successo).
 */

$activePage   = $activePage ?? '';
$tplBrandName = getSetting('brand_name', getSetting('business_name', 'Kokedama'));
$tplBrandSub  = getSetting('brand_subtitle', 'Sculture Naturali');
$tplBrandLogo = getSetting('brand_logo', '');
$businessName = getSetting('business_name', 'Kokedama & Sculture Naturali');
$metaDesc     = $metaDescription ?? getSetting('seo_description');
$tplWa           = whatsappLink();

// Voci di menu: unico punto da modificare per aggiungere una pagina.
$tplNav = [
    'home'     => ['url' => APP_URL . '/',         'label' => 'Home'],
    'about'    => ['url' => APP_URL . '/about',    'label' => 'Chi siamo'],
    'services' => ['url' => APP_URL . '/services', 'label' => 'Servizi'],
    'projects' => ['url' => APP_URL . '/projects', 'label' => 'Progetti'],
    'gallery'  => ['url' => APP_URL . '/gallery',  'label' => 'Galleria'],
    'events'   => ['url' => APP_URL . '/events',   'label' => 'Eventi'],
    'contact'  => ['url' => APP_URL . '/contact',  'label' => 'Contatti'],
];
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($pageTitle ?? 'Kokedama & Sculture Naturali'); ?> — <?php echo e($businessName); ?> | <?php echo e(getSetting('business_city', 'Ferrara')); ?></title>
<meta name="description" content="<?php echo e($metaDesc); ?>">
<meta name="theme-color" content="#3F6247">
<link rel="canonical" href="<?php echo e(APP_URL . ($_SERVER['REQUEST_URI'] ?? '/')); ?>">

<!-- Open Graph: anteprima quando il link viene condiviso su Facebook/WhatsApp -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?php echo e($businessName); ?>">
<meta property="og:title" content="<?php echo e($pageTitle ?? $businessName); ?>">
<meta property="og:description" content="<?php echo e($metaDesc); ?>">
<meta property="og:locale" content="it_IT">
<?php if (!empty($ogImage) && mediaExists($ogImage)): ?>
<meta property="og:image" content="<?php echo e(mediaUrl($ogImage)); ?>">
<?php endif; ?>

<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%F0%9F%8C%B1%3C/text%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?php echo APP_BASE; ?>/assets/css/style.css?v=3">

<!-- Dati strutturati: aiuta Google a mostrare indirizzo, telefono e orari -->
<script type="application/ld+json">
<?php echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => $businessName,
    'description' => $metaDesc,
    'url' => APP_URL,
    'telephone' => getSetting('business_phone'),
    'email' => getSetting('business_email'),
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => getSetting('business_address'),
        'postalCode' => getSetting('business_zip'),
        'addressLocality' => getSetting('business_city'),
        'addressCountry' => 'IT',
    ],
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); // le slash restano escapate: impedisce a un "</script>" nei dati di rompere la pagina ?>
</script>
</head>
<body>
<a class="skip-link" href="#main">Vai al contenuto</a>

<!-- Barra informativa (nascosta su mobile: lì c'è la barra azioni in basso) -->
<div class="topbar">
  <div class="container">
    <div class="topbar-items">
      <span><i class="fas fa-map-marker-alt"></i> <?php echo e(fullAddress()); ?></span>
      <?php $tplToday = todayHours(); if ($tplToday): ?>
      <span><i class="far fa-clock"></i> Oggi: <?php echo e($tplToday['value']); ?></span>
      <?php endif; ?>
    </div>
    <div class="topbar-items">
      <a href="tel:<?php echo e(telHref()); ?>"><i class="fas fa-phone"></i> <?php echo e(getSetting('business_phone')); ?></a>
      <div class="topbar-social">
        <?php if (getSetting('business_facebook')): ?><a href="<?php echo e(getSetting('business_facebook')); ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
        <?php if (getSetting('business_instagram')): ?><a href="<?php echo e(getSetting('business_instagram')); ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a><?php endif; ?>
      </div>
    </div>
  </div>
</div>

<nav class="navbar" id="navbar">
  <div class="container">
    <a href="<?php echo APP_URL; ?>/" class="navbar-brand">
      <?php if (mediaExists($tplBrandLogo)): ?>
      <img class="brand-logo" src="<?php echo e(mediaUrl($tplBrandLogo)); ?>" alt="<?php echo e($tplBrandName); ?>">
      <?php else: ?>
      <span class="brand-mark"><i class="fas fa-leaf" aria-hidden="true"></i></span>
      <?php endif; ?>
      <span class="brand-text">
        <span class="brand-name"><?php echo e($tplBrandName); ?></span>
        <?php if ($tplBrandSub !== ''): ?><span class="brand-sub"><?php echo e($tplBrandSub); ?></span><?php endif; ?>
      </span>
    </a>

    <ul class="navbar-nav">
      <?php foreach ($tplNav as $tplKey => $tplLink): ?>
      <li><a href="<?php echo e($tplLink['url']); ?>"<?php echo $activePage === $tplKey ? ' class="active" aria-current="page"' : ''; ?>><?php echo e($tplLink['label']); ?></a></li>
      <?php endforeach; ?>
      <li class="nav-cta"><a href="<?php echo APP_URL; ?>/booking" class="btn btn-kokedama btn-sm"><i class="fas fa-calendar-check"></i> Prenota</a></li>
    </ul>

    <button class="nav-toggle" id="navToggle" aria-label="Apri il menu" aria-expanded="false" aria-controls="mobileDrawer">
      <span></span>
    </button>
  </div>
</nav>

<!-- Menu a tutta pagina per smartphone e tablet -->
<div class="mobile-drawer" id="mobileDrawer">
  <nav>
    <?php foreach ($tplNav as $tplKey => $tplLink): ?>
    <a href="<?php echo e($tplLink['url']); ?>"<?php echo $activePage === $tplKey ? ' class="active"' : ''; ?>>
      <?php echo e($tplLink['label']); ?><i class="fas fa-arrow-right" aria-hidden="true"></i>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="drawer-footer">
    <a href="<?php echo APP_URL; ?>/booking" class="btn btn-kokedama btn-lg btn-block"><i class="fas fa-calendar-check"></i> Prenota ora</a>
    <?php if ($tplWa): ?>
    <a href="<?php echo e($tplWa); ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-block"><i class="fab fa-whatsapp"></i> Scrivici su WhatsApp</a>
    <?php endif; ?>
    <div class="drawer-contact">
      <a href="tel:<?php echo e(telHref()); ?>"><i class="fas fa-phone"></i> <?php echo e(getSetting('business_phone')); ?></a>
      <span><i class="fas fa-map-marker-alt"></i> <?php echo e(fullAddress()); ?></span>
    </div>
  </div>
</div>

<?php $tplFlash = takeFlash(); if ($tplFlash): ?>
<div class="flash-wrap">
  <div class="alert alert-<?php echo $tplFlash['type'] === 'success' ? 'success' : 'error'; ?>" role="status">
    <i class="fas <?php echo $tplFlash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
    <span><?php echo e($tplFlash['message']); ?></span>
  </div>
</div>
<?php endif; ?>

<main id="main">
