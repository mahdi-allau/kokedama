<?php
$db = getDB();
$services = $db->query("SELECT id, title, price, duration FROM services WHERE published = 1 ORDER BY ordering, title")->fetchAll();
$events   = $db->query("SELECT id, title, event_date, event_time, price FROM events WHERE published = 1 AND event_date >= date('now') ORDER BY event_date")->fetchAll();

$noticeDays = max(0, (int)getSetting('booking_notice_days', '2'));
$minDate = date('Y-m-d', strtotime("+$noticeDays days"));

// Preselezione da /service/... o /event/...
$preService = isset($_GET['service']) ? (int)$_GET['service'] : 0;
$preEvent   = isset($_GET['event'])   ? (int)$_GET['event']   : 0;

$errors = [];
$old = [
    'booking_type' => $preEvent ? 'evento' : 'servizio',
    'service_id' => $preService,
    'event_id' => $preEvent,
    'booking_date' => '', 'booking_time' => '', 'participants' => 1,
    'first_name' => '', 'last_name' => '', 'email' => '', 'phone' => '', 'notes' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();

    foreach (array_keys($old) as $campo) {
        if (isset($_POST[$campo])) $old[$campo] = $_POST[$campo];
    }

    $type = in_array($_POST['booking_type'] ?? '', ['servizio', 'evento'], true) ? $_POST['booking_type'] : 'servizio';
    $serviceId = !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null;
    $eventId   = !empty($_POST['event_id'])   ? (int)$_POST['event_id']   : null;

    // In base al tipo teniamo solo il riferimento pertinente
    if ($type === 'evento') { $serviceId = null; } else { $eventId = null; }

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $notes     = trim($_POST['notes'] ?? '');
    $date      = $_POST['booking_date'] ?? '';
    $time      = $_POST['booking_time'] ?? '';
    $people    = max(1, min(50, (int)($_POST['participants'] ?? 1)));

    // Per gli eventi data e ora vengono dall'evento stesso
    if ($type === 'evento' && $eventId) {
        $ev = $db->prepare("SELECT event_date, event_time FROM events WHERE id = ? AND published = 1");
        $ev->execute([$eventId]);
        if ($row = $ev->fetch()) { $date = $row['event_date']; $time = $row['event_time']; }
        else { $errors[] = 'L\'evento selezionato non è più disponibile.'; }
    }

    if ($type === 'evento' && !$eventId)   $errors[] = 'Scegli l\'evento a cui vuoi partecipare.';
    if ($type === 'servizio' && !$serviceId) $errors[] = 'Scegli il servizio che ti interessa.';
    if ($firstName === '') $errors[] = 'Il nome è obbligatorio.';
    if ($lastName === '')  $errors[] = 'Il cognome è obbligatorio.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Inserisci un indirizzo email valido.';
    if ($type === 'servizio') {
        if ($date === '') $errors[] = 'Indica la data che preferisci.';
        elseif ($date < $minDate) $errors[] = 'Scegli una data a partire dal ' . formatDate($minDate) . '.';
    }
    if (empty($_POST['consent_gdpr'])) $errors[] = 'Devi accettare l\'informativa privacy per inviare la richiesta.';

    // Bot: fingiamo il successo senza salvare nulla
    if (honeypotTriggered()) {
        flash('Prenotazione inviata! Ti contatteremo presto.');
        header('Location: ' . APP_URL . '/booking');
        exit;
    }

    if (!$errors) {
        $stmt = $db->prepare(
            "INSERT INTO bookings (booking_type, service_id, event_id, booking_date, booking_time,
             participants, first_name, last_name, email, phone, notes, consent_gdpr, ip_address)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            $type, $serviceId, $eventId, $date ?: null, $time ?: null, $people,
            $firstName, $lastName, $email, $phone, $notes, 1, $_SERVER['REMOTE_ADDR'] ?? ''
        ]);

        // Oggetto della richiesta, per l'email
        $what = '';
        if ($serviceId) {
            $q = $db->prepare("SELECT title FROM services WHERE id = ?"); $q->execute([$serviceId]);
            $what = (string)$q->fetchColumn();
        } elseif ($eventId) {
            $q = $db->prepare("SELECT title FROM events WHERE id = ?"); $q->execute([$eventId]);
            $what = (string)$q->fetchColumn();
        }

        $customer = "$firstName $lastName";
        $riepilogo = "Richiesta: " . ($what ?: ucfirst($type)) . "\n"
                   . "Data: " . formatDate($date) . ($time ? ' alle ' . formatTime($time) : '') . "\n"
                   . "Partecipanti: $people\n";

        sendEmail(
            getSetting('business_email'),
            'Nuova prenotazione da ' . $customer,
            "Hai ricevuto una nuova richiesta di prenotazione.\n\n"
            . $riepilogo
            . "\nCliente: $customer\nEmail: $email\nTelefono: " . ($phone ?: '—') . "\n"
            . ($notes !== '' ? "\nNote:\n$notes\n" : '')
            . "\nGestiscila qui: " . APP_URL . "/admin/bookings.php\n"
        );

        sendEmail(
            $email,
            'Abbiamo ricevuto la tua richiesta',
            "Gentile $firstName,\n\ngrazie per la tua richiesta! L'abbiamo ricevuta e ti confermiamo entro 24 ore.\n\n"
            . $riepilogo
            . "\nSe hai bisogno di modificare qualcosa, rispondi pure a questa email."
            . emailSignature()
        );

        flash('Richiesta inviata! Ti abbiamo mandato una email di riepilogo e ti confermiamo entro 24 ore.');
        header('Location: ' . APP_URL . '/booking');
        exit;
    }

    $old['booking_type'] = $type;
}

$pageTitle = 'Prenota';
$activePage = 'booking';
$metaDescription = 'Prenota un workshop di kokedama o un servizio su misura a ' . getSetting('business_city', 'Ferrara') . '. Conferma entro 24 ore, nessun pagamento anticipato.';
require __DIR__ . '/../templates/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Percorso">
      <a href="<?php echo APP_URL; ?>/">Home</a><i class="fas fa-chevron-right"></i><span>Prenota</span>
    </nav>
    <span class="eyebrow eyebrow--center">Richiesta</span>
    <h1 class="display-2">Prenota</h1>
    <p class="lead"><?php echo e(getSetting('booking_intro')); ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="contact-grid" style="grid-template-columns:1.4fr .6fr">

      <div class="booking-form">
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

          <div class="form-group">
            <label class="form-label">Cosa vuoi prenotare? <span class="req">*</span></label>
            <div class="choice-group">
              <label class="choice">
                <input type="radio" name="booking_type" value="servizio" data-toggle-type
                       <?php echo $old['booking_type'] === 'servizio' ? 'checked' : ''; ?>>
                <i class="fas fa-spa"></i> Un servizio
              </label>
              <label class="choice">
                <input type="radio" name="booking_type" value="evento" data-toggle-type
                       <?php echo $old['booking_type'] === 'evento' ? 'checked' : ''; ?>>
                <i class="fas fa-calendar-day"></i> Un evento in calendario
              </label>
            </div>
          </div>

          <!-- Blocco SERVIZIO -->
          <div data-block="servizio">
            <div class="form-group">
              <label class="form-label" for="service_id">Servizio <span class="req">*</span></label>
              <select name="service_id" id="service_id" class="form-control form-select">
                <option value="">Seleziona un servizio…</option>
                <?php foreach ($services as $s): ?>
                <option value="<?php echo (int)$s['id']; ?>" <?php echo (int)$old['service_id'] === (int)$s['id'] ? 'selected' : ''; ?>>
                  <?php echo e($s['title']); ?><?php echo $s['price'] ? ' — ' . formatPrice($s['price']) : ''; ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="booking_date">Data preferita <span class="req">*</span></label>
                <input type="date" name="booking_date" id="booking_date" class="form-control"
                       min="<?php echo e($minDate); ?>" value="<?php echo e($old['booking_date']); ?>">
                <p class="form-hint">Con almeno <?php echo $noticeDays; ?> giorni di anticipo.</p>
              </div>
              <div class="form-group">
                <label class="form-label" for="booking_time">Orario preferito</label>
                <input type="time" name="booking_time" id="booking_time" class="form-control" value="<?php echo e($old['booking_time']); ?>">
                <p class="form-hint">Facoltativo: se lasci vuoto, proponiamo noi.</p>
              </div>
            </div>
          </div>

          <!-- Blocco EVENTO -->
          <div data-block="evento">
            <div class="form-group">
              <label class="form-label" for="event_id">Evento <span class="req">*</span></label>
              <?php if ($events): ?>
              <select name="event_id" id="event_id" class="form-control form-select">
                <option value="">Seleziona un evento…</option>
                <?php foreach ($events as $ev): ?>
                <option value="<?php echo (int)$ev['id']; ?>" <?php echo (int)$old['event_id'] === (int)$ev['id'] ? 'selected' : ''; ?>>
                  <?php echo e($ev['title']); ?> — <?php echo e(formatDate($ev['event_date'])); ?><?php echo $ev['event_time'] ? ' ore ' . e(formatTime($ev['event_time'])) : ''; ?>
                </option>
                <?php endforeach; ?>
              </select>
              <p class="form-hint">Data e orario sono già fissati dall'evento.</p>
              <?php else: ?>
              <div class="alert alert-info"><i class="fas fa-circle-info"></i><span>Nessun evento in calendario al momento. Scegli “Un servizio” oppure <a href="<?php echo APP_URL; ?>/contact">scrivici</a>.</span></div>
              <?php endif; ?>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="participants">Quante persone <span class="req">*</span></label>
            <input type="number" name="participants" id="participants" class="form-control"
                   min="1" max="50" value="<?php echo (int)$old['participants']; ?>" required>
          </div>

          <hr style="border:0;border-top:1px solid var(--line-soft);margin:2rem 0">
          <h3 style="font-size:1.25rem;margin-bottom:1.25rem">I tuoi dati</h3>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="first_name">Nome <span class="req">*</span></label>
              <input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo e($old['first_name']); ?>" required autocomplete="given-name">
            </div>
            <div class="form-group">
              <label class="form-label" for="last_name">Cognome <span class="req">*</span></label>
              <input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo e($old['last_name']); ?>" required autocomplete="family-name">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="email">Email <span class="req">*</span></label>
              <input type="email" name="email" id="email" class="form-control" value="<?php echo e($old['email']); ?>" required autocomplete="email">
            </div>
            <div class="form-group">
              <label class="form-label" for="phone">Telefono</label>
              <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo e($old['phone']); ?>" autocomplete="tel">
              <p class="form-hint">Ci aiuta a ricontattarti più in fretta.</p>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="notes">Note o richieste particolari</label>
            <textarea name="notes" id="notes" class="form-control" rows="4"
                      placeholder="Es. è un regalo, siamo un gruppo di colleghi, ho un'allergia…"><?php echo e($old['notes']); ?></textarea>
          </div>

          <div class="form-group">
            <label class="form-check">
              <input type="checkbox" name="consent_gdpr" value="1" required>
              <span><?php echo e(getSetting('gdpr_consent_text')); ?>
                <a href="<?php echo APP_URL; ?>/privacy" target="_blank" class="link-underline">Informativa</a> <span class="req">*</span></span>
            </label>
          </div>

          <button type="submit" class="btn btn-kokedama btn-lg btn-block">
            <i class="fas fa-paper-plane"></i> Invia richiesta
          </button>
          <p class="form-hint text-center mt-2">Non è richiesto alcun pagamento in questa fase.</p>
        </form>
      </div>

      <aside>
        <div class="form-aside">
          <h4>Come procediamo</h4>
          <ul>
            <li><i class="fas fa-paper-plane"></i><span>Invii la richiesta: <strong>non è un impegno</strong>.</span></li>
            <li><i class="fas fa-reply"></i><span>Ti rispondiamo <strong>entro 24 ore</strong> con la conferma.</span></li>
            <li><i class="fas fa-euro-sign"></i><span>Si salda in atelier, il giorno stesso.</span></li>
            <li><i class="fas fa-rotate-left"></i><span>Puoi disdire fino a 48 ore prima.</span></li>
          </ul>

          <div style="margin-top:1.75rem;padding-top:1.25rem;border-top:1px solid var(--green-100)">
            <p style="font-size:.9rem;margin-bottom:.85rem">Preferisci parlarne a voce?</p>
            <a href="tel:<?php echo e(telHref()); ?>" class="btn btn-outline-kokedama btn-sm btn-block"><i class="fas fa-phone"></i> <?php echo e(getSetting('business_phone')); ?></a>
            <?php if ($wa = whatsappLink()): ?>
            <a href="<?php echo e($wa); ?>" target="_blank" rel="noopener" class="btn btn-ghost btn-sm btn-block mt-2"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <?php endif; ?>
          </div>
        </div>
      </aside>

    </div>
  </div>
</section>

<script>
// Mostra solo i campi del tipo di prenotazione scelto.
(function () {
  var radios = document.querySelectorAll('[data-toggle-type]');
  var blocks = document.querySelectorAll('[data-block]');
  if (!radios.length) return;

  function sync() {
    var checked = document.querySelector('[data-toggle-type]:checked');
    var value = checked ? checked.value : 'servizio';
    blocks.forEach(function (block) {
      var active = block.getAttribute('data-block') === value;
      block.hidden = !active;
      // I campi nascosti non devono bloccare l'invio del modulo
      block.querySelectorAll('input, select, textarea').forEach(function (field) {
        field.disabled = !active;
      });
    });
  }

  radios.forEach(function (r) { r.addEventListener('change', sync); });
  sync();
})();
</script>

<?php require __DIR__ . '/../templates/footer.php'; ?>
