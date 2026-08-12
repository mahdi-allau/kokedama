<?php
$db = getDB();
$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare("SELECT * FROM services WHERE slug = ? AND published = 1");
$stmt->execute([$slug]);
$item = $stmt->fetch();
if (!$item) { header('Location: ' . APP_URL . '/services'); exit; }

// Progetti realizzati con questo servizio
$rel = $db->prepare("SELECT * FROM projects WHERE published = 1 AND service_id = ? ORDER BY ordering LIMIT 3");
$rel->execute([$item['id']]);
$related = $rel->fetchAll();

$others = $db->prepare("SELECT * FROM services WHERE published = 1 AND id != ? ORDER BY ordering LIMIT 3");
$others->execute([$item['id']]);
$others = $others->fetchAll();

$pageTitle = $item['title'];
$activePage = 'services';
$metaDescription = truncate($item['short_desc'] ?: $item['description'], 155);
$ogImage = $item['image'];
require __DIR__ . '/../templates/header.php';
?>

<section class="section">
  <div class="container">
    <nav class="breadcrumb" style="justify-content:flex-start" aria-label="Percorso">
      <a href="<?php echo APP_URL; ?>/">Home</a><i class="fas fa-chevron-right"></i>
      <a href="<?php echo APP_URL; ?>/services">Servizi</a><i class="fas fa-chevron-right"></i>
      <span><?php echo e($item['title']); ?></span>
    </nav>

    <div class="feature-row" style="margin-bottom:0">
      <div class="feature-image">
        <div class="feature-image-inner"><?php renderMedia($item['image'], $item['title'], 'fas fa-spa', false); ?></div>
      </div>
      <div class="feature-content">
        <span class="eyebrow">Servizio</span>
        <h1 class="display-3" style="margin-bottom:1rem"><?php echo e($item['title']); ?></h1>

        <div class="d-flex gap-3 flex-wrap mb-4">
          <span class="badge badge-green" style="font-size:1rem;padding:.5rem 1.1rem"><?php echo formatPrice($item['price']); ?></span>
          <?php if ($item['duration']): ?>
          <span class="badge badge-light" style="font-size:1rem;padding:.5rem 1.1rem"><i class="far fa-clock"></i> <?php echo (int)$item['duration']; ?> minuti</span>
          <?php endif; ?>
        </div>

        <div class="lead" style="margin-bottom:2rem"><?php echo nl2br(e($item['description'] ?: $item['short_desc'])); ?></div>

        <ul class="feature-list">
          <li><i class="fas fa-check"></i> Tutti i materiali sono inclusi</li>
          <li><i class="fas fa-check"></i> Istruzioni per la cura della pianta</li>
          <li><i class="fas fa-check"></i> Conferma entro 24 ore dalla richiesta</li>
        </ul>

        <div class="hero-buttons">
          <a href="<?php echo APP_URL; ?>/booking?service=<?php echo (int)$item['id']; ?>" class="btn btn-kokedama btn-lg"><i class="fas fa-calendar-check"></i> Prenota questo servizio</a>
          <?php if ($wa = whatsappLink('Ciao! Vorrei informazioni su: ' . $item['title'])): ?>
          <a href="<?php echo e($wa); ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-lg"><i class="fab fa-whatsapp"></i> Chiedi info</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="section bg-light">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <span class="eyebrow eyebrow--center">Esempi reali</span>
      <h2 class="display-4">Realizzati con questo servizio</h2>
    </div>
    <div class="row">
      <?php foreach ($related as $p): ?>
      <article class="card" data-reveal>
        <div class="card-media"><?php renderMedia($p['image'], $p['title'], 'fas fa-seedling'); ?></div>
        <div class="card-body">
          <h3 class="card-title"><a href="<?php echo APP_URL; ?>/project/<?php echo e($p['slug']); ?>"><?php echo e($p['title']); ?></a></h3>
          <?php if ($p['plant_type']): ?><p class="meta-line"><i class="fas fa-leaf"></i> <?php echo e($p['plant_type']); ?></p><?php endif; ?>
        </div>
        <a href="<?php echo APP_URL; ?>/project/<?php echo e($p['slug']); ?>" class="card-link" aria-label="<?php echo e($p['title']); ?>"></a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($others): ?>
<section class="section">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <h2 class="display-4">Altri servizi</h2>
    </div>
    <div class="row">
      <?php foreach ($others as $o): ?>
      <article class="card" data-reveal>
        <div class="card-media"><?php renderMedia($o['image'], $o['title'], 'fas fa-spa'); ?></div>
        <div class="card-body">
          <h3 class="card-title"><a href="<?php echo APP_URL; ?>/service/<?php echo e($o['slug']); ?>"><?php echo e($o['title']); ?></a></h3>
          <p class="card-text"><?php echo e(truncate($o['short_desc'], 90)); ?></p>
          <div class="card-meta"><span class="price"><?php echo formatPrice($o['price']); ?></span></div>
        </div>
        <a href="<?php echo APP_URL; ?>/service/<?php echo e($o['slug']); ?>" class="card-link" aria-label="<?php echo e($o['title']); ?>"></a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/../templates/footer.php'; ?>
