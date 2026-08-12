<?php
/**
 * Motore CRUD condiviso del pannello.
 *
 * Ogni sezione gestionale (servizi, eventi, progetti, galleria) è descritta da
 * un array di configurazione e riusa questo file: una sola logica da
 * mantenere, quattro schermate coerenti fra loro.
 *
 * Chiave      Significato
 * ----------- ------------------------------------------------------------
 * table       Nome tabella SQLite (deve essere in $CRUD_TABLES)
 * page        Chiave usata dal menu laterale
 * title       Titolo della sezione (plurale)
 * singular    Nome al singolare, usato nei messaggi
 * uploadDir   Sottocartella di assets/uploads per le immagini
 * slugFrom    Campo da cui generare lo slug (null = tabella senza slug)
 * order       ORDER BY della lista
 * publicUrl   callable($row): URL pubblico dell'elemento, oppure null
 * fields      Campi del modulo
 * columns     Colonne della tabella di elenco
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
initDatabase();

/** Tabelle che il CRUD può toccare: nessun nome arriva mai dalla richiesta. */
const CRUD_TABLES = ['services', 'events', 'projects', 'gallery'];

function crudRun(array $cfg) {
    $db = getDB();
    $table = $cfg['table'];
    if (!in_array($table, CRUD_TABLES, true)) {
        throw new InvalidArgumentException("Tabella non gestita: $table");
    }

    $action = $_GET['action'] ?? 'list';
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $self = basename($_SERVER['SCRIPT_NAME']);

    /* ---------- Scritture --------------------------------------------- */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        requireCsrf();
        $postAction = $_POST['action'] ?? '';

        if ($postAction === 'delete') {
            crudDelete($db, $cfg, (int)($_POST['id'] ?? 0));
            header("Location: $self");
            exit;
        }

        if ($postAction === 'bulk') {
            crudBulk($db, $cfg, array_map('intval', (array)($_POST['ids'] ?? [])), $_POST['bulk'] ?? '');
            header("Location: $self");
            exit;
        }

        if ($postAction === 'save') {
            $savedId = crudSave($db, $cfg, (int)($_POST['id'] ?? 0), $errors);
            if ($savedId) {
                header("Location: $self");
                exit;
            }
            // In caso di errore restiamo sul modulo con i dati appena inseriti
            $action = 'form';
            $id = (int)($_POST['id'] ?? 0);
            $formData = $_POST;
        }
    }

    /* ---------- Schermate --------------------------------------------- */
    $adminPage  = $cfg['page'];
    $adminTitle = $cfg['title'];

    if ($action === 'new' || $action === 'edit' || $action === 'form') {
        $row = $formData ?? null;
        if ($row === null && $id) {
            $stmt = $db->prepare("SELECT * FROM $table WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch() ?: null;
            if (!$row) { flash('Elemento non trovato.', 'error'); header("Location: $self"); exit; }
        }
        // Dopo un errore i dati vengono da $_POST, dove il file non compare:
        // recuperiamo l'immagine già salvata così l'anteprima non sparisce.
        if ($row !== null && !isset($row['image']) && isset($_POST['image_current'])) {
            $row['image'] = $_POST['image_current'];
        }

        $adminTitle = $id
            ? 'Modifica ' . $cfg['singular']
            : 'Nuovo ' . $cfg['singular'];

        require __DIR__ . '/_layout.php';
        crudRenderForm($cfg, $row, $id, $errors ?? [], $self);
        require __DIR__ . '/_layout_end.php';
        return;
    }

    $items = $db->query("SELECT * FROM $table ORDER BY " . $cfg['order'])->fetchAll();
    $adminAction = '<a href="' . e($self) . '?action=new" class="btn btn-primary">'
                 . '<i class="fas fa-plus"></i> Nuovo ' . e($cfg['singular']) . '</a>';

    require __DIR__ . '/_layout.php';
    crudRenderList($cfg, $items, $self);
    require __DIR__ . '/_layout_end.php';
}

/* ======================================================================
 * Salvataggio
 * ==================================================================== */

function crudSave($db, array $cfg, int $id, &$errors) {
    $errors = [];
    $table = $cfg['table'];
    $data = [];

    foreach ($cfg['fields'] as $field) {
        $name = $field['name'];
        $type = $field['type'];

        // Le intestazioni non sono colonne del database
        if ($type === 'section') continue;

        if ($type === 'image') {
            // 1) rimozione esplicita  2) nuovo upload  3) valore invariato
            $current = trim($_POST[$name . '_current'] ?? '');
            if (!empty($_POST[$name . '_remove'])) {
                deleteUpload($current);
                $data[$name] = null;
            } elseif (!empty($_FILES[$name]['name'])) {
                $uploadError = null;
                $path = uploadImage($_FILES[$name], $cfg['uploadDir'], $uploadError);
                if ($path === null) {
                    $errors[] = $uploadError ?: 'Immagine non caricata.';
                    $data[$name] = $current ?: null;
                } else {
                    if ($current) deleteUpload($current);
                    $data[$name] = $path;
                }
            } else {
                $data[$name] = $current ?: null;
            }

            if (!empty($field['required']) && empty($data[$name])) {
                $errors[] = 'Devi caricare un\'immagine per il campo «' . $field['label'] . '».';
            }
            continue;
        }

        $raw = $_POST[$name] ?? null;

        switch ($type) {
            case 'checkbox':
                $value = !empty($raw) ? 1 : 0;
                break;
            case 'number':
                // Campo lasciato vuoto: usiamo il valore predefinito del campo
                // (es. ordering = 0) invece di scrivere NULL in tabella.
                $value = ($raw === '' || $raw === null)
                    ? ($field['default'] ?? null)
                    : (int)$raw;
                break;
            case 'price':
                $raw = str_replace(',', '.', (string)$raw);
                $value = trim($raw) === '' ? null : round((float)$raw, 2);
                break;
            default:
                $value = trim((string)$raw);
                if ($value === '') $value = null;
        }

        if (!empty($field['required']) && ($value === null || $value === '')) {
            $errors[] = 'Il campo «' . $field['label'] . '» è obbligatorio.';
        }

        $data[$name] = $value;
    }

    if ($errors) return null;

    // Slug: generato dal titolo, sempre univoco
    if (!empty($cfg['slugFrom'])) {
        $source = $data[$cfg['slugFrom']] ?? '';
        $data['slug'] = uniqueSlug($table, (string)$source, $id ?: null);
    }

    $columns = array_keys($data);

    if ($id) {
        $set = implode(', ', array_map(fn($c) => "$c = ?", $columns));
        $stmt = $db->prepare("UPDATE $table SET $set WHERE id = ?");
        $stmt->execute([...array_values($data), $id]);
        flash(ucfirst($cfg['singular']) . ' aggiornato con successo.');
        return $id;
    }

    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $stmt = $db->prepare("INSERT INTO $table (" . implode(', ', $columns) . ") VALUES ($placeholders)");
    $stmt->execute(array_values($data));
    flash(ucfirst($cfg['singular']) . ' creato con successo.');
    return (int)$db->lastInsertId();
}

function crudDelete($db, array $cfg, int $id) {
    if (!$id) return;
    $table = $cfg['table'];

    $stmt = $db->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { flash('Elemento già rimosso.', 'info'); return; }

    if (!empty($row['image'])) deleteUpload($row['image']);

    $db->prepare("DELETE FROM $table WHERE id = ?")->execute([$id]);
    flash(ucfirst($cfg['singular']) . ' eliminato.');
}

function crudBulk($db, array $cfg, array $ids, string $bulk) {
    $ids = array_values(array_filter($ids));
    if (!$ids) { flash('Nessun elemento selezionato.', 'info'); return; }

    $table = $cfg['table'];
    $ph = implode(',', array_fill(0, count($ids), '?'));

    switch ($bulk) {
        case 'publish':
            $db->prepare("UPDATE $table SET published = 1 WHERE id IN ($ph)")->execute($ids);
            flash(count($ids) . ' elementi pubblicati.');
            break;
        case 'unpublish':
            $db->prepare("UPDATE $table SET published = 0 WHERE id IN ($ph)")->execute($ids);
            flash(count($ids) . ' elementi nascosti dal sito.');
            break;
        case 'delete':
            $stmt = $db->prepare("SELECT image FROM $table WHERE id IN ($ph)");
            $stmt->execute($ids);
            foreach ($stmt->fetchAll() as $r) {
                if (!empty($r['image'])) deleteUpload($r['image']);
            }
            $db->prepare("DELETE FROM $table WHERE id IN ($ph)")->execute($ids);
            flash(count($ids) . ' elementi eliminati.');
            break;
        default:
            flash('Azione non riconosciuta.', 'error');
    }
}

/* ======================================================================
 * Elenco
 * ==================================================================== */

function crudRenderList(array $cfg, array $items, string $self) {
    ?>
    <?php if (!empty($cfg['intro'])): ?>
    <div class="alert alert-info">
      <i class="fas fa-lightbulb"></i>
      <span><?php echo $cfg['intro']; ?></span>
    </div>
    <?php endif; ?>

    <div class="panel">
      <?php if (!$items): ?>
        <div class="empty">
          <i class="<?php echo e($cfg['icon']); ?>"></i>
          <h3>Ancora nessun <?php echo e($cfg['singular']); ?></h3>
          <p><?php echo e($cfg['emptyHint'] ?? 'Aggiungi il primo elemento: comparirà subito nel sito.'); ?></p>
          <a href="<?php echo e($self); ?>?action=new" class="btn btn-primary">
            <i class="fas fa-plus"></i> Crea il primo <?php echo e($cfg['singular']); ?>
          </a>
        </div>
      <?php else: ?>
        <form method="post" data-bulk-form>
          <?php echo csrfField(); ?>
          <input type="hidden" name="action" value="bulk">

          <div class="bulk-bar" data-bulk-bar>
            <strong data-bulk-count>0 elementi selezionati</strong>
            <div class="spacer"></div>
            <button type="submit" name="bulk" value="publish" class="btn btn-sm btn-success"><i class="fas fa-eye"></i> Pubblica</button>
            <button type="submit" name="bulk" value="unpublish" class="btn btn-sm btn-outline"><i class="fas fa-eye-slash"></i> Nascondi</button>
            <button type="submit" name="bulk" value="delete" class="btn btn-sm btn-danger"
                    data-confirm="Eliminare definitivamente gli elementi selezionati? L'operazione non è reversibile.">
              <i class="fas fa-trash"></i> Elimina
            </button>
          </div>

          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th style="width:36px"><input type="checkbox" data-check-all aria-label="Seleziona tutto"></th>
                  <?php foreach ($cfg['columns'] as $col): ?>
                  <th<?php echo !empty($col['hideSm']) ? ' class="hide-sm"' : ''; ?>><?php echo e($col['label']); ?></th>
                  <?php endforeach; ?>
                  <th style="width:1%"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $row): ?>
                <tr<?php echo isset($row['published']) && !$row['published'] ? ' class="row-muted"' : ''; ?>>
                  <td><input type="checkbox" name="ids[]" value="<?php echo (int)$row['id']; ?>" data-check
                             aria-label="Seleziona <?php echo e($row['title'] ?? ('#' . $row['id'])); ?>"></td>
                  <?php foreach ($cfg['columns'] as $col): ?>
                  <td<?php echo !empty($col['hideSm']) ? ' class="hide-sm"' : ''; ?>><?php echo $col['render']($row); ?></td>
                  <?php endforeach; ?>
                  <td class="nowrap">
                    <div class="btn-row">
                      <a href="<?php echo e($self); ?>?action=edit&id=<?php echo (int)$row['id']; ?>"
                         class="btn btn-sm btn-outline" title="Modifica"><i class="fas fa-pen"></i></a>
                      <?php if (!empty($cfg['publicUrl']) && (!isset($row['published']) || $row['published'])): ?>
                      <a href="<?php echo e($cfg['publicUrl']($row)); ?>" target="_blank"
                         class="btn btn-sm btn-outline" title="Vedi nel sito"><i class="fas fa-arrow-up-right-from-square"></i></a>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </form>
      <?php endif; ?>
    </div>
    <?php
}

/* ======================================================================
 * Modulo di inserimento / modifica
 * ==================================================================== */

function crudRenderForm(array $cfg, ?array $row, int $id, array $errors, string $self) {
    $val = function ($name, $default = '') use ($row) {
        if ($row === null) return $default;
        return $row[$name] ?? $default;
    };
    ?>
    <?php if ($errors): ?>
    <div class="alert alert-error">
      <i class="fas fa-circle-exclamation"></i>
      <span>
        <strong>Non è stato possibile salvare:</strong>
        <ul><?php foreach ($errors as $err): ?><li><?php echo e($err); ?></li><?php endforeach; ?></ul>
      </span>
    </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <?php echo csrfField(); ?>
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?php echo $id; ?>">

      <div class="panel">
        <div class="panel-body">
          <div class="form-grid">
            <?php foreach ($cfg['fields'] as $field): crudRenderField($field, $val, $cfg); endforeach; ?>
          </div>
        </div>
      </div>

      <div class="btn-row">
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-floppy-disk"></i> Salva</button>
        <a href="<?php echo e($self); ?>" class="btn btn-outline btn-lg">Annulla</a>
        <div style="margin-left:auto"></div>
        <?php if ($id): ?>
        <button type="submit" form="deleteForm" class="btn btn-danger"
                data-confirm="Eliminare definitivamente questo <?php echo e($cfg['singular']); ?>?">
          <i class="fas fa-trash"></i> Elimina
        </button>
        <?php endif; ?>
      </div>
    </form>

    <?php if ($id): ?>
    <form method="post" id="deleteForm" style="display:none">
      <?php echo csrfField(); ?>
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" value="<?php echo $id; ?>">
    </form>
    <?php endif; ?>
    <?php
}

function crudRenderField(array $field, callable $val, array $cfg) {
    $name = $field['name'];
    $type = $field['type'];
    $label = $field['label'];
    $required = !empty($field['required']);
    $full = !empty($field['full']) || in_array($type, ['textarea', 'image', 'section'], true);
    $current = $val($name, $field['default'] ?? '');

    if ($type === 'section') {
        echo '<h3 class="form-section-title">' . e($label) . '</h3>';
        return;
    }
    ?>
    <div class="form-group<?php echo $full ? ' full' : ''; ?>">
      <?php if ($type !== 'checkbox'): ?>
      <label class="form-label" for="f_<?php echo e($name); ?>">
        <?php echo e($label); ?><?php echo $required ? ' <span class="req">*</span>' : ''; ?>
      </label>
      <?php endif; ?>

      <?php if ($type === 'textarea'): ?>
        <textarea name="<?php echo e($name); ?>" id="f_<?php echo e($name); ?>" class="form-control"
                  rows="<?php echo (int)($field['rows'] ?? 5); ?>"
                  <?php echo !empty($field['placeholder']) ? 'placeholder="' . e($field['placeholder']) . '"' : ''; ?>><?php echo e($current); ?></textarea>

      <?php elseif ($type === 'select'): ?>
        <select name="<?php echo e($name); ?>" id="f_<?php echo e($name); ?>" class="form-control">
          <?php if (!empty($field['empty'])): ?><option value=""><?php echo e($field['empty']); ?></option><?php endif; ?>
          <?php
          $options = is_callable($field['options']) ? $field['options']() : $field['options'];
          foreach ($options as $optValue => $optLabel): ?>
          <option value="<?php echo e($optValue); ?>" <?php echo ((string)$current === (string)$optValue) ? 'selected' : ''; ?>>
            <?php echo e($optLabel); ?>
          </option>
          <?php endforeach; ?>
        </select>

      <?php elseif ($type === 'checkbox'): ?>
        <label class="form-check">
          <input type="checkbox" name="<?php echo e($name); ?>" id="f_<?php echo e($name); ?>" value="1"
                 <?php echo !empty($current) ? 'checked' : ''; ?>>
          <span><?php echo e($label); ?></span>
        </label>

      <?php elseif ($type === 'image'): ?>
        <div class="image-field">
          <div class="image-preview" id="preview_<?php echo e($name); ?>">
            <?php if (mediaExists($current)): ?>
              <img src="<?php echo e(mediaUrl($current)); ?>" alt="Immagine attuale">
            <?php else: ?>
              <i class="<?php echo e($cfg['icon']); ?>"></i>
            <?php endif; ?>
          </div>
          <div class="image-field-controls">
            <input type="hidden" name="<?php echo e($name); ?>_current" value="<?php echo e($current); ?>">
            <input type="file" name="<?php echo e($name); ?>" id="f_<?php echo e($name); ?>" class="form-control"
                   accept="image/jpeg,image/png,image/gif,image/webp"
                   data-image-input="#preview_<?php echo e($name); ?>">
            <p class="form-hint">JPG, PNG, GIF o WEBP · massimo 6 MB. Formato consigliato: orizzontale 4:3.</p>
            <?php if (mediaExists($current)): ?>
            <label class="form-check mt-2">
              <input type="checkbox" name="<?php echo e($name); ?>_remove" value="1">
              <span>Rimuovi l'immagine attuale</span>
            </label>
            <?php endif; ?>
          </div>
        </div>

      <?php else:
        $inputType = match ($type) {
            'number', 'price' => 'number',
            'date' => 'date',
            'time' => 'time',
            'email' => 'email',
            'url' => 'url',
            default => 'text',
        };
      ?>
        <input type="<?php echo $inputType; ?>" name="<?php echo e($name); ?>" id="f_<?php echo e($name); ?>"
               class="form-control" value="<?php echo e($current); ?>"
               <?php echo $type === 'price' ? 'step="0.01" min="0"' : ''; ?>
               <?php echo $type === 'number' ? 'min="0"' : ''; ?>
               <?php echo !empty($field['placeholder']) ? 'placeholder="' . e($field['placeholder']) . '"' : ''; ?>>
      <?php endif; ?>

      <?php if (!empty($field['hint'])): ?>
      <p class="form-hint"><?php echo $field['hint']; ?></p>
      <?php endif; ?>
    </div>
    <?php
}

/* ======================================================================
 * Pezzi riutilizzabili per le colonne dell'elenco
 * ==================================================================== */

/** Miniatura + titolo (+ sottotitolo). */
function crudColMedia(string $titleField, ?string $subField = null, string $icon = 'fas fa-image') {
    return function (array $row) use ($titleField, $subField, $icon) {
        $html = '<div class="cell-media">';
        $html .= mediaExists($row['image'] ?? '')
            ? '<img class="thumb" src="' . e(mediaUrl($row['image'])) . '" alt="">'
            : '<span class="thumb-empty"><i class="' . e($icon) . '"></i></span>';
        $html .= '<div><span class="title">' . e($row[$titleField] ?? '') . '</span>';
        if ($subField && !empty($row[$subField])) {
            $html .= '<small>' . e(truncate($row[$subField], 70)) . '</small>';
        }
        $html .= '</div></div>';
        return $html;
    };
}

/** Pillola pubblicato / bozza. */
function crudColPublished() {
    return function (array $row) {
        return !empty($row['published'])
            ? '<span class="pill pill-on"><i class="fas fa-eye"></i> Online</span>'
            : '<span class="pill pill-off"><i class="fas fa-eye-slash"></i> Bozza</span>';
    };
}

function crudColText(string $field, string $fallback = '—') {
    return fn(array $row) => trim((string)($row[$field] ?? '')) !== '' ? e($row[$field]) : $fallback;
}

function crudColPrice(string $field = 'price') {
    return fn(array $row) => e(formatPrice($row[$field] ?? null));
}
