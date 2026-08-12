<?php
$adminTitle = 'Riepilogo';
$adminPage  = 'dashboard';
require __DIR__ . '/_layout.php';

$pendingBookings = (int)$db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$newMessages     = (int)$db->query("SELECT COUNT(*) FROM messages WHERE status = 'new'")->fetchColumn();
$upcomingEvents  = (int)$db->query("SELECT COUNT(*) FROM events WHERE published = 1 AND event_date >= date('now')")->fetchColumn();
$activeServices  = (int)$db->query("SELECT COUNT(*) FROM services WHERE published = 1")->fetchColumn();
$galleryCount    = (int)$db->query("SELECT COUNT(*) FROM gallery WHERE published = 1")->fetchColumn();
$projectCount    = (int)$db->query("SELECT COUNT(*) FROM projects WHERE published = 1")->fetchColumn();

$recentBookings = $db->query(
    "SELECT b.*, s.title AS service_title, e.title AS event_title
     FROM bookings b
     LEFT JOIN services s ON s.id = b.service_id
     LEFT JOIN events   e ON e.id = b.event_id
     ORDER BY b.created_at DESC LIMIT 6"
)->fetchAll();

$recentMessages = $db->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 6")->fetchAll();

$nextEvents = $db->query(
    "SELECT * FROM events WHERE published = 1 AND event_date >= date('now')
     ORDER BY event_date, event_time LIMIT 4"
)->fetchAll();

// Cosa manca per avere un sito "completo": una checklist concreta.
$todo = [];
if ($galleryCount < 6)  $todo[] = ['gallery.php',  'Carica almeno 6 foto in galleria', 'Le immagini sono ciò che convince chi non ti conosce.'];
if ($projectCount < 3)  $todo[] = ['projects.php', 'Aggiungi qualche progetto al portfolio', 'Mostrare lavori già fatti aumenta la fiducia.'];
if ($upcomingEvents === 0) $todo[] = ['events.php', 'Metti in calendario un workshop', 'La pagina Eventi è vuota: senza date non arrivano prenotazioni.'];
if (trim(getSetting('business_whatsapp', '')) === '') $todo[] = ['settings.php', 'Aggiungi il numero WhatsApp', 'Attiva il pulsante verde: è il canale più usato dai clienti.'];
if (trim(getSetting('home_hero_image', '')) === '')   $todo[] = ['settings.php', 'Scegli la foto principale della home', 'Al momento la home mostra un segnaposto grafico.'];
if (trim(getSetting('business_maps_embed', '')) === '') $todo[] = ['settings.php', 'Incolla la mappa di Google', 'Aiuta i clienti a trovare l\'atelier.'];
?>

<div class="stats">
  <a href="bookings.php" class="stat-card<?php echo $pendingBookings > 0 ? ' alert' : ''; ?>">
    <span class="icon"><i class="fas fa-calendar-check"></i></span>
    <span>
      <span class="number"><?php echo $pendingBookings; ?></span>
      <span class="label">Prenotazioni da gestire</span>
    </span>
  </a>
  <a href="messages.php" class="stat-card<?php echo $newMessages > 0 ? ' alert' : ''; ?>">
    <span class="icon"><i class="fas fa-envelope"></i></span>
    <span>
      <span class="number"><?php echo $newMessages; ?></span>
      <span class="label">Messaggi non letti</span>
    </span>
  </a>
  <a href="events.php" class="stat-card">
    <span class="icon"><i class="fas fa-calendar-day"></i></span>
    <span>
      <span class="number"><?php echo $upcomingEvents; ?></span>
      <span class="label">Eventi in programma</span>
    </span>
  </a>
  <a href="services.php" class="stat-card">
    <span class="icon"><i class="fas fa-spa"></i></span>
    <span>
      <span class="number"><?php echo $activeServices; ?></span>
      <span class="label">Servizi pubblicati</span>
    </span>
  </a>
</div>

<?php if ($todo): ?>
<div class="panel">
  <div class="panel-head">
    <h2><i class="fas fa-wand-magic-sparkles" style="color:var(--a-clay)"></i> Per migliorare il sito</h2>
  </div>
  <div class="panel-body">
    <p class="text-muted mb-3">Piccoli passi che fanno una grande differenza su chi visita il sito.</p>
    <?php foreach ($todo as [$link, $titolo, $perche]): ?>
    <a href="<?php echo e($link); ?>" class="d-flex align-center gap-3" style="padding:.85rem 0;border-top:1px solid var(--a-line-soft);color:inherit">
      <i class="fas fa-circle-arrow-right" style="color:var(--a-green)"></i>
      <span>
        <strong style="color:var(--a-ink)"><?php echo e($titolo); ?></strong>
        <small class="text-muted" style="display:block"><?php echo e($perche); ?></small>
      </span>
    </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<div class="grid-2">

  <div class="panel">
    <div class="panel-head">
      <h2>Ultime prenotazioni</h2>
      <div class="spacer"></div>
      <a href="bookings.php" class="btn btn-sm btn-outline">Vedi tutte</a>
    </div>
    <?php if (!$recentBookings): ?>
      <div class="empty">
        <i class="fas fa-calendar-check"></i>
        <h3>Nessuna prenotazione</h3>
        <p>Le richieste dal sito compariranno qui.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Cliente</th><th class="hide-sm">Richiesta</th><th>Data</th><th>Stato</th></tr></thead>
          <tbody>
          <?php foreach ($recentBookings as $b): ?>
            <tr>
              <td>
                <span class="title"><?php echo e($b['first_name'] . ' ' . $b['last_name']); ?></span>
                <small><?php echo e($b['email']); ?></small>
              </td>
              <td class="hide-sm"><?php echo e($b['service_title'] ?: ($b['event_title'] ?: ucfirst($b['booking_type']))); ?></td>
              <td class="nowrap"><?php echo e(formatDate($b['booking_date'])); ?></td>
              <td><?php echo statusBadge($b['status']); ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h2>Ultimi messaggi</h2>
      <div class="spacer"></div>
      <a href="messages.php" class="btn btn-sm btn-outline">Vedi tutti</a>
    </div>
    <?php if (!$recentMessages): ?>
      <div class="empty">
        <i class="fas fa-envelope"></i>
        <h3>Nessun messaggio</h3>
        <p>I messaggi dal modulo contatti arrivano qui.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Da</th><th class="hide-sm">Oggetto</th><th>Ricevuto</th><th>Stato</th></tr></thead>
          <tbody>
          <?php foreach ($recentMessages as $m): ?>
            <tr<?php echo $m['status'] === 'new' ? ' class="row-unread"' : ''; ?>>
              <td>
                <span class="title"><?php echo e($m['name']); ?></span>
                <small><?php echo e($m['email']); ?></small>
              </td>
              <td class="hide-sm"><?php echo e($m['subject'] ?: '—'); ?></td>
              <td class="nowrap"><?php echo e(formatDate($m['created_at'])); ?></td>
              <td><?php echo statusBadge($m['status']); ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php if ($nextEvents): ?>
<div class="panel">
  <div class="panel-head">
    <h2>Prossimi appuntamenti</h2>
    <div class="spacer"></div>
    <a href="events.php" class="btn btn-sm btn-outline">Gestisci eventi</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Evento</th><th>Data</th><th>Iscritti</th><th class="hide-sm">Prezzo</th></tr></thead>
      <tbody>
      <?php foreach ($nextEvents as $ev):
        $q = $db->prepare("SELECT COALESCE(SUM(participants),0) FROM bookings WHERE event_id = ? AND status IN ('pending','confirmed')");
        $q->execute([$ev['id']]);
        $taken = (int)$q->fetchColumn();
        $max = (int)$ev['max_participants'];
      ?>
        <tr>
          <td><span class="title"><?php echo e($ev['title']); ?></span></td>
          <td class="nowrap"><?php echo e(formatDate($ev['event_date'])); ?><small><?php echo e(formatTime($ev['event_time'])); ?></small></td>
          <td>
            <strong><?php echo $taken; ?></strong><?php echo $max > 0 ? ' <span class="text-muted">/ ' . $max . '</span>' : ''; ?>
            <?php if ($max > 0 && $taken >= $max): ?><span class="pill pill-rejected">Completo</span><?php endif; ?>
          </td>
          <td class="hide-sm"><?php echo e(formatPrice($ev['price'])); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/_layout_end.php'; ?>
