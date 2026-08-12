<?php
$adminTitle = 'Messaggi';
$adminPage  = 'messages';

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

    $ids = array_values(array_filter(array_map('intval', (array)($_POST['ids'] ?? []))));
    $action = $_POST['status'] ?? '';

    if (!$ids) {
        flash('Seleziona almeno un messaggio.', 'info');
    } elseif ($action === 'delete') {
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $db->prepare("DELETE FROM messages WHERE id IN ($ph)")->execute($ids);
        flash(count($ids) . ' messaggi eliminati.');
    } elseif (in_array($action, ['new', 'read', 'replied', 'archived'], true)) {
        $ph = implode(',', array_fill(0, count($ids), '?'));

        // Le colonne data vanno aggiornate solo per lo stato corrispondente
        $extra = match ($action) {
            'read'    => ", read_at = COALESCE(read_at, datetime('now'))",
            'replied' => ", replied_at = datetime('now'), read_at = COALESCE(read_at, datetime('now'))",
            default   => '',
        };

        $stmt = $db->prepare("UPDATE messages SET status = ?$extra WHERE id IN ($ph)");
        $stmt->execute([$action, ...$ids]);
        flash(count($ids) . ' messaggi segnati come «' . statusLabel($action) . '».');
    } else {
        flash('Azione non riconosciuta.', 'error');
    }

    header('Location: messages.php');
    exit;
}

// Aprire la pagina segna come letti i messaggi nuovi solo se richiesto
if (isset($_GET['read_all'])) {
    $db->exec("UPDATE messages SET status = 'read', read_at = COALESCE(read_at, datetime('now')) WHERE status = 'new'");
    flash('Tutti i messaggi sono stati segnati come letti.');
    header('Location: messages.php');
    exit;
}

/* ---------------------------------------------------------------------
 * Elenco
 * ------------------------------------------------------------------- */
$filter = $_GET['status'] ?? 'all';
$sql = "SELECT * FROM messages";
$params = [];
if (in_array($filter, ['new', 'read', 'replied', 'archived'], true)) {
    $sql .= " WHERE status = ?";
    $params[] = $filter;
}
$sql .= " ORDER BY CASE WHEN status = 'new' THEN 0 ELSE 1 END, created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

$counts = [];
foreach ($db->query("SELECT status, COUNT(*) n FROM messages GROUP BY status")->fetchAll() as $r) {
    $counts[$r['status']] = (int)$r['n'];
}
$counts['all'] = array_sum($counts);

$adminAction = ($counts['new'] ?? 0) > 0
    ? '<a href="?read_all=1" class="btn btn-outline"><i class="fas fa-envelope-open"></i> Segna tutti come letti</a>'
    : '';

require __DIR__ . '/_layout.php';
?>

<div class="panel">
  <div class="toolbar">
    <?php
    $tabs = ['all' => 'Tutti', 'new' => 'Non letti', 'read' => 'Letti', 'replied' => 'Risposti', 'archived' => 'Archiviati'];
    foreach ($tabs as $key => $label): $n = $counts[$key] ?? 0; ?>
    <a href="?status=<?php echo e($key); ?>" class="chip-filter<?php echo $filter === $key ? ' active' : ''; ?>">
      <?php echo e($label); ?><?php echo $n ? ' (' . $n . ')' : ''; ?>
    </a>
    <?php endforeach; ?>
  </div>

  <?php if (!$items): ?>
    <div class="empty">
      <i class="fas fa-envelope-open"></i>
      <h3>Nessun messaggio<?php echo $filter !== 'all' ? ' in questo stato' : ''; ?></h3>
      <p>I messaggi dal modulo contatti del sito compaiono qui.</p>
      <?php if ($filter !== 'all'): ?><a href="?status=all" class="btn btn-outline">Mostra tutti</a><?php endif; ?>
    </div>
  <?php else: ?>
    <form method="post" data-bulk-form>
      <?php echo csrfField(); ?>

      <div class="bulk-bar" data-bulk-bar>
        <strong data-bulk-count>0 selezionati</strong>
        <div class="spacer"></div>
        <button type="submit" name="status" value="read" class="btn btn-sm btn-warning"><i class="fas fa-envelope-open"></i> Letto</button>
        <button type="submit" name="status" value="replied" class="btn btn-sm btn-success"><i class="fas fa-reply"></i> Risposto</button>
        <button type="submit" name="status" value="archived" class="btn btn-sm btn-outline"><i class="fas fa-box-archive"></i> Archivia</button>
        <button type="submit" name="status" value="delete" class="btn btn-sm btn-danger"
                data-confirm="Eliminare definitivamente i messaggi selezionati?">
          <i class="fas fa-trash"></i> Elimina
        </button>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th style="width:36px"><input type="checkbox" data-check-all aria-label="Seleziona tutto"></th>
              <th>Da</th>
              <th>Oggetto</th>
              <th class="hide-sm">Anteprima</th>
              <th>Stato</th>
              <th class="hide-sm">Ricevuto</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $m): ?>
            <tr<?php echo $m['status'] === 'new' ? ' class="row-unread"' : ''; ?>>
              <td><input type="checkbox" name="ids[]" value="<?php echo (int)$m['id']; ?>" data-check
                         aria-label="Seleziona messaggio di <?php echo e($m['name']); ?>"></td>
              <td>
                <span class="title"><?php echo e($m['name']); ?></span>
                <small>
                  <a href="mailto:<?php echo e($m['email']); ?>"><?php echo e($m['email']); ?></a>
                  <?php if ($m['phone']): ?> · <a href="tel:<?php echo e(telHref($m['phone'])); ?>"><?php echo e($m['phone']); ?></a><?php endif; ?>
                </small>
              </td>
              <td><?php echo e($m['subject'] ?: '—'); ?></td>
              <td class="hide-sm text-muted"><?php echo e(truncate($m['message'], 60)); ?></td>
              <td><?php echo statusBadge($m['status']); ?></td>
              <td class="hide-sm nowrap"><small><?php echo e(formatDate($m['created_at'])); ?></small></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </form>

    <?php foreach ($items as $m): ?>
    <details class="detail">
      <summary>
        <i class="fas fa-envelope<?php echo $m['status'] === 'new' ? '' : '-open'; ?> text-muted"></i>
        <strong><?php echo e($m['name']); ?></strong>
        <span class="text-muted">— <?php echo e($m['subject'] ?: 'senza oggetto'); ?></span>
      </summary>
      <div class="detail-body">
        <div class="quote"><?php echo e($m['message']); ?></div>
        <div class="btn-row mt-3">
          <a class="btn btn-sm btn-primary"
             href="mailto:<?php echo e($m['email']); ?>?subject=<?php echo rawurlencode('Re: ' . ($m['subject'] ?: 'la tua richiesta')); ?>&body=<?php echo rawurlencode("Gentile " . $m['name'] . ",\n\n"); ?>">
            <i class="fas fa-reply"></i> Rispondi via email
          </a>
          <?php if ($m['phone'] && ($waNum = preg_replace('/[^\d]/', '', $m['phone']))): ?>
          <a class="btn btn-sm btn-outline" target="_blank" rel="noopener"
             href="https://wa.me/<?php echo e(strlen($waNum) <= 11 ? '39' . $waNum : $waNum); ?>">
            <i class="fab fa-whatsapp"></i> WhatsApp
          </a>
          <?php endif; ?>
          <span class="text-muted" style="font-size:.85rem;margin-left:auto">
            Ricevuto il <?php echo e(formatDate($m['created_at'], true)); ?>
          </span>
        </div>
      </div>
    </details>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/_layout_end.php'; ?>
