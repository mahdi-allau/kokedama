<?php
$pageTitle = 'Chi siamo';
$activePage = 'about';
$metaDescription = 'La storia di Kokedama & Sculture Naturali: laboratorio artigianale di arte botanica a ' . getSetting('business_city', 'Ferrara') . '.';
require __DIR__ . '/../templates/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Percorso">
      <a href="<?php echo APP_URL; ?>/">Home</a><i class="fas fa-chevron-right"></i><span>Chi siamo</span>
    </nav>
    <span class="eyebrow eyebrow--center">Il laboratorio</span>
    <h1 class="display-2">La nostra <span class="accent">storia</span></h1>
    <p class="lead">Dalla passione per la natura all'arte delle sculture vegetali, nel cuore di <?php echo e(getSetting('business_city', 'Ferrara')); ?>.</p>
  </div>
</section>

<section class="section">
  <div class="container-narrow text-center">
    <p style="font-size:1.2rem;line-height:1.9;color:var(--body)" data-reveal>
      <?php echo nl2br(e(getSetting('about_intro'))); ?>
    </p>
    <div class="divider-leaf" data-reveal><i class="fas fa-leaf"></i></div>
  </div>
</section>

<section class="section bg-light">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <span class="eyebrow eyebrow--center">Il nostro approccio</span>
      <h2 class="display-3">La filosofia</h2>
      <p>Natura, arte e manualità in perfetta armonia.</p>
    </div>

    <div class="feature-row" data-reveal>
      <div class="feature-image">
        <div class="feature-image-inner"><span class="media-placeholder"><i class="fas fa-spa"></i></span></div>
      </div>
      <div class="feature-content">
        <h3>La kokedama: un'arte antica</h3>
        <p>La parola <em>kokedama</em> (苔玉) in giapponese significa letteralmente “palla di muschio”. È una tecnica secolare: la pianta cresce dentro una sfera di terriccio avvolta di muschio e legata con lo spago. Senza vaso, la pianta è libera di mostrarsi per quello che è.</p>
        <p>Per noi ogni kokedama è più di una pianta: è una scultura vivente, un pezzo di natura che entra nelle case e negli uffici e porta con sé calma e bellezza.</p>
      </div>
    </div>

    <div class="feature-row reverse" data-reveal>
      <div class="feature-image">
        <div class="feature-image-inner"><span class="media-placeholder"><i class="fas fa-heart"></i></span></div>
      </div>
      <div class="feature-content">
        <h3>Materiali naturali e sostenibili</h3>
        <p>Usiamo muschi raccolti in modo responsabile, terricci di qualità e piante scelte per bellezza e resistenza. Ogni materiale è selezionato per far durare a lungo la creazione e per rispettare l'ambiente.</p>
        <ul class="feature-list">
          <li><i class="fas fa-check"></i> Muschi naturali e sostenibili</li>
          <li><i class="fas fa-check"></i> Piante selezionate e certificate</li>
          <li><i class="fas fa-check"></i> Terricci biologici</li>
          <li><i class="fas fa-check"></i> Imballaggi plastic-free</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-header section-header--center" data-reveal>
      <span class="eyebrow eyebrow--center">Servizi</span>
      <h2 class="display-3">Cosa facciamo</h2>
      <p>Su misura per privati, aziende ed eventi.</p>
    </div>
    <div class="row">
      <?php
      $cosa = [
        ['fas fa-leaf',  'Creazioni su commissione', "Kokedama, composizioni vegetali, terrari e sculture floreali su richiesta. Scegli la pianta, le dimensioni e lo stile: creiamo il pezzo giusto per te."],
        ['fas fa-users', 'Workshop e corsi',         "Laboratori pratici individuali, per coppie o gruppi. Impari la tecnica, scopri le piante adatte e porti a casa la tua creazione. Perfetti per team building."],
        ['fas fa-store', 'Vendita e consulenza',     "Vieni in atelier a scegliere tra le creazioni disponibili. Ti aiutiamo anche a capire come curare le tue piante e quali specie stanno bene nel tuo spazio."],
        ['fas fa-gift',  'Regali e bomboniere',      "Un regalo originale per ogni occasione, con confezione elegante e biglietto personalizzato. Bomboniere verdi per matrimoni, battesimi e comunioni."],
      ];
      foreach ($cosa as $i => $c): ?>
      <article class="card" data-reveal style="--reveal-delay:<?php echo ($i % 3) * 0.1; ?>s">
        <div class="card-media"><span class="media-placeholder"><i class="<?php echo e($c[0]); ?>"></i></span></div>
        <div class="card-body">
          <h3 class="card-title"><?php echo e($c[1]); ?></h3>
          <p class="card-text"><?php echo e($c[2]); ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <div class="cta-band" data-reveal>
      <h2 class="display-3">Vieni a trovarci a <?php echo e(getSetting('business_city', 'Ferrara')); ?></h2>
      <p class="lead">Siamo in <strong><?php echo e(getSetting('business_address')); ?></strong>. Passa a vedere le creazioni dal vivo o prenota un workshop.</p>
      <div class="cta-buttons">
        <a href="<?php echo e(mapsDirectionsUrl()); ?>" target="_blank" rel="noopener" class="btn btn-white btn-lg"><i class="fas fa-map-marker-alt"></i> Come arrivare</a>
        <a href="<?php echo APP_URL; ?>/booking" class="btn btn-outline-white btn-lg"><i class="fas fa-calendar-check"></i> Prenota</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../templates/footer.php'; ?>
