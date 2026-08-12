<?php
$errors = [];
$old = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    foreach (array_keys($old) as $campo) { $old[$campo] = trim($_POST[$campo] ?? ''); }

    if ($old['name'] === '')    $errors[] = 'Il nome è obbligatorio.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Inserisci un indirizzo email valido.';
    if ($old['message'] === '') $errors[] = 'Scrivi il tuo messaggio.';
    if (empty($_POST['consent_gdpr'])) $errors[] = 'Devi accettare l\'informativa privacy.';

    if (honeypotTriggered()) {
        flash('Messaggio inviato! Ti risponderemo al più presto.');
        header('Location: ' . APP_URL . '/contact');
        exit;
    }

    if (!$errors) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO messages (name, email, phone, subject, message, consent_gdpr, ip_address)
                              VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([
            $old['name'], $old['email'], $old['phone'],
            $old['subject'] ?: 'Richiesta dal sito', $old['message'],
            1, $_SERVER['REMOTE_ADDR'] ?? ''
        ]);

        sendEmail(
            getSetting('business_email'),
            'Nuovo messaggio da ' . $old['name'],
            "Hai ricevuto un messaggio dal sito.\n\n"
            . "Da: {$old['name']}\nEmail: {$old['email']}\nTelefono: " . ($old['phone'] ?: '—') . "\n"
            . "Oggetto: " . ($old['subject'] ?: '—') . "\n\n{$old['message']}\n\n"
            . "Rispondi qui: " . APP_URL . "/admin/messages.php\n"
        );
        sendEmail(
            $old['email'],
            'Abbiamo ricevuto il tuo messaggio',
            "Gentile {$old['name']},\n\ngrazie per averci scritto! Ti rispondiamo al più presto."
            . emailSignature()
        );

        flash('Messaggio inviato! Ti risponderemo al più presto.');
        header('Location: ' . APP_URL . '/contact');
        exit;
    }
}

$pageTitle = 'Contatti';
$activePage = 'contact';
$metaDescription = 'Contatta Kokedama & Sculture Naturali a ' . getSetting('business_city', 'Ferrara') . ': indirizzo, telefono, orari e modulo di contatto.';
$orari = openingHours();
$oggi = todayHours();
require __DIR__ . '/../templates/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Percorso">
      <a href="<?php echo APP_URL; ?>/">Home</a><i class="fas fa-chevron-right"></i><span>Contatti</span>
    </nav>
    <span class="eyebrow eyebrow--center">Parliamone</span>
    <h1 class="display-2">Contatti</h1>
    <p class="lead">Siamo qui per te. Scrivici, chiamaci o vieni a trovarci in atelier.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="contact-grid">

      <div>
        <div class="contact-info-card">
          <h3><?php echo e(getSetting('business_name')); ?></h3>

          <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <div>
              <strong>Dove siamo</strong>
              <?php echo nl2br(e(getSetting('business_address'))); ?><br>
              <?php echo e(getSetting('business_zip')); ?> <?php echo e(getSetting('business_city')); ?><br>
              <a href="<?php echo e(mapsDirectionsUrl()); ?>" target="_blank" rel="noopener" class="link-underline">Apri in Google Maps</a>
            </div>
          </div>

          <div class="contact-item">
            <i class="fas fa-phone"></i>
            <div>
              <strong>Telefono</strong>
              <a href="tel:<?php echo e(telHref()); ?>"><?php echo e(getSetting('business_phone')); ?></a>
            </div>
          </div>

          <?php if ($wa = whatsappLink()): ?>
          <div class="contact-item">
            <i class="fab fa-whatsapp"></i>
            <div>
              <strong>WhatsApp</strong>
              <a href="<?php echo e($wa); ?>" target="_blank" rel="noopener">Scrivici un messaggio</a>
            </div>
          </div>
          <?php endif; ?>

          <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <div>
              <strong>Email</strong>
              <a href="mailto:<?php echo e(getSetting('business_email')); ?>"><?php echo e(getSetting('business_email')); ?></a>
            </div>
          </div>

          <?php if (getSetting('business_facebook') || getSetting('business_instagram')): ?>
          <div class="social-links" style="padding-left:.85rem">
            <?php if (getSetting('business_facebook')): ?><a href="<?php echo e(getSetting('business_facebook')); ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
            <?php if (getSetting('business_instagram')): ?><a href="<?php echo e(getSetting('business_instagram')); ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a><?php endif; ?>
          </div>
          <?php endif; ?>
        </div>

        <?php if ($orari): ?>
        <div class="contact-info-card mt-4">
          <h3 style="font-size:1.2rem;margin-bottom:1rem"><i class="far fa-clock" style="color:var(--green-600)"></i> Orari di apertura</h3>
          <ul class="hours-list">
            <?php foreach ($orari as $row): ?>
            <li<?php echo ($oggi && $oggi['label'] === $row['label']) ? ' class="is-today"' : ''; ?>>
              <span><?php echo e($row['label']); ?><?php echo ($oggi && $oggi['label'] === $row['label']) ? ' · oggi' : ''; ?></span>
              <span><?php echo e($row['value']); ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>

      <div class="form-card">
        <h3 style="margin-bottom:.5rem">Scrivici</h3>
        <p class="text-muted mb-4" style="font-size:.95rem">Rispondiamo di solito entro un giorno lavorativo.</p>

        <?php if ($errors): ?>
        <div class="alert alert-error mb-4" role="alert">
          <i class="fas fa-circle-exclamation"></i>
          <span>
            <strong>Controlla questi punti:</strong>
            <ul style="margin:.5rem 0 0 1rem">
              <?php foreach ($errors as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?>
            </ul>
          </span>
        </div>
        <?php endif; ?>

        <form method="post" action="" novalidate>
          <?php echo csrfField(); ?>
          <?php echo honeypotField(); ?>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="name">Nome <span class="req">*</span></label>
              <input type="text" name="name" id="name" class="form-control" value="<?php echo e($old['name']); ?>" required autocomplete="name">
            </div>
            <div class="form-group">
              <label class="form-label" for="email">Email <span class="req">*</span></label>
              <input type="email" name="email" id="email" class="form-control" value="<?php echo e($old['email']); ?>" required autocomplete="email">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="phone">Telefono</label>
              <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo e($old['phone']); ?>" autocomplete="tel">
            </div>
            <div class="form-group">
              <label class="form-label" for="subject">Oggetto</label>
              <input type="text" name="subject" id="subject" class="form-control" value="<?php echo e($old['subject']); ?>" placeholder="Es. Regalo, workshop, preventivo…">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="message">Messaggio <span class="req">*</span></label>
            <textarea name="message" id="message" class="form-control" rows="6" required><?php echo e($old['message']); ?></textarea>
          </div>

          <div class="form-group">
            <label class="form-check">
              <input type="checkbox" name="consent_gdpr" value="1" required>
              <span><?php echo e(getSetting('gdpr_consent_text')); ?>
                <a href="<?php echo APP_URL; ?>/privacy" target="_blank" class="link-underline">Informativa</a> <span class="req">*</span></span>
            </label>
          </div>

          <button type="submit" class="btn btn-kokedama btn-lg btn-block"><i class="fas fa-paper-plane"></i> Invia messaggio</button>
        </form>
      </div>

    </div>
  </div>
</section>

<?php $mapSrc = mapsEmbedSrc(); if ($mapSrc): ?>
<section class="section section--tight">
  <div class="container">
    <div class="map-embed" data-reveal>
      <iframe src="<?php echo e($mapSrc); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
              title="Mappa: dove siamo" allowfullscreen></iframe>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/../templates/footer.php'; ?>
