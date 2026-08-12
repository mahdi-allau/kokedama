<?php
/**
 * Router principale
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

initDatabase();

$request = (string)parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Tolgo la sottocartella SOLO se è davvero il prefisso dell'indirizzo.
// (Un semplice str_replace romperebbe gli URL come /service/nome-slug.)
if (APP_BASE !== '' && str_starts_with($request, APP_BASE)) {
    $request = substr($request, strlen(APP_BASE));
}
$request = trim($request, '/');

// Default route
if ($request === '' || $request === 'index.php') {
    $request = 'home';
}

/**
 * Ripulisco l'indirizzo prima di usarlo per comporre un percorso di file.
 * Senza questo controllo un indirizzo come "../../qualcosa" potrebbe far
 * caricare file fuori dalla cartella pages/.
 */
$request = strtolower($request);
if (!preg_match('~^[a-z0-9][a-z0-9\-_]*(/[a-z0-9\-_]+)?$~', $request)) {
    $request = '404';
}

$pageFile = __DIR__ . '/pages/' . $request . '.php';

if (!str_contains($request, '/') && is_file($pageFile)) {
    require $pageFile;
} else {
    // Pagine di dettaglio: /service/slug, /project/slug, /event/slug
    if (preg_match('~^service/([a-z0-9\-_]+)$~', $request, $m)) {
        $_GET['slug'] = $m[1];
        require __DIR__ . '/pages/service.php';
    } elseif (preg_match('~^project/([a-z0-9\-_]+)$~', $request, $m)) {
        $_GET['slug'] = $m[1];
        require __DIR__ . '/pages/project.php';
    } elseif (preg_match('~^event/([a-z0-9\-_]+)$~', $request, $m)) {
        $_GET['slug'] = $m[1];
        require __DIR__ . '/pages/event.php';
    } else {
        http_response_code(404);
        $pageTitle = 'Pagina non trovata';
        $activePage = '';
        require __DIR__ . '/templates/header.php';
        ?>
        <section class="page-hero">
          <div class="container">
            <span class="eyebrow eyebrow--center">Errore 404</span>
            <h1 class="display-1">Qui non cresce nulla</h1>
            <p class="lead">La pagina che cerchi non esiste o è stata spostata. Riproviamo da un ramo che conosciamo.</p>
          </div>
        </section>
        <section class="section">
          <div class="container text-center">
            <div class="hero-buttons" style="justify-content:center">
              <a href="<?php echo APP_URL; ?>/" class="btn btn-kokedama btn-lg"><i class="fas fa-house"></i> Torna alla home</a>
              <a href="<?php echo APP_URL; ?>/services" class="btn btn-ghost btn-lg">Vedi i servizi</a>
              <a href="<?php echo APP_URL; ?>/contact" class="btn btn-ghost btn-lg">Contattaci</a>
            </div>
          </div>
        </section>
        <?php
        require __DIR__ . '/templates/footer.php';
    }
}
