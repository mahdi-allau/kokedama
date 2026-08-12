<?php
$db = getDB();
$items = $db->query("SELECT * FROM gallery WHERE published = 1 ORDER BY ordering, id")->fetchAll();

// Categorie ricavate dalle foto effettivamente pubblicate
$categories = [];
foreach ($items as $it) {
    $cat = trim($it['category'] ?: 'general');
    $categories[$cat] = ($categories[$cat] ?? 0) + 1;
}
ksort($categories);

$catLabels = [
    'general'  => 'Tutte le altre',
    'kokedama' => 'Kokedama',
    'workshop' => 'Workshop',
    'atelier'  => 'Atelier',
    'eventi'   => 'Eventi',
];

$pageTitle = 'Galleria';
$activePage = 'gallery';
$metaDescription = 'Foto di kokedama, composizioni vegetali, workshop e atelier a ' . getSetting('business_city', 'Ferrara') . '.';
require __DIR__ . '/../templates/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Percorso">
      <a href="<?php echo APP_URL; ?>/">Home</a><i class="fas fa-chevron-right"></i><span>Galleria</span>
    </nav>
    <span class="eyebrow eyebrow--center">Immagini</span>
    <h1 class="display-2">Galleria</h1>
    <p class="lead">Kokedama, laboratori e momenti dall'atelier. Tocca una foto per ingrandirla.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($items): ?>

      <?php if (count($categories) > 1): ?>
      <div class="filter-bar" data-filter-bar>
        <button class="filter-chip is-active" data-filter="all">Tutte <span style="opacity:.6">(<?php echo count($items); ?>)</span></button>
        <?php foreach ($categories as $cat => $n): ?>
        <button class="filter-chip" data-filter="<?php echo e($cat); ?>">
          <?php echo e($catLabels[$cat] ?? ucfirst($cat)); ?> <span style="opacity:.6">(<?php echo $n; ?>)</span>
        </button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="gallery-grid">
        <?php foreach ($items as $item): $has = mediaExists($item['image']); ?>
        <figure class="gallery-item"
                data-category="<?php echo e(trim($item['category'] ?: 'general')); ?>"
                <?php if ($has): ?>data-lightbox="<?php echo e(mediaUrl($item['image'])); ?>"<?php else: ?>style="cursor:default"<?php endif; ?>
                data-caption="<?php echo e($item['title']); ?>"
                <?php if ($has): ?>tabindex="0" role="button" aria-label="Ingrandisci: <?php echo e($item['title']); ?>"<?php endif; ?>>
          <?php renderMedia($item['image'], $item['title'], 'fas fa-image'); ?>
          <figcaption><?php echo e($item['title']); ?></figcaption>
        </figure>
        <?php endforeach; ?>
      </div>

    <?php else: ?>
    <div class="empty-state">
      <i class="fas fa-images"></i>
      <h3>Galleria in allestimento</h3>
      <p>Le foto arrivano presto. Intanto puoi seguirci sui social o <a href="<?php echo APP_URL; ?>/contact">scriverci</a>.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/../templates/footer.php'; ?>
