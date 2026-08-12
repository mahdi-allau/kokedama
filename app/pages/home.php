<?php
$db = getDB();
$services = $db->query("SELECT * FROM services WHERE published = 1 AND featured = 1 ORDER BY ordering LIMIT 3")->fetchAll();
if (!$services) {
    $services = $db->query("SELECT * FROM services WHERE published = 1 ORDER BY ordering LIMIT 3")->fetchAll();
}
$events = $db->query("SELECT * FROM events WHERE published = 1 AND event_date >= date('now') ORDER BY event_date LIMIT 3")->fetchAll();
$shots  = $db->query("SELECT * FROM gallery WHERE published = 1 ORDER BY ordering LIMIT 6")->fetchAll();

$pageTitle = 'Home';
$activePage = 'home';
$heroImage = getSetting('home_hero_image');
$ogImage = $heroImage;
require __DIR__ . '/../templates/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="hero">
  <div class="container">
    <div class="hero-grid">
      <div class="hero-content fade-in-up">
        <span class="eyebrow">Atelier botanico · <?php echo e(getSetting('business_city', 'Ferrara')); ?></span>
        <h1 class="display-1"><?php echo e(getSetting('home_hero_title', 'Sculture naturali fatte a mano')); ?></h1>
        <p class="lead"><?php echo e(getSetting('home_hero_subtitle')); ?></p>
        <div class="hero-buttons">
          <a href="<?php echo APP_URL; ?>/booking" class="btn btn-kokedama btn-lg"><i class="fas fa-calendar-check"></i> Prenota un workshop</a>
          <a href="<?php echo APP_URL; ?>/services" class="btn btn-ghost btn-lg btn-arrow">Scopri i servizi <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="hero-trust">
          <span class="hero-trust-item"><i class="fas fa-hand-holding-heart"></i> <span><strong>100%</strong> fatto a mano</span></span>
          <span class="hero-trust-item"><i class="fas fa-users"></i> <span><strong>Piccoli gruppi</strong>, max 8 persone</span></span>
          <span class="hero-trust-item"><i class="fas fa-seedling"></i> <span><strong>Materiali</strong> naturali</span></span>
        </div>
      </div>

      <div class="hero-visual fade-in delay-2">
        <div class="hero-frame">
          <?php renderMedia($heroImage, 'Kokedama artigianale realizzata a mano', 'fas fa-spa', false); ?>
        </div>
        <div class="hero-badge">
          <i class="fas fa-star" aria-hidden="true"></i>
          <div>
            <strong>Laboratorio artigiano</strong>
            <small><?php echo e(fullAddress()); ?></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== SERVIZI ===================== -->
<section class="section">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <span class="eyebrow eyebrow--center">Cosa offriamo</span>
      <h2 class="display-3">I nostri servizi</h2>
      <p>Creazioni su misura e laboratori pratici, per chi vuole portarsi a casa un pezzo di natura.</p>
    </div>

    <?php if ($services): ?>
    <div class="row">
      <?php foreach ($services as $i => $s): ?>
      <article class="card" data-reveal style="--reveal-delay:<?php echo $i * 0.1; ?>s">
        <div class="card-media">
          <?php renderMedia($s['image'], $s['title'], 'fas fa-spa'); ?>
          <?php if ($s['duration']): ?>
          <span class="card-media-tag"><?php echo (int)$s['duration']; ?> min</span>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <h3 class="card-title"><a href="<?php echo APP_URL; ?>/service/<?php echo e($s['slug']); ?>"><?php echo e($s['title']); ?></a></h3>
          <p class="card-text"><?php echo e(truncate($s['short_desc'] ?: $s['description'], 110)); ?></p>
          <div class="card-meta">
            <span class="price"><?php echo formatPrice($s['price']); ?></span>
            <span class="btn btn-outline-kokedama btn-sm btn-arrow">Dettagli <i class="fas fa-arrow-right"></i></span>
          </div>
        </div>
        <a href="<?php echo APP_URL; ?>/service/<?php echo e($s['slug']); ?>" class="card-link" aria-label="Scopri <?php echo e($s['title']); ?>"></a>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5" data-reveal>
      <a href="<?php echo APP_URL; ?>/services" class="btn btn-kokedama btn-arrow">Vedi tutti i servizi <i class="fas fa-arrow-right"></i></a>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <i class="fas fa-spa"></i>
      <h3>Servizi in arrivo</h3>
      <p>Stiamo preparando il catalogo. Nel frattempo, <a href="<?php echo APP_URL; ?>/contact">scrivici</a>: raccontaci cosa cerchi.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ===================== COME FUNZIONA ===================== -->
<section class="section bg-light">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <span class="eyebrow eyebrow--center">Semplice</span>
      <h2 class="display-3">Come funziona</h2>
      <p>Dalla richiesta alla tua creazione, in tre passaggi.</p>
    </div>
    <div class="steps">
      <div class="step" data-reveal>
        <h3>Scegli</h3>
        <p>Sfoglia i servizi e i workshop in calendario. Non sai cosa fa per te? Scrivici, ti consigliamo noi.</p>
      </div>
      <div class="step" data-reveal style="--reveal-delay:.12s">
        <h3>Prenota</h3>
        <p>Compila il modulo in un minuto. Ti rispondiamo entro 24 ore con la conferma e tutti i dettagli.</p>
      </div>
      <div class="step" data-reveal style="--reveal-delay:.24s">
        <h3>Crea</h3>
        <p>Ti aspettiamo in atelier a <?php echo e(getSetting('business_city', 'Ferrara')); ?>. Materiali inclusi: tu porti solo la voglia di sporcarti le mani.</p>
      </div>
    </div>
    <div class="text-center mt-5" data-reveal>
      <a href="<?php echo APP_URL; ?>/booking" class="btn btn-kokedama btn-lg"><i class="fas fa-calendar-check"></i> Inizia da qui</a>
    </div>
  </div>
</section>

<!-- ===================== PERCHÉ NOI ===================== -->
<section class="section">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <span class="eyebrow eyebrow--center">Il nostro modo di lavorare</span>
      <h2 class="display-3">Perché sceglierci</h2>
      <p>L'arte botanica giapponese incontra la cura artigianale italiana.</p>
    </div>

    <div class="feature-row" data-reveal>
      <div class="feature-image">
        <div class="feature-image-inner"><span class="media-placeholder"><i class="fas fa-hand-holding-heart"></i></span></div>
      </div>
      <div class="feature-content">
        <h3>Ogni pezzo è unico</h3>
        <p>Ogni kokedama nasce interamente a mano, con tecniche tradizionali giapponesi. Nessuna creazione è uguale all'altra: la forma segue la pianta, non uno stampo.</p>
        <ul class="feature-list">
          <li><i class="fas fa-check"></i> Tecniche giapponesi autentiche</li>
          <li><i class="fas fa-check"></i> Muschi e terricci selezionati</li>
          <li><i class="fas fa-check"></i> Personalizzabile su richiesta</li>
        </ul>
        <a href="<?php echo APP_URL; ?>/projects" class="btn btn-outline-kokedama btn-arrow">Guarda i progetti <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>

    <div class="feature-row reverse" data-reveal>
      <div class="feature-image">
        <div class="feature-image-inner"><span class="media-placeholder"><i class="fas fa-seedling"></i></span></div>
      </div>
      <div class="feature-content">
        <h3>Workshop per veri principianti</h3>
        <p>Non serve alcuna esperienza. Lavoriamo in piccoli gruppi, con calma, e ognuno esce con la propria kokedama e le istruzioni per tenerla viva a lungo.</p>
        <ul class="feature-list">
          <li><i class="fas fa-check"></i> Tutti i materiali inclusi</li>
          <li><i class="fas fa-check"></i> Massimo 8 partecipanti</li>
          <li><i class="fas fa-check"></i> Ideale per coppie, gruppi e team building</li>
        </ul>
        <a href="<?php echo APP_URL; ?>/events" class="btn btn-outline-kokedama btn-arrow">Vedi il calendario <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>

    <div class="feature-row" data-reveal>
      <div class="feature-image">
        <div class="feature-image-inner"><span class="media-placeholder"><i class="fas fa-gift"></i></span></div>
      </div>
      <div class="feature-content">
        <h3>Un regalo che nessuno ha già</h3>
        <p>Compleanni, anniversari, lauree, nascite: una scultura vegetale è il regalo che resta e cresce. Confezione curata e biglietto personalizzato inclusi.</p>
        <ul class="feature-list">
          <li><i class="fas fa-check"></i> Confezione elegante inclusa</li>
          <li><i class="fas fa-check"></i> Biglietto personalizzato su richiesta</li>
          <li><i class="fas fa-check"></i> Consegna in tutta <?php echo e(getSetting('business_city', 'Ferrara')); ?></li>
        </ul>
        <a href="<?php echo APP_URL; ?>/contact" class="btn btn-outline-kokedama btn-arrow">Richiedi un'idea regalo <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- ===================== NUMERI ===================== -->
<section class="section bg-green">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <span class="eyebrow eyebrow--center">Dal 2018</span>
      <h2 class="display-3">Kokedama in numeri</h2>
    </div>
    <div class="stats-grid">
      <div class="stat-item" data-reveal><div class="stat-number">500+</div><div class="stat-label">Kokedama create</div></div>
      <div class="stat-item" data-reveal style="--reveal-delay:.1s"><div class="stat-number">120+</div><div class="stat-label">Workshop tenuti</div></div>
      <div class="stat-item" data-reveal style="--reveal-delay:.2s"><div class="stat-number">8</div><div class="stat-label">Anni di esperienza</div></div>
      <div class="stat-item" data-reveal style="--reveal-delay:.3s"><div class="stat-number">4,9</div><div class="stat-label">Valutazione media</div></div>
    </div>
  </div>
</section>

<!-- ===================== EVENTI ===================== -->
<?php if ($events): ?>
<section class="section">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <span class="eyebrow eyebrow--center">Calendario</span>
      <h2 class="display-3">Prossimi appuntamenti</h2>
      <p>I posti sono limitati: si prenota in ordine di arrivo.</p>
    </div>
    <div class="row">
      <?php foreach ($events as $i => $ev): $soon = relativeDay($ev['event_date']); ?>
      <article class="card" data-reveal style="--reveal-delay:<?php echo $i * 0.1; ?>s">
        <div class="card-media">
          <?php renderMedia($ev['image'], $ev['title'], 'fas fa-calendar-day'); ?>
          <?php if ($soon): ?><span class="card-media-tag card-media-tag--clay"><?php echo e($soon); ?></span><?php endif; ?>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center gap-2">
            <span class="badge badge-light"><?php echo e(ucfirst($ev['event_type'])); ?></span>
            <span class="price"><?php echo formatPrice($ev['price']); ?></span>
          </div>
          <h3 class="card-title"><a href="<?php echo APP_URL; ?>/event/<?php echo e($ev['slug']); ?>"><?php echo e($ev['title']); ?></a></h3>
          <p class="meta-line"><i class="far fa-calendar"></i> <?php echo e(formatDate($ev['event_date'], true)); ?></p>
          <p class="meta-line"><i class="far fa-clock"></i> <?php echo e(formatTime($ev['event_time'])); ?><?php echo $ev['end_time'] ? ' – ' . e(formatTime($ev['end_time'])) : ''; ?></p>
          <div class="card-meta">
            <span class="meta-line"><i class="fas fa-users"></i> Max <?php echo (int)$ev['max_participants']; ?> posti</span>
            <span class="btn btn-outline-kokedama btn-sm">Prenota</span>
          </div>
        </div>
        <a href="<?php echo APP_URL; ?>/event/<?php echo e($ev['slug']); ?>" class="card-link" aria-label="Dettagli di <?php echo e($ev['title']); ?>"></a>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5" data-reveal>
      <a href="<?php echo APP_URL; ?>/events" class="btn btn-kokedama btn-arrow">Tutti gli eventi <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===================== GALLERIA ===================== -->
<?php if ($shots): ?>
<section class="section bg-light">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <span class="eyebrow eyebrow--center">Dall'atelier</span>
      <h2 class="display-3">Uno sguardo al lavoro</h2>
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
    <div class="text-center mt-5" data-reveal>
      <a href="<?php echo APP_URL; ?>/gallery" class="btn btn-outline-kokedama btn-arrow">Apri la galleria <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===================== TESTIMONIANZE ===================== -->
<section class="section">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <span class="eyebrow eyebrow--center">Recensioni</span>
      <h2 class="display-3">Cosa dicono di noi</h2>
    </div>
    <div class="grid-3">
      <?php
      $reviews = [
          ['Ho partecipato al workshop per il compleanno di mia sorella. Un\'esperienza magica: Simona è paziente e appassionata. Siamo tornate a casa con due meraviglie verdi.', 'Martina G.', 'Ferrara', 'MG'],
          ['Ho regalato una kokedama a mia madre per la Festa della Mamma. Confezione curatissima e pianta stupenda: dopo due mesi è ancora rigogliosa. Consigliatissimo.', 'Luca B.', 'Bologna', 'LB'],
          ['Team building aziendale organizzato con Simona: tutti i colleghi entusiasti. Un\'attività originale che unisce creatività, natura e relax. Da rifare!', 'Alice R.', 'HR — Ferrara', 'AR'],
      ];
      foreach ($reviews as $i => $r): ?>
      <div class="testimonial" data-reveal style="--reveal-delay:<?php echo $i * 0.1; ?>s">
        <div class="testimonial-stars" aria-label="5 stelle su 5">★★★★★</div>
        <p class="testimonial-text"><?php echo e($r[0]); ?></p>
        <div class="testimonial-author">
          <div class="testimonial-avatar"><?php echo e($r[3]); ?></div>
          <div><strong><?php echo e($r[1]); ?></strong><small><?php echo e($r[2]); ?></small></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== CTA FINALE ===================== -->
<section class="section section--tight">
  <div class="container">
    <div class="cta-band" data-reveal>
      <h2 class="display-3">Pronto a creare la tua kokedama?</h2>
      <p class="lead">Prenota un workshop o ordina la tua scultura vegetale su misura. Ti aspettiamo in <?php echo e(getSetting('business_address', 'Piazza della Repubblica 11')); ?>.</p>
      <div class="cta-buttons">
        <a href="<?php echo APP_URL; ?>/booking" class="btn btn-white btn-lg"><i class="fas fa-calendar-check"></i> Prenota ora</a>
        <?php if ($wa = whatsappLink()): ?>
        <a href="<?php echo e($wa); ?>" target="_blank" rel="noopener" class="btn btn-outline-white btn-lg"><i class="fab fa-whatsapp"></i> Scrivici su WhatsApp</a>
        <?php else: ?>
        <a href="<?php echo APP_URL; ?>/contact" class="btn btn-outline-white btn-lg"><i class="fas fa-envelope"></i> Contattaci</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../templates/footer.php'; ?>
