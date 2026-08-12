<?php
$db = getDB();
$items = $db->query("SELECT * FROM services WHERE published = 1 ORDER BY ordering, id")->fetchAll();
$pageTitle = 'Servizi';
$activePage = 'services';
$metaDescription = 'Kokedama su misura, composizioni vegetali e workshop a ' . getSetting('business_city', 'Ferrara') . '. Scopri servizi, prezzi e durata.';
require __DIR__ . '/../templates/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Percorso">
      <a href="<?php echo APP_URL; ?>/">Home</a><i class="fas fa-chevron-right"></i><span>Servizi</span>
    </nav>
    <span class="eyebrow eyebrow--center">Il catalogo</span>
    <h1 class="display-2">Servizi</h1>
    <p class="lead">Creazioni artigianali e laboratori dedicati all'arte del kokedama. Ogni prezzo è indicativo: per richieste su misura scrivici pure.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($items): ?>
    <div class="row">
      <?php foreach ($items as $i => $item): ?>
      <article class="card" data-reveal style="--reveal-delay:<?php echo ($i % 3) * 0.1; ?>s">
        <div class="card-media">
          <?php renderMedia($item['image'], $item['title'], 'fas fa-spa'); ?>
          <?php if ($item['featured']): ?><span class="card-media-tag card-media-tag--clay">Più richiesto</span><?php endif; ?>
        </div>
        <div class="card-body">
          <h2 class="card-title"><a href="<?php echo APP_URL; ?>/service/<?php echo e($item['slug']); ?>"><?php echo e($item['title']); ?></a></h2>
          <p class="card-text"><?php echo e(truncate($item['short_desc'] ?: $item['description'], 130)); ?></p>
          <?php if ($item['duration']): ?>
          <p class="meta-line"><i class="far fa-clock"></i> Durata <?php echo (int)$item['duration']; ?> minuti</p>
          <?php endif; ?>
          <div class="card-meta">
            <span class="price"><?php echo formatPrice($item['price']); ?></span>
            <span class="btn btn-outline-kokedama btn-sm btn-arrow">Scopri <i class="fas fa-arrow-right"></i></span>
          </div>
        </div>
        <a href="<?php echo APP_URL; ?>/service/<?php echo e($item['slug']); ?>" class="card-link" aria-label="Dettagli di <?php echo e($item['title']); ?>"></a>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <i class="fas fa-spa"></i>
      <h3>Nessun servizio pubblicato</h3>
      <p>Il catalogo è in aggiornamento. <a href="<?php echo APP_URL; ?>/contact">Contattaci</a> e ti raccontiamo cosa possiamo realizzare.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <div class="cta-band" data-reveal>
      <h2 class="display-4">Hai in mente qualcosa di diverso?</h2>
      <p class="lead">Composizioni per eventi, allestimenti per negozi, bomboniere verdi: raccontaci l'idea e la realizziamo insieme.</p>
      <div class="cta-buttons">
        <a href="<?php echo APP_URL; ?>/contact" class="btn btn-white btn-lg"><i class="fas fa-envelope"></i> Richiedi un preventivo</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../templates/footer.php'; ?>
