<?php
$db = getDB();
$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare("SELECT * FROM events WHERE slug = ? AND published = 1");
$stmt->execute([$slug]);
$item = $stmt->fetch();
if (!$item) { header('Location: ' . APP_URL . '/events'); exit; }

// Posti già impegnati da prenotazioni confermate o in attesa
$booked = $db->prepare("SELECT COALESCE(SUM(participants), 0) FROM bookings
                        WHERE event_id = ? AND status IN ('pending','confirmed')");
$booked->execute([$item['id']]);
$booked = (int)$booked->fetchColumn();
$max = (int)$item['max_participants'];
$left = $max > 0 ? max(0, $max - $booked) : null;
$isPast = strtotime($item['event_date']) < strtotime(date('Y-m-d'));

$others = $db->prepare("SELECT * FROM events WHERE published = 1 AND id != ? AND event_date >= date('now') ORDER BY event_date LIMIT 3");
$others->execute([$item['id']]);
$others = $others->fetchAll();

$pageTitle = $item['title'];
$activePage = 'events';
$metaDescription = truncate($item['description'], 155);
$ogImage = $item['image'];
require __DIR__ . '/../templates/header.php';
?>

<section class="section">
  <div class="container">
    <nav class="breadcrumb" style="justify-content:flex-start" aria-label="Percorso">
      <a href="<?php echo APP_URL; ?>/">Home</a><i class="fas fa-chevron-right"></i>
      <a href="<?php echo APP_URL; ?>/events">Eventi</a><i class="fas fa-chevron-right"></i>
      <span><?php echo e($item['title']); ?></span>
    </nav>

    <div class="contact-grid" style="grid-template-columns:1.35fr .65fr">
      <div>
        <div class="feature-image" style="aspect-ratio:16/9;margin-bottom:2rem">
          <div class="feature-image-inner"><?php renderMedia($item['image'], $item['title'], 'fas fa-calendar-day', false); ?></div>
        </div>

        <span class="eyebrow"><?php echo e(ucfirst($item['event_type'])); ?></span>
        <h1 class="display-3" style="margin-bottom:1.25rem"><?php echo e($item['title']); ?></h1>
        <div class="lead"><?php echo nl2br(e($item['description'])); ?></div>

        <h3 style="margin:2.5rem 0 1rem">Cosa è incluso</h3>
        <ul class="feature-list">
          <li><i class="fas fa-check"></i> Tutti i materiali: pianta, muschio, terriccio e spago</li>
          <li><i class="fas fa-check"></i> La tua creazione da portare a casa</li>
          <li><i class="fas fa-check"></i> Scheda scritta con le istruzioni per la cura</li>
          <li><i class="fas fa-check"></i> Grembiule e postazione di lavoro</li>
        </ul>

        <div class="alert alert-info mt-4">
          <i class="fas fa-circle-info"></i>
          <span>Non serve esperienza: il laboratorio è pensato per principianti. Vieni con abiti comodi che non temi di sporcare.</span>
        </div>
      </div>

      <aside>
        <div class="form-aside">
          <h4>Dettagli</h4>
          <ul>
            <li><i class="far fa-calendar"></i><span><strong><?php echo e(formatDate($item['event_date'], true)); ?></strong></span></li>
            <li><i class="far fa-clock"></i><span><?php echo e(formatTime($item['event_time'])); ?><?php echo $item['end_time'] ? ' – ' . e(formatTime($item['end_time'])) : ''; ?></span></li>
            <?php if ($item['location']): ?>
            <li><i class="fas fa-map-marker-alt"></i><span><?php echo e($item['location']); ?></span></li>
            <?php endif; ?>
            <?php if ($max > 0): ?>
            <li><i class="fas fa-users"></i><span>Massimo <?php echo $max; ?> partecipanti</span></li>
            <?php endif; ?>
          </ul>

          <?php if ($item['price']): ?>
          <div style="margin:1.5rem 0 1rem;padding-top:1.25rem;border-top:1px solid var(--green-100)">
            <span class="price" style="font-size:1.85rem"><?php echo formatPrice($item['price']); ?></span>
            <small style="display:block;color:var(--muted)">a persona, materiali inclusi</small>
          </div>
          <?php endif; ?>

          <?php if ($isPast): ?>
            <div class="alert alert-info" style="margin-bottom:1rem"><i class="fas fa-clock-rotate-left"></i><span>Questo appuntamento è concluso.</span></div>
            <a href="<?php echo APP_URL; ?>/events" class="btn btn-outline-kokedama btn-block">Vedi le prossime date</a>
          <?php elseif ($left !== null && $left <= 0): ?>
            <div class="alert alert-error" style="margin-bottom:1rem"><i class="fas fa-circle-exclamation"></i><span><strong>Posti esauriti.</strong> Scrivici per entrare in lista d'attesa.</span></div>
            <a href="<?php echo APP_URL; ?>/contact" class="btn btn-outline-kokedama btn-block">Lista d'attesa</a>
          <?php else: ?>
            <?php if ($left !== null && $left <= 3): ?>
            <p class="mb-3" style="color:var(--clay-700);font-weight:500;font-size:.9rem">
              <i class="fas fa-fire"></i> Ultimi <?php echo $left; ?> posti disponibili
            </p>
            <?php endif; ?>
            <a href="<?php echo APP_URL; ?>/booking?event=<?php echo (int)$item['id']; ?>" class="btn btn-kokedama btn-lg btn-block">
              <i class="fas fa-calendar-check"></i> Prenota il tuo posto
            </a>
            <p class="form-hint text-center mt-2">Nessun pagamento ora: confermiamo entro 24 ore.</p>
          <?php endif; ?>

          <?php if ($wa = whatsappLink('Ciao! Vorrei informazioni sul workshop: ' . $item['title'])): ?>
          <a href="<?php echo e($wa); ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-block mt-2"><i class="fab fa-whatsapp"></i> Chiedi informazioni</a>
          <?php endif; ?>
        </div>
      </aside>
    </div>
  </div>
</section>

<?php if ($others): ?>
<section class="section bg-light">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <h2 class="display-4">Altri appuntamenti</h2>
    </div>
    <div class="row">
      <?php foreach ($others as $o): ?>
      <article class="card" data-reveal>
        <div class="card-media"><?php renderMedia($o['image'], $o['title'], 'fas fa-calendar-day'); ?></div>
        <div class="card-body">
          <h3 class="card-title"><a href="<?php echo APP_URL; ?>/event/<?php echo e($o['slug']); ?>"><?php echo e($o['title']); ?></a></h3>
          <p class="meta-line"><i class="far fa-calendar"></i> <?php echo e(formatDate($o['event_date'], true)); ?></p>
          <div class="card-meta"><span class="price"><?php echo formatPrice($o['price']); ?></span></div>
        </div>
        <a href="<?php echo APP_URL; ?>/event/<?php echo e($o['slug']); ?>" class="card-link" aria-label="<?php echo e($o['title']); ?>"></a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/../templates/footer.php'; ?>
