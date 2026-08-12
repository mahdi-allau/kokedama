<?php
$db = getDB();
$items = $db->query("SELECT * FROM events WHERE published = 1 AND event_date >= date('now') ORDER BY event_date, event_time")->fetchAll();
$past  = $db->query("SELECT * FROM events WHERE published = 1 AND event_date < date('now') ORDER BY event_date DESC LIMIT 3")->fetchAll();

$pageTitle = 'Eventi & Workshop';
$activePage = 'events';
$metaDescription = 'Calendario dei workshop di kokedama e degli eventi a ' . getSetting('business_city', 'Ferrara') . '. Posti limitati, adatti anche ai principianti.';
require __DIR__ . '/../templates/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Percorso">
      <a href="<?php echo APP_URL; ?>/">Home</a><i class="fas fa-chevron-right"></i><span>Eventi</span>
    </nav>
    <span class="eyebrow eyebrow--center">Calendario</span>
    <h1 class="display-2">Eventi &amp; <span class="accent">workshop</span></h1>
    <p class="lead">Laboratori pratici in piccoli gruppi, adatti anche a chi non ha mai toccato una pianta. Materiali sempre inclusi.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($items): ?>
    <div class="row">
      <?php foreach ($items as $i => $item): $soon = relativeDay($item['event_date']); ?>
      <article class="card" data-reveal style="--reveal-delay:<?php echo ($i % 3) * 0.1; ?>s">
        <div class="card-media">
          <?php renderMedia($item['image'], $item['title'], 'fas fa-calendar-day'); ?>
          <?php if ($soon): ?><span class="card-media-tag card-media-tag--clay"><?php echo e($soon); ?></span><?php endif; ?>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center gap-2">
            <span class="badge badge-light"><?php echo e(ucfirst($item['event_type'])); ?></span>
            <span class="price"><?php echo formatPrice($item['price']); ?><?php echo $item['price'] ? '<small> / persona</small>' : ''; ?></span>
          </div>
          <h2 class="card-title"><a href="<?php echo APP_URL; ?>/event/<?php echo e($item['slug']); ?>"><?php echo e($item['title']); ?></a></h2>
          <p class="meta-line"><i class="far fa-calendar"></i> <?php echo e(formatDate($item['event_date'], true)); ?></p>
          <p class="meta-line"><i class="far fa-clock"></i> <?php echo e(formatTime($item['event_time'])); ?><?php echo $item['end_time'] ? ' – ' . e(formatTime($item['end_time'])) : ''; ?></p>
          <p class="card-text"><?php echo e(truncate($item['description'], 110)); ?></p>
          <div class="card-meta">
            <span class="meta-line"><i class="fas fa-users"></i> Max <?php echo (int)$item['max_participants']; ?> posti</span>
            <span class="btn btn-outline-kokedama btn-sm btn-arrow">Prenota <i class="fas fa-arrow-right"></i></span>
          </div>
        </div>
        <a href="<?php echo APP_URL; ?>/event/<?php echo e($item['slug']); ?>" class="card-link" aria-label="Dettagli di <?php echo e($item['title']); ?>"></a>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <i class="far fa-calendar"></i>
      <h3>Nessun evento in calendario</h3>
      <p>Le nuove date arrivano a breve. Lasciaci un contatto e ti avvisiamo appena apriamo le iscrizioni.</p>
      <p class="mt-4"><a href="<?php echo APP_URL; ?>/contact" class="btn btn-kokedama">Avvisami</a></p>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($past): ?>
<section class="section bg-light">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <span class="eyebrow eyebrow--center">Archivio</span>
      <h2 class="display-4">Appuntamenti passati</h2>
      <p>Ti sei perso questi? Molti li riproponiamo: scrivici per essere avvisato.</p>
    </div>
    <div class="row">
      <?php foreach ($past as $item): ?>
      <article class="card" data-reveal style="opacity:.9">
        <div class="card-media">
          <?php renderMedia($item['image'], $item['title'], 'fas fa-calendar-day'); ?>
          <span class="card-media-tag">Concluso</span>
        </div>
        <div class="card-body">
          <h3 class="card-title"><?php echo e($item['title']); ?></h3>
          <p class="meta-line"><i class="far fa-calendar"></i> <?php echo e(formatDate($item['event_date'])); ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/../templates/footer.php'; ?>
