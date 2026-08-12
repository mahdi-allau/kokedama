<?php
/**
 * Kokedama Sculture Naturali - Configurazione
 */

define('APP_ROOT', __DIR__);
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

/**
 * Indirizzo base del sito.
 *
 * Va calcolato sulla posizione della cartella "app", NON su quella dello
 * script in esecuzione: le pagine dell'area riservata stanno in /admin, e
 * usare dirname(SCRIPT_NAME) farebbe puntare APP_URL a .../admin, rompendo
 * fogli di stile e collegamenti dell'intero pannello.
 */
$appDir  = str_replace('\\', '/', (string)realpath(__DIR__));
$docRoot = str_replace('\\', '/', (string)realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));

if ($docRoot !== '' && $appDir !== '' && str_starts_with($appDir . '/', $docRoot . '/')) {
    // Caso normale: XAMPP, hosting condiviso, server PHP integrato
    $basePath = rtrim(substr($appDir, strlen($docRoot)), '/');
} else {
    // Ripiego: ricavo il percorso dallo script, togliendo /admin se presente
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    if (substr($basePath, -6) === '/admin') {
        $basePath = substr($basePath, 0, -6);
    }
}
if ($basePath === '/' ) $basePath = '';

define('APP_BASE', $basePath);          // '' oppure '/sottocartella'
define('APP_URL', $protocol . '://' . $host . $basePath);
// Se qualcosa non tornasse (proxy, alias Apache particolari), imposta a mano:
// define('APP_URL', 'https://www.iltuodominio.it');

define('DB_PATH', APP_ROOT . '/database.sqlite');
define('UPLOADS_DIR', APP_ROOT . '/assets/uploads');
define('UPLOADS_URL', APP_URL . '/assets/uploads');

// Credenziali dell'area riservata.
// ADMIN_PASS_DEFAULT vale SOLO al primo accesso: subito dopo, l'hash viene
// salvato nel database e la password si cambia dal pannello (Impostazioni).
define('ADMIN_USER', 'admin');
define('ADMIN_PASS_DEFAULT', 'kokedama2026');

// I dati dell'attività (indirizzo, telefono, orari, social) NON stanno qui:
// si modificano dal pannello, in Impostazioni. Vedi includes/db.php → seedSettings().

/**
 * Modalità sviluppo.
 * In locale mostra gli errori a schermo; online li nasconde ai visitatori
 * (restano comunque nel log di PHP del server).
 * Metti false prima di pubblicare il sito.
 */
define('APP_DEBUG', in_array($host, ['localhost', '127.0.0.1'], true) || str_starts_with($host, 'localhost:'));

error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('display_startup_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');

// Sessione: cookie non leggibile da JavaScript e non inviato a siti terzi
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'secure'   => $protocol === 'https',
        'samesite' => 'Lax',
    ]);
    session_start();
}

date_default_timezone_set('Europe/Rome');
