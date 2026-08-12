<?php
$db = getDB();
$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare("SELECT p.*, s.title AS service_title, s.slug AS service_slug
                      FROM projects p
                      LEFT JOIN services s ON s.id = p.service_id
                      WHERE p.slug = ? AND p.published = 1");
$stmt->execute([$slug]);
$item = $stmt->fetch();
if (!$item) { header('Location: ' . APP_URL . '/projects'); exit; }

// Foto della galleria collegate a questo progetto
$g = $db->prepare("SELECT * FROM gallery WHERE published = 1 AND project_id = ? ORDER BY ordering");
$g->execute([$item['id']]);
$shots = $g->fetchAll();

$others = $db->prepare("SELECT * FROM projects WHERE published = 1 AND id != ? ORDER BY ordering LIMIT 3");
$others->execute([$item['id']]);
$others = $others->fetchAll();

$pageTitle = $item['title'];
$activePage = 'projects';
$metaDescription = truncate($item['description'], 155);
$ogImage = $item['image'];
require __DIR__ . '/../templates/header.php';
?>

<section class="section">
  <div class="container">
    <nav class="breadcrumb" style="justify-content:flex-start" aria-label="Percorso">
      <a href="<?php echo APP_URL; ?>/">Home</a><i class="fas fa-chevron-right"></i>
      <a href="<?php echo APP_URL; ?>/projects">Progetti</a><i class="fas fa-chevron-right"></i>
      <span><?php echo e($item['title']); ?></span>
    </nav>

    <div class="feature-row" style="margin-bottom:0">
      <div class="feature-image">
        <div class="feature-image-inner"><?php renderMedia($item['image'], $item['title'], 'fas fa-seedling', false); ?></div>
      </div>
      <div class="feature-content">
        <span class="eyebrow">Progetto realizzato</span>
        <h1 class="display-3" style="margin-bottom:1rem"><?php echo e($item['title']); ?></h1>
        <div class="lead" style="margin-bottom:2rem"><?php echo nl2br(e($item['description'])); ?></div>

        <ul class="feature-list">
          <?php if ($item['plant_type']): ?>
          <li><i class="fas fa-leaf"></i> <strong>Piante:</strong>&nbsp;<?php echo e($item['plant_type']); ?></li>
          <?php endif; ?>
          <?php if ($item['materials']): ?>
          <li><i class="fas fa-layer-group"></i> <strong>Materiali:</strong>&nbsp;<?php echo e($item['materials']); ?></li>
          <?php endif; ?>
          <?php if ($item['service_title']): ?>
          <li><i class="fas fa-spa"></i> <strong>Servizio:</strong>&nbsp;<a href="<?php echo APP_URL; ?>/service/<?php echo e($item['service_slug']); ?>" class="link-underline"><?php echo e($item['service_title']); ?></a></li>
          <?php endif; ?>
        </ul>

        <div class="hero-buttons">
          <a href="<?php echo APP_URL; ?>/booking" class="btn btn-kokedama btn-lg"><i class="fas fa-calendar-check"></i> Voglio qualcosa di simile</a>
          <a href="<?php echo APP_URL; ?>/projects" class="btn btn-ghost btn-lg">Torna al portfolio</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if ($shots): ?>
<section class="section bg-light">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <h2 class="display-4">Altre immagini</h2>
    </div>
    <div class="gallery-grid" data-reveal>
      <?php foreach ($shots as $shot): ?>
      <figure class="gallery-item"
              data-lightbox="<?php echo mediaExists($shot['image']) ? e(mediaUrl($shot['image'])) : ''; ?>"
              data-caption="<?php echo e($shot['title']); ?>">
        <?php renderMedia($shot['image'], $shot['title'], 'fas fa-image'); ?>
        <figcaption><?php echo e($shot['title']); ?></figcaption>
      </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($others): ?>
<section class="section">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <h2 class="display-4">Altri progetti</h2>
    </div>
    <div class="row">
      <?php foreach ($others as $o): ?>
      <article class="card" data-reveal>
        <div class="card-media"><?php renderMedia($o['image'], $o['title'], 'fas fa-seedling'); ?></div>
        <div class="card-body">
          <h3 class="card-title"><a href="<?php echo APP_URL; ?>/project/<?php echo e($o['slug']); ?>"><?php echo e($o['title']); ?></a></h3>
          <?php if ($o['plant_type']): ?><p class="meta-line"><i class="fas fa-leaf"></i> <?php echo e($o['plant_type']); ?></p><?php endif; ?>
        </div>
        <a href="<?php echo APP_URL; ?>/project/<?php echo e($o['slug']); ?>" class="card-link" aria-label="<?php echo e($o['title']); ?>"></a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/../templates/footer.php'; ?>
