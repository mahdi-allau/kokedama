<?php
/**
 * Funzioni utili — Kokedama & Sculture Naturali
 */

/* ---------------------------------------------------------------------------
 * Output sicuro
 * ------------------------------------------------------------------------- */

function e($str) {
    return htmlspecialchars((string)($str ?? ''), ENT_QUOTES, 'UTF-8');
}

/* ---------------------------------------------------------------------------
 * Impostazioni (lette una sola volta per richiesta)
 * ------------------------------------------------------------------------- */

function allSettings($refresh = false) {
    static $cache = null;
    if ($cache === null || $refresh) {
        $cache = [];
        try {
            foreach (getDB()->query("SELECT key, value FROM settings")->fetchAll() as $row) {
                $cache[$row['key']] = $row['value'];
            }
        } catch (Throwable $ex) {
            $cache = [];
        }
    }
    return $cache;
}

function getSetting($key, $default = '') {
    $all = allSettings();
    $val = $all[$key] ?? null;
    return ($val === null || $val === '') ? $default : $val;
}

function setSetting($key, $value) {
    $stmt = getDB()->prepare(
        "INSERT INTO settings (key, value) VALUES (?, ?)
         ON CONFLICT(key) DO UPDATE SET value = excluded.value"
    );
    $stmt->execute([$key, $value]);
    allSettings(true); // invalida la cache
}

/* ---------------------------------------------------------------------------
 * Testo
 * ------------------------------------------------------------------------- */

function slugify($text) {
    $text = trim((string)$text);
    if (function_exists('iconv')) {
        $conv = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($conv !== false) $text = $conv;
    }
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text !== '' ? $text : 'voce-' . substr(md5(microtime(true)), 0, 6);
}

/** Slug garantito univoco su una tabella (escludendo eventualmente un id). */
function uniqueSlug($table, $title, $excludeId = null) {
    $allowed = ['services', 'projects', 'events'];
    if (!in_array($table, $allowed, true)) {
        throw new InvalidArgumentException('Tabella non consentita: ' . $table);
    }
    $db = getDB();
    $base = slugify($title);
    $slug = $base;
    $i = 2;
    while (true) {
        $sql = "SELECT COUNT(*) FROM $table WHERE slug = ?";
        $params = [$slug];
        if ($excludeId) { $sql .= " AND id != ?"; $params[] = (int)$excludeId; }
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        if ((int)$stmt->fetchColumn() === 0) return $slug;
        $slug = $base . '-' . $i++;
    }
}

/** Troncamento sicuro con accenti italiani. */
function truncate($text, $length = 120) {
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string)$text)));
    if (mb_strlen($text, 'UTF-8') <= $length) return $text;
    return rtrim(mb_substr($text, 0, $length, 'UTF-8'), " ,.;:-") . '…';
}

/* ---------------------------------------------------------------------------
 * Formattazione
 * ------------------------------------------------------------------------- */

function formatPrice($price) {
    if ($price === null || $price === '') return 'Su richiesta';
    $p = (float)$price;
    $decimals = (fmod($p, 1) == 0.0) ? 0 : 2;
    return '€ ' . number_format($p, $decimals, ',', '.');
}

function formatDate($date, $long = false) {
    if (!$date) return '';
    $ts = strtotime($date);
    if (!$ts) return '';
    if (!$long) return date('d/m/Y', $ts);

    $giorni = ['Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato'];
    $mesi = ['','gennaio','febbraio','marzo','aprile','maggio','giugno',
             'luglio','agosto','settembre','ottobre','novembre','dicembre'];
    return $giorni[(int)date('w', $ts)] . ' ' . (int)date('j', $ts) . ' '
         . $mesi[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

function formatTime($time) {
    if (!$time) return '';
    return substr($time, 0, 5);
}

/** "Fra 3 giorni", "Domani", "Oggi" — utile nelle card evento. */
function relativeDay($date) {
    if (!$date) return '';
    $days = (int)floor((strtotime(date('Y-m-d', strtotime($date))) - strtotime(date('Y-m-d'))) / 86400);
    if ($days < 0)  return 'Concluso';
    if ($days === 0) return 'Oggi';
    if ($days === 1) return 'Domani';
    if ($days <= 7)  return "Fra $days giorni";
    return '';
}

/* ---------------------------------------------------------------------------
 * Immagini
 * ------------------------------------------------------------------------- */

/**
 * Trasforma il valore salvato in DB in un URL utilizzabile.
 * Accetta: URL assoluti, path già completi, oppure solo il nome file.
 */
function mediaUrl($image) {
    $image = trim((string)$image);
    if ($image === '') return '';
    if (preg_match('~^(https?:)?//~i', $image)) return $image;
    if (str_starts_with($image, '/')) return $image;
    if (str_contains($image, 'assets/uploads/')) {
        return APP_URL . '/' . ltrim($image, '/');
    }
    return UPLOADS_URL . '/' . ltrim($image, '/');
}

/** Il file immagine esiste davvero su disco? (evita immagini rotte) */
function mediaExists($image) {
    $image = trim((string)$image);
    if ($image === '') return false;
    if (preg_match('~^(https?:)?//~i', $image)) return true;
    $rel = str_contains($image, 'assets/uploads/')
        ? preg_replace('~^.*assets/uploads/~', '', $image)
        : ltrim($image, '/');
    return is_file(UPLOADS_DIR . '/' . $rel);
}

/**
 * Stampa l'immagine se disponibile, altrimenti un placeholder botanico.
 * $icon: classe Font Awesome usata nel placeholder.
 */
function renderMedia($image, $alt = '', $icon = 'fas fa-spa', $lazy = true) {
    if (mediaExists($image)) {
        printf(
            '<img src="%s" alt="%s"%s>',
            e(mediaUrl($image)),
            e($alt),
            $lazy ? ' loading="lazy" decoding="async"' : ''
        );
        return;
    }
    printf(
        '<span class="media-placeholder" role="img" aria-label="%s"><i class="%s" aria-hidden="true"></i></span>',
        e($alt !== '' ? $alt : 'Immagine non ancora disponibile'),
        e($icon)
    );
}

/* ---------------------------------------------------------------------------
 * Contatti rapidi
 * ------------------------------------------------------------------------- */

/** Numero ripulito per href="tel:" */
function telHref($number = null) {
    $number = $number ?? getSetting('business_phone', '0532 242503');
    return preg_replace('/[^\d+]/', '', $number);
}

/** Link WhatsApp con messaggio precompilato (vuoto se non configurato). */
function whatsappLink($message = null) {
    $raw = getSetting('business_whatsapp', '');
    if ($raw === '') return '';
    $num = preg_replace('/[^\d]/', '', $raw);
    if ($num === '') return '';
    if (!str_starts_with($num, '39') && strlen($num) <= 11) $num = '39' . $num;
    $message = $message ?? 'Ciao! Ho visto il sito e vorrei qualche informazione.';
    return 'https://wa.me/' . $num . '?text=' . rawurlencode($message);
}

/** Indirizzo su una riga, per Google Maps o il footer. */
function fullAddress() {
    return trim(
        getSetting('business_address', 'Piazza della Repubblica 11') . ', ' .
        getSetting('business_zip', '44121') . ' ' .
        getSetting('business_city', 'Ferrara')
    );
}

function mapsDirectionsUrl() {
    return 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode(fullAddress());
}

/**
 * Estrae il solo URL dall'embed della mappa.
 * Simona può incollare l'intero <iframe> copiato da Google: l'iframe lo
 * ricostruiamo noi, così nel sito non finisce mai HTML arbitrario.
 * Restituisce '' se il valore non è un embed di mappa riconosciuto.
 */
function mapsEmbedSrc() {
    $raw = trim(getSetting('business_maps_embed', ''));
    if ($raw === '') return '';

    if (preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $raw, $m)) {
        $raw = $m[1];
    }
    $raw = html_entity_decode($raw, ENT_QUOTES, 'UTF-8');

    if (!filter_var($raw, FILTER_VALIDATE_URL)) return '';
    $host = strtolower((string)parse_url($raw, PHP_URL_HOST));
    $ok = ['www.google.com', 'google.com', 'maps.google.com', 'www.openstreetmap.org', 'openstreetmap.org'];
    foreach ($ok as $allowed) {
        if ($host === $allowed || str_ends_with($host, '.' . $allowed)) return $raw;
    }
    return '';
}

/**
 * Orari di apertura salvati come "Lun-Ven|9:30 - 13:00 / 15:30 - 19:30"
 * (una riga per giorno). Restituisce un array [etichetta, orario].
 */
function openingHours() {
    $raw = getSetting('business_hours', '');
    if (trim($raw) === '') return [];
    $out = [];
    foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $parts = explode('|', $line, 2);
        $out[] = [
            'label' => trim($parts[0]),
            'value' => trim($parts[1] ?? ''),
        ];
    }
    return $out;
}

/** Nome italiano del giorno corrente, minuscolo (es. "martedì"). */
function todayName() {
    $giorni = ['domenica','lunedì','martedì','mercoledì','giovedì','venerdì','sabato'];
    return $giorni[(int)date('w')];
}

/**
 * Riga di orario corrispondente a oggi.
 * Riconosce sia i giorni singoli ("Martedì") sia gli intervalli ("Martedì - Venerdì").
 */
function todayHours() {
    $ordine = ['lunedì','martedì','mercoledì','giovedì','venerdì','sabato','domenica'];
    $oggi = array_search(todayName(), $ordine, true);

    foreach (openingHours() as $row) {
        $label = mb_strtolower($row['label'], 'UTF-8');

        // Intervallo: "martedì - venerdì"
        if (preg_match('/^\s*([\p{L}]+)\s*[-–]\s*([\p{L}]+)\s*$/u', $label, $m)) {
            $from = array_search($m[1], $ordine, true);
            $to   = array_search($m[2], $ordine, true);
            if ($from !== false && $to !== false && $oggi !== false) {
                $inRange = $from <= $to
                    ? ($oggi >= $from && $oggi <= $to)
                    : ($oggi >= $from || $oggi <= $to); // intervallo a cavallo della settimana
                if ($inRange) return $row;
            }
            continue;
        }

        // Giorno singolo
        if (str_contains($label, todayName())) return $row;
    }
    return null;
}

/* ---------------------------------------------------------------------------
 * Sicurezza
 * ------------------------------------------------------------------------- */

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function csrfVerify() {
    $sent = $_POST['csrf_token'] ?? '';
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $sent);
}

/** Blocca la richiesta se il token non è valido. */
function requireCsrf() {
    if (!csrfVerify()) {
        http_response_code(419);
        exit('Sessione scaduta. Torna indietro, ricarica la pagina e riprova.');
    }
}

/** Anti-spam elementare: campo nascosto che un umano non compila mai. */
function honeypotField() {
    return '<div style="position:absolute;left:-9999px;" aria-hidden="true">'
         . '<label>Non compilare<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>';
}

function honeypotTriggered() {
    return !empty($_POST['website']);
}

/* ---------------------------------------------------------------------------
 * Messaggi flash
 * ------------------------------------------------------------------------- */

function flash($message, $type = 'success') {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function takeFlash() {
    if (empty($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

/* ---------------------------------------------------------------------------
 * Stato (badge admin)
 * ------------------------------------------------------------------------- */

function statusLabel($status) {
    $map = [
        'pending'   => 'In attesa',
        'confirmed' => 'Confermata',
        'rejected'  => 'Rifiutata',
        'cancelled' => 'Annullata',
        'completed' => 'Completata',
        'new'       => 'Nuovo',
        'read'      => 'Letto',
        'replied'   => 'Risposto',
        'archived'  => 'Archiviato',
    ];
    return $map[$status] ?? ucfirst((string)$status);
}

function statusBadge($status) {
    return '<span class="pill pill-' . e($status) . '">' . e(statusLabel($status)) . '</span>';
}

/* ---------------------------------------------------------------------------
 * Email
 * ------------------------------------------------------------------------- */

function sendEmail($to, $subject, $body) {
    $to = trim((string)$to);
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

    $from = getSetting('business_email', 'anda22@gmail.com');
    $headers  = "From: " . getSetting('business_name', 'Kokedama') . " <$from>\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";

    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    return @mail($to, $encodedSubject, $body, $headers);
}

/** Firma condivisa in fondo alle email automatiche. */
function emailSignature() {
    return "\n\n—\n" . getSetting('business_name', 'Kokedama & Sculture Naturali') . "\n"
         . fullAddress() . "\n"
         . getSetting('business_phone', '0532 242503') . "\n"
         . APP_URL . "\n";
}

/* ---------------------------------------------------------------------------
 * Upload immagini
 * ------------------------------------------------------------------------- */

/**
 * Carica un'immagine in assets/uploads/{$subdir}.
 * Restituisce il path relativo ("servizi/abc.jpg") o null.
 * Gli errori leggibili finiscono in $error.
 */
function uploadImage($file, $subdir, &$error = null) {
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Caricamento non riuscito (codice ' . $file['error'] . '). '
               . 'Se il file è molto grande, prova a ridurlo.';
        return null;
    }

    $maxBytes = 6 * 1024 * 1024; // 6 MB
    if ($file['size'] > $maxBytes) {
        $error = 'Immagine troppo pesante (max 6 MB). Ridimensionala e riprova.';
        return null;
    }

    // Verifica reale del contenuto, non del solo header inviato dal browser
    $info = @getimagesize($file['tmp_name']);
    $allowed = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_GIF  => 'gif',
        IMAGETYPE_WEBP => 'webp',
    ];
    if (!$info || !isset($allowed[$info[2]])) {
        $error = 'Formato non supportato. Usa JPG, PNG, GIF o WEBP.';
        return null;
    }

    $ext = $allowed[$info[2]];
    $filename = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
    $destDir = rtrim(UPLOADS_DIR, '/') . '/' . trim($subdir, '/');

    if (!is_dir($destDir) && !@mkdir($destDir, 0775, true) && !is_dir($destDir)) {
        $error = 'Impossibile creare la cartella di destinazione: ' . $destDir;
        return null;
    }

    if (!@move_uploaded_file($file['tmp_name'], $destDir . '/' . $filename)) {
        $error = 'Impossibile salvare il file. Controlla i permessi di assets/uploads.';
        return null;
    }

    return trim($subdir, '/') . '/' . $filename;
}

/** Rimuove un'immagine caricata (ignora URL esterni). */
function deleteUpload($image) {
    $image = trim((string)$image);
    if ($image === '' || preg_match('~^(https?:)?//~i', $image)) return;
    $rel = str_contains($image, 'assets/uploads/')
        ? preg_replace('~^.*assets/uploads/~', '', $image)
        : ltrim($image, '/');
    $path = realpath(UPLOADS_DIR . '/' . $rel);
    $base = realpath(UPLOADS_DIR);
    if ($path && $base && str_starts_with($path, $base) && is_file($path)) {
        @unlink($path);
    }
}

/* Compatibilità con il vecchio nome */
function uploadFile($file, $subdir) {
    return uploadImage($file, $subdir);
}
