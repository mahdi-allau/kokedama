<?php
$adminTitle = 'Impostazioni';
$adminPage  = 'settings';

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
initDatabase();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'password') {
    requireCsrf();
    $problema = adminChangePassword(
        $_POST['current_password'] ?? '',
        $_POST['new_password'] ?? '',
        $_POST['confirm_password'] ?? ''
    );
    flash($problema ?: 'Password aggiornata. Usala al prossimo accesso.', $problema ? 'error' : 'success');
    header('Location: settings.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();

    $testi = [
        'business_name', 'business_tagline', 'business_address', 'business_city', 'business_zip',
        'business_phone', 'business_whatsapp', 'business_email',
        'business_facebook', 'business_instagram', 'business_maps_embed', 'business_hours',
        'home_hero_title', 'home_hero_subtitle', 'about_intro',
        'booking_notice_days', 'booking_intro',
        'seo_description', 'gdpr_consent_text', 'privacy_owner',
    ];
    foreach ($testi as $key) {
        setSetting($key, trim($_POST[$key] ?? ''));
    }

    // Foto principale della home
    $current = trim($_POST['home_hero_image_current'] ?? '');
    if (!empty($_POST['home_hero_image_remove'])) {
        deleteUpload($current);
        setSetting('home_hero_image', '');
    } elseif (!empty($_FILES['home_hero_image']['name'])) {
        $uploadError = null;
        $path = uploadImage($_FILES['home_hero_image'], 'site', $uploadError);
        if ($path === null) {
            $errors[] = $uploadError ?: 'Immagine non caricata.';
        } else {
            if ($current) deleteUpload($current);
            setSetting('home_hero_image', $path);
        }
    }

    if ($errors) {
        flash('Impostazioni salvate, ma: ' . implode(' ', $errors), 'warning');
    } else {
        flash('Impostazioni salvate. Le modifiche sono già online.');
    }
    header('Location: settings.php');
    exit;
}

$heroImage = getSetting('home_hero_image');
$adminAction = '<a href="' . APP_URL . '/" target="_blank" class="btn btn-outline"><i class="fas fa-arrow-up-right-from-square"></i> Vedi il sito</a>';

require __DIR__ . '/_layout.php';
?>

<div class="alert alert-info">
  <i class="fas fa-lightbulb"></i>
  <span>Tutto quello che scrivi qui compare <strong>subito nel sito</strong>: indirizzo, telefono, orari, testi della home.
        Non serve toccare il codice né chiamare nessuno.</span>
</div>

<form method="post" enctype="multipart/form-data">
  <?php echo csrfField(); ?>

  <!-- ============================ CONTATTI ============================ -->
  <div class="panel">
    <div class="panel-head"><h2><i class="fas fa-address-card"></i> Dati dell'attività</h2></div>
    <div class="panel-body">
      <div class="form-grid">
        <div class="form-group full">
          <label class="form-label" for="business_name">Nome dell'attività</label>
          <input type="text" name="business_name" id="business_name" class="form-control" value="<?php echo e(getSetting('business_name')); ?>">
        </div>

        <div class="form-group full">
          <label class="form-label" for="business_tagline">Sottotitolo</label>
          <input type="text" name="business_tagline" id="business_tagline" class="form-control" value="<?php echo e(getSetting('business_tagline')); ?>">
          <p class="form-hint">Una riga che descrive cosa fai. Compare nel footer.</p>
        </div>

        <div class="form-group full">
          <label class="form-label" for="business_address">Indirizzo</label>
          <input type="text" name="business_address" id="business_address" class="form-control" value="<?php echo e(getSetting('business_address')); ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="business_city">Città</label>
          <input type="text" name="business_city" id="business_city" class="form-control" value="<?php echo e(getSetting('business_city')); ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="business_zip">CAP</label>
          <input type="text" name="business_zip" id="business_zip" class="form-control" value="<?php echo e(getSetting('business_zip')); ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="business_phone">Telefono</label>
          <input type="text" name="business_phone" id="business_phone" class="form-control" value="<?php echo e(getSetting('business_phone')); ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="business_email">Email</label>
          <input type="email" name="business_email" id="business_email" class="form-control" value="<?php echo e(getSetting('business_email')); ?>">
          <p class="form-hint">Qui arrivano le notifiche di prenotazioni e messaggi.</p>
        </div>

        <div class="form-group full">
          <label class="form-label" for="business_whatsapp">Numero WhatsApp</label>
          <input type="text" name="business_whatsapp" id="business_whatsapp" class="form-control"
                 value="<?php echo e(getSetting('business_whatsapp')); ?>" placeholder="333 1234567">
          <p class="form-hint">
            Attiva il pulsante verde WhatsApp in tutto il sito e nella barra in basso su telefono.
            Scrivi il numero come vuoi: ci pensiamo noi a formattarlo. <strong>Lascia vuoto per disattivarlo.</strong>
          </p>
        </div>

        <div class="form-group">
          <label class="form-label" for="business_facebook">Pagina Facebook</label>
          <input type="url" name="business_facebook" id="business_facebook" class="form-control" value="<?php echo e(getSetting('business_facebook')); ?>" placeholder="https://facebook.com/…">
        </div>

        <div class="form-group">
          <label class="form-label" for="business_instagram">Profilo Instagram</label>
          <input type="url" name="business_instagram" id="business_instagram" class="form-control" value="<?php echo e(getSetting('business_instagram')); ?>" placeholder="https://instagram.com/…">
        </div>
      </div>
    </div>
  </div>

  <!-- ============================ ORARI =============================== -->
  <div class="panel">
    <div class="panel-head"><h2><i class="far fa-clock"></i> Orari di apertura</h2></div>
    <div class="panel-body">
      <div class="form-group">
        <label class="form-label" for="business_hours">Orari</label>
        <textarea name="business_hours" id="business_hours" class="form-control" rows="7"><?php echo e(getSetting('business_hours')); ?></textarea>
        <p class="form-hint">
          Una riga per giorno, nel formato <code>Giorno|Orario</code>. Esempio:<br>
          <code>Lunedì|Chiuso</code><br>
          <code>Martedì - Venerdì|9:30 - 13:00 / 15:30 - 19:30</code><br>
          Il sito evidenzia in automatico la riga di oggi e mostra “Oggi: …” nella barra in alto.
          Lascia il campo vuoto per non mostrare gli orari.
        </p>
      </div>

      <div class="form-group mb-0">
        <label class="form-label" for="business_maps_embed">Mappa Google</label>
        <textarea name="business_maps_embed" id="business_maps_embed" class="form-control" rows="3"
                  placeholder="Incolla qui il codice copiato da Google Maps"><?php echo e(getSetting('business_maps_embed')); ?></textarea>
        <p class="form-hint">
          Su Google Maps: cerca l'indirizzo → <em>Condividi</em> → <em>Incorpora una mappa</em> → <em>Copia HTML</em>.
          Incolla tutto qui: la mappa comparirà in fondo alla pagina Contatti.
          <?php echo mapsEmbedSrc() ? '<strong style="color:var(--a-green)">✓ Mappa attiva.</strong>' : ''; ?>
        </p>
      </div>
    </div>
  </div>

  <!-- ============================ HOME ================================ -->
  <div class="panel">
    <div class="panel-head"><h2><i class="fas fa-house"></i> Testi della home page</h2></div>
    <div class="panel-body">
      <div class="form-group">
        <label class="form-label" for="home_hero_title">Titolo principale</label>
        <input type="text" name="home_hero_title" id="home_hero_title" class="form-control" value="<?php echo e(getSetting('home_hero_title')); ?>">
        <p class="form-hint">La prima frase che si legge entrando nel sito. Tienila breve e concreta.</p>
      </div>

      <div class="form-group">
        <label class="form-label" for="home_hero_subtitle">Sottotitolo</label>
        <textarea name="home_hero_subtitle" id="home_hero_subtitle" class="form-control" rows="3"><?php echo e(getSetting('home_hero_subtitle')); ?></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Foto principale della home</label>
        <div class="image-field">
          <div class="image-preview" id="preview_hero">
            <?php if (mediaExists($heroImage)): ?>
              <img src="<?php echo e(mediaUrl($heroImage)); ?>" alt="Foto attuale della home">
            <?php else: ?>
              <i class="fas fa-spa"></i>
            <?php endif; ?>
          </div>
          <div class="image-field-controls">
            <input type="hidden" name="home_hero_image_current" value="<?php echo e($heroImage); ?>">
            <input type="file" name="home_hero_image" class="form-control"
                   accept="image/jpeg,image/png,image/gif,image/webp" data-image-input="#preview_hero">
            <p class="form-hint">
              È la foto più importante del sito. Meglio verticale (o quadrata), luminosa, con una tua creazione in primo piano.
              Massimo 6 MB.
            </p>
            <?php if (mediaExists($heroImage)): ?>
            <label class="form-check mt-2">
              <input type="checkbox" name="home_hero_image_remove" value="1">
              <span>Rimuovi la foto attuale</span>
            </label>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="form-group mb-0">
        <label class="form-label" for="about_intro">Presentazione (pagina “Chi siamo”)</label>
        <textarea name="about_intro" id="about_intro" class="form-control" rows="5"><?php echo e(getSetting('about_intro')); ?></textarea>
      </div>
    </div>
  </div>

  <!-- ============================ PRENOTAZIONI ======================== -->
  <div class="panel">
    <div class="panel-head"><h2><i class="fas fa-calendar-check"></i> Prenotazioni</h2></div>
    <div class="panel-body">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label" for="booking_notice_days">Preavviso minimo (giorni)</label>
          <input type="number" name="booking_notice_days" id="booking_notice_days" class="form-control" min="0" max="60"
                 value="<?php echo e(getSetting('booking_notice_days', '2')); ?>">
          <p class="form-hint">Il modulo non accetta date più vicine di così.</p>
        </div>

        <div class="form-group full">
          <label class="form-label" for="booking_intro">Testo introduttivo del modulo</label>
          <textarea name="booking_intro" id="booking_intro" class="form-control" rows="3"><?php echo e(getSetting('booking_intro')); ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================ SEO E PRIVACY ======================= -->
  <div class="panel">
    <div class="panel-head"><h2><i class="fas fa-magnifying-glass"></i> Google e privacy</h2></div>
    <div class="panel-body">
      <div class="form-group">
        <label class="form-label" for="seo_description">Descrizione per Google</label>
        <textarea name="seo_description" id="seo_description" class="form-control" rows="3"><?php echo e(getSetting('seo_description')); ?></textarea>
        <p class="form-hint">È il testo grigio che appare sotto il titolo nei risultati di ricerca. Massimo ~155 caratteri.</p>
      </div>

      <div class="form-group">
        <label class="form-label" for="privacy_owner">Titolare del trattamento dati</label>
        <input type="text" name="privacy_owner" id="privacy_owner" class="form-control" value="<?php echo e(getSetting('privacy_owner')); ?>">
        <p class="form-hint">Ragione sociale che compare nell'informativa privacy.</p>
      </div>

      <div class="form-group mb-0">
        <label class="form-label" for="gdpr_consent_text">Testo del consenso privacy</label>
        <textarea name="gdpr_consent_text" id="gdpr_consent_text" class="form-control" rows="2"><?php echo e(getSetting('gdpr_consent_text')); ?></textarea>
        <p class="form-hint">Compare accanto alla casella da spuntare nei moduli contatti e prenotazioni.</p>
      </div>
    </div>
  </div>

  <div class="btn-row" style="position:sticky;bottom:1rem">
    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-floppy-disk"></i> Salva tutte le impostazioni</button>
  </div>
</form>

<!-- ============================ PASSWORD ============================ -->
<div class="panel mt-4">
  <div class="panel-head"><h2><i class="fas fa-key"></i> Password di accesso</h2></div>
  <div class="panel-body">
    <p class="text-muted mb-3">
      Cambia la password con cui entri in questo pannello.
      Se la dimentichi, si può reimpostare solo intervenendo sul server: annotala in un posto sicuro.
    </p>
    <form method="post">
      <?php echo csrfField(); ?>
      <input type="hidden" name="form" value="password">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label" for="current_password">Password attuale</label>
          <input type="password" name="current_password" id="current_password" class="form-control" required autocomplete="current-password">
        </div>
        <div class="form-group">
          <label class="form-label" for="new_password">Nuova password</label>
          <input type="password" name="new_password" id="new_password" class="form-control" required minlength="8" autocomplete="new-password">
          <p class="form-hint">Almeno 8 caratteri.</p>
        </div>
        <div class="form-group">
          <label class="form-label" for="confirm_password">Ripeti la nuova password</label>
          <input type="password" name="confirm_password" id="confirm_password" class="form-control" required minlength="8" autocomplete="new-password">
        </div>
      </div>
      <button type="submit" class="btn btn-outline"><i class="fas fa-key"></i> Cambia password</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/_layout_end.php'; ?>
