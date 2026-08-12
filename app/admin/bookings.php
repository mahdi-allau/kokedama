<?php
$adminTitle = 'Prenotazioni';
$adminPage  = 'bookings';

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
initDatabase();
$db = getDB();

/* ---------------------------------------------------------------------
 * Azioni
 * ------------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();

    // Nota interna su una singola prenotazione
    if (($_POST['action'] ?? '') === 'note') {
        $stmt = $db->prepare("UPDATE bookings SET admin_notes = ?, modified_at = datetime('now') WHERE id = ?");
        $stmt->execute([trim($_POST['admin_notes'] ?? ''), (int)$_POST['id']]);
        flash('Nota salvata.');
        header('Location: bookings.php');
        exit;
    }

    $ids = array_values(array_filter(array_map('intval', (array)($_POST['ids'] ?? []))));
    $newStatus = $_POST['status'] ?? '';
    $allowed = ['confirmed', 'rejected', 'cancelled', 'completed', 'pending'];

    if (!$ids) {
        flash('Seleziona almeno una prenotazione.', 'info');
    } elseif (!in_array($newStatus, $allowed, true)) {
        flash('Azione non riconosciuta.', 'error');
    } else {
        $ph = implode(',', array_fill(0, count($ids), '?'));

        // Recupero i dati PRIMA dell'aggiornamento: servono per le email
        $before = $db->prepare(
            "SELECT b.*, s.title AS service_title, e.title AS event_title
             FROM bookings b
             LEFT JOIN services s ON s.id = b.service_id
             LEFT JOIN events   e ON e.id = b.event_id
             WHERE b.id IN ($ph)"
        );
        $before->execute($ids);
        $rows = $before->fetchAll();

        $upd = $db->prepare("UPDATE bookings SET status = ?, modified_at = datetime('now') WHERE id IN ($ph)");
        $upd->execute([$newStatus, ...$ids]);

        // Avviso al cliente solo per conferma o rifiuto
        $sent = 0;
        if (in_array($newStatus, ['confirmed', 'rejected'], true) && empty($_POST['skip_email'])) {
            foreach ($rows as $b) {
                $what = $b['service_title'] ?: ($b['event_title'] ?: ucfirst($b['booking_type']));
                $quando = formatDate($b['booking_date'], true)
                        . ($b['booking_time'] ? ' alle ' . formatTime($b['booking_time']) : '');

                if ($newStatus === 'confirmed') {
                    $subject = 'Prenotazione confermata';
                    $body = "Gentile {$b['first_name']},\n\n"
                          . "la tua prenotazione è confermata!\n\n"
                          . "Richiesta: $what\nQuando: $quando\nPartecipanti: {$b['participants']}\n\n"
                          . "Ti aspettiamo in " . fullAddress() . ".\n"
                          . "Se hai un imprevisto, avvisaci: siamo flessibili."
                          . emailSignature();
                } else {
                    $subject = 'Prenotazione non confermata';
                    $body = "Gentile {$b['first_name']},\n\n"
                          . "purtroppo non possiamo confermare la tua richiesta per $quando.\n\n"
                          . "Scrivici o chiamaci: troviamo volentieri una data alternativa."
                          . emailSignature();
                }
                if (sendEmail($b['email'], $subject, $body)) $sent++;
            }
        }

        $msg = count($ids) . ' prenotazioni aggiornate a «' . statusLabel($newStatus) . '»';
        $msg .= $sent > 0 ? ", $sent email inviate al cliente." : '.';
        flash($msg);
    }

    header('Location: bookings.php');
    exit;
}

/* ---------------------------------------------------------------------
 * Elenco
 * ------------------------------------------------------------------- */
$filter = $_GET['status'] ?? 'pending';
$sql = "SELECT b.*, s.title AS service_title, e.title AS event_title
        FROM bookings b
        LEFT JOIN services s ON s.id = b.service_id
        LEFT JOIN events   e ON e.id = b.event_id";
$params = [];
if ($filter !== 'all' && in_array($filter, ['pending','confirmed','rejected','cancelled','completed'], true)) {
    $sql .= " WHERE b.status = ?";
    $params[] = $filter;
}
$sql .= " ORDER BY CASE WHEN b.status = 'pending' THEN 0 ELSE 1 END, b.booking_date, b.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

$counts = [];
foreach ($db->query("SELECT status, COUNT(*) n FROM bookings GROUP BY status")->fetchAll() as $r) {
    $counts[$r['status']] = (int)$r['n'];
}
$counts['all'] = array_sum($counts);

require __DIR__ . '/_layout.php';
?>

<div class="panel">
  <div class="toolbar" data-filter-nav>
    <?php
    $tabs = ['pending' => 'Da gestire', 'confirmed' => 'Confermate', 'completed' => 'Completate',
             'rejected' => 'Rifiutate', 'cancelled' => 'Annullate', 'all' => 'Tutte'];
    foreach ($tabs as $key => $label):
      $n = $counts[$key] ?? 0;
    ?>
    <a href="?status=<?php echo e($key); ?>" class="chip-filter<?php echo $filter === $key ? ' active' : ''; ?>">
      <?php echo e($label); ?><?php echo $n ? ' (' . $n . ')' : ''; ?>
    </a>
    <?php endforeach; ?>
  </div>

  <?php if (!$items): ?>
    <div class="empty">
      <i class="fas fa-calendar-check"></i>
      <h3>Nessuna prenotazione<?php echo $filter !== 'all' ? ' in questo stato' : ''; ?></h3>
      <p>Le richieste inviate dal sito compaiono qui, con tutti i dati del cliente.</p>
      <?php if ($filter !== 'all'): ?><a href="?status=all" class="btn btn-outline">Mostra tutte</a><?php endif; ?>
    </div>
  <?php else: ?>
    <form method="post" data-bulk-form>
      <?php echo csrfField(); ?>

      <div class="bulk-bar" data-bulk-bar>
        <strong data-bulk-count>0 selezionate</strong>
        <label class="form-check" style="margin-left:.5rem">
          <input type="checkbox" name="skip_email" value="1">
          <span>Non inviare email al cliente</span>
        </label>
        <div class="spacer"></div>
        <button type="submit" name="status" value="confirmed" class="btn btn-sm btn-success"
                data-confirm="Confermare le prenotazioni selezionate? Il cliente riceverà un'email.">
          <i class="fas fa-check"></i> Conferma
        </button>
        <button type="submit" name="status" value="rejected" class="btn btn-sm btn-danger"
                data-confirm="Rifiutare le prenotazioni selezionate? Il cliente riceverà un'email.">
          <i class="fas fa-xmark"></i> Rifiuta
        </button>
        <button type="submit" name="status" value="completed" class="btn btn-sm btn-info"><i class="fas fa-flag-checkered"></i> Completata</button>
        <button type="submit" name="status" value="cancelled" class="btn btn-sm btn-outline"><i class="fas fa-ban"></i> Annulla</button>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th style="width:36px"><input type="checkbox" data-check-all aria-label="Seleziona tutto"></th>
              <th>Cliente</th>
              <th>Richiesta</th>
              <th>Quando</th>
              <th class="hide-sm">Persone</th>
              <th>Stato</th>
              <th class="hide-sm">Ricevuta</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $b): ?>
            <tr<?php echo $b['status'] === 'pending' ? ' class="row-unread"' : ''; ?>>
              <td><input type="checkbox" name="ids[]" value="<?php echo (int)$b['id']; ?>" data-check
                         aria-label="Seleziona prenotazione di <?php echo e($b['first_name']); ?>"></td>
              <td>
                <span class="title"><?php echo e($b['first_name'] . ' ' . $b['last_name']); ?></span>
                <small>
                  <a href="mailto:<?php echo e($b['email']); ?>"><?php echo e($b['email']); ?></a>
                  <?php if ($b['phone']): ?> · <a href="tel:<?php echo e(telHref($b['phone'])); ?>"><?php echo e($b['phone']); ?></a><?php endif; ?>
                </small>
              </td>
              <td>
                <?php echo e($b['service_title'] ?: ($b['event_title'] ?: ucfirst($b['booking_type']))); ?>
                <?php if ($b['notes']): ?><small><i class="fas fa-note-sticky"></i> <?php echo e(truncate($b['notes'], 50)); ?></small><?php endif; ?>
              </td>
              <td class="nowrap">
                <span class="title"><?php echo e(formatDate($b['booking_date'])); ?></span>
                <?php if ($b['booking_time']): ?><small><?php echo e(formatTime($b['booking_time'])); ?></small><?php endif; ?>
              </td>
              <td class="hide-sm"><?php echo (int)$b['participants']; ?></td>
              <td><?php echo statusBadge($b['status']); ?></td>
              <td class="hide-sm nowrap"><small><?php echo e(formatDate($b['created_at'])); ?></small></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </form>

    <?php foreach ($items as $b): ?>
    <details class="detail">
      <summary>
        <i class="fas fa-<?php echo ($b['notes'] || $b['admin_notes']) ? 'note-sticky' : 'circle-info'; ?> text-muted"></i>
        <strong><?php echo e($b['first_name'] . ' ' . $b['last_name']); ?></strong>
        <span class="text-muted">— <?php echo e(formatDate($b['booking_date'])); ?></span>
        <?php if ($b['notes']): ?><span class="pill pill-off">ha lasciato una nota</span><?php endif; ?>
      </summary>
      <div class="detail-body">
        <dl class="detail-grid">
          <div><dt>Richiesta</dt><dd><?php echo e($b['service_title'] ?: ($b['event_title'] ?: ucfirst($b['booking_type']))); ?></dd></div>
          <div><dt>Quando</dt><dd><?php echo e(formatDate($b['booking_date'], true)); ?><?php echo $b['booking_time'] ? ' · ' . e(formatTime($b['booking_time'])) : ''; ?></dd></div>
          <div><dt>Partecipanti</dt><dd><?php echo (int)$b['participants']; ?></dd></div>
          <div><dt>Email</dt><dd><a href="mailto:<?php echo e($b['email']); ?>"><?php echo e($b['email']); ?></a></dd></div>
          <div><dt>Telefono</dt><dd><?php echo $b['phone'] ? '<a href="tel:' . e(telHref($b['phone'])) . '">' . e($b['phone']) . '</a>' : '—'; ?></dd></div>
          <div><dt>Ricevuta il</dt><dd><?php echo e(formatDate($b['created_at'])); ?></dd></div>
        </dl>
        <?php if ($b['notes']): ?>
        <p class="text-muted mt-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.08em">Note del cliente</p>
        <div class="quote"><?php echo e($b['notes']); ?></div>
        <?php endif; ?>
        <form method="post" class="mt-3">
          <?php echo csrfField(); ?>
          <input type="hidden" name="action" value="note">
          <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>">
          <label class="form-label">Nota interna (non visibile al cliente)</label>
          <textarea name="admin_notes" class="form-control" rows="2"><?php echo e($b['admin_notes']); ?></textarea>
          <button type="submit" class="btn btn-sm btn-outline mt-2"><i class="fas fa-floppy-disk"></i> Salva nota</button>
        </form>
      </div>
    </details>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/_layout_end.php'; ?>
