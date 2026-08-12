<?php
/**
 * Autenticazione dell'area riservata.
 *
 * La password iniziale è definita in config.php (ADMIN_PASS_DEFAULT).
 * Al primo accesso viene salvato nel database il suo hash: da quel momento
 * Simona può cambiarla dal pannello senza toccare alcun file.
 */

function isAdmin() {
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . APP_URL . '/admin/');
        exit;
    }
    // Disconnessione automatica dopo 8 ore di inattività
    $limit = 8 * 3600;
    if (!empty($_SESSION['admin_last_seen']) && (time() - $_SESSION['admin_last_seen']) > $limit) {
        adminLogout();
        header('Location: ' . APP_URL . '/admin/');
        exit;
    }
    $_SESSION['admin_last_seen'] = time();
}

/** Hash attualmente valido: quello salvato, altrimenti quello di default. */
function adminPasswordHash() {
    $stored = getSetting('admin_pass_hash', '');
    if ($stored !== '') return $stored;

    // Prima installazione: genera e memorizza l'hash della password iniziale
    $hash = password_hash(ADMIN_PASS_DEFAULT, PASSWORD_DEFAULT);
    setSetting('admin_pass_hash', $hash);
    return $hash;
}

function adminLogin($username, $password) {
    $userOk = hash_equals(ADMIN_USER, (string)$username);
    $passOk = password_verify((string)$password, adminPasswordHash());

    // Verifichiamo sempre entrambi, così il tempo di risposta non rivela
    // se a essere sbagliato è il nome utente o la password.
    if (!$userOk || !$passOk) {
        loginRecordFailure();
        return false;
    }

    session_regenerate_id(true);   // difesa contro il session fixation
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_last_seen'] = time();
    unset($_SESSION['login_failures'], $_SESSION['login_blocked_until']);
    return true;
}

/** Cambia la password dell'area riservata. Restituisce '' oppure l'errore. */
function adminChangePassword($current, $new, $confirm) {
    if (!password_verify((string)$current, adminPasswordHash())) {
        return 'La password attuale non è corretta.';
    }
    if (strlen((string)$new) < 8) {
        return 'La nuova password deve avere almeno 8 caratteri.';
    }
    if ($new !== $confirm) {
        return 'Le due nuove password non coincidono.';
    }
    setSetting('admin_pass_hash', password_hash((string)$new, PASSWORD_DEFAULT));
    return '';
}

function adminLogout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/* ---------------------------------------------------------------------------
 * Freno ai tentativi ripetuti (protezione minima contro chi prova a indovinare)
 * ------------------------------------------------------------------------- */

function loginRecordFailure() {
    $_SESSION['login_failures'] = ($_SESSION['login_failures'] ?? 0) + 1;
    if ($_SESSION['login_failures'] >= 5) {
        $_SESSION['login_blocked_until'] = time() + 60;
        $_SESSION['login_failures'] = 0;
    }
}

/** true se i tentativi sono momentaneamente bloccati; $wait = secondi mancanti. */
function loginThrottled(&$wait = 0) {
    $until = $_SESSION['login_blocked_until'] ?? 0;
    if ($until > time()) {
        $wait = $until - time();
        return true;
    }
    return false;
}
