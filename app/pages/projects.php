<?php
$db = getDB();
$items = $db->query("SELECT * FROM projects WHERE published = 1 ORDER BY ordering, id")->fetchAll();
$pageTitle = 'Progetti';
$activePage = 'projects';
$metaDescription = 'Portfolio di kokedama, composizioni vegetali e allestimenti realizzati a ' . getSetting('business_city', 'Ferrara') . '.';
require __DIR__ . '/../templates/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Percorso">
      <a href="<?php echo APP_URL; ?>/">Home</a><i class="fas fa-chevron-right"></i><span>Progetti</span>
    </nav>
    <span class="eyebrow eyebrow--center">Portfolio</span>
    <h1 class="display-2">Le nostre <span class="accent">creazioni</span></h1>
    <p class="lead">Ogni progetto è nato da una richiesta diversa. Guarda cosa abbiamo realizzato: la tua idea può essere la prossima.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($items): ?>
    <div class="row">
      <?php foreach ($items as $i => $item): ?>
      <article class="card" data-reveal style="--reveal-delay:<?php echo ($i % 3) * 0.1; ?>s">
        <div class="card-media">
          <?php renderMedia($item['image'], $item['title'], 'fas fa-seedling'); ?>
          <?php if ($item['featured']): ?><span class="card-media-tag">In evidenza</span><?php endif; ?>
        </div>
        <div class="card-body">
          <h2 class="card-title"><a href="<?php echo APP_URL; ?>/project/<?php echo e($item['slug']); ?>"><?php echo e($item['title']); ?></a></h2>
          <p class="card-text"><?php echo e(truncate($item['description'], 120)); ?></p>
          <?php if ($item['plant_type']): ?>
          <p class="meta-line"><i class="fas fa-leaf"></i> <?php echo e($item['plant_type']); ?></p>
          <?php endif; ?>
        </div>
        <a href="<?php echo APP_URL; ?>/project/<?php echo e($item['slug']); ?>" class="card-link" aria-label="Dettagli di <?php echo e($item['title']); ?>"></a>
      </article>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <i class="fas fa-seedling"></i>
      <h3>Portfolio in costruzione</h3>
      <p>Stiamo fotografando le ultime creazioni. Nel frattempo dai un'occhiata alla <a href="<?php echo APP_URL; ?>/gallery">galleria</a>.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/../templates/footer.php'; ?>
