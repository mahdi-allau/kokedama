<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
initDatabase();

// Già dentro? Vai direttamente al pannello.
if (isAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfVerify()) {
        $error = 'Sessione scaduta. Riprova.';
    } elseif (loginThrottled($wait)) {
        $error = "Troppi tentativi falliti. Riprova fra $wait secondi.";
    } elseif (adminLogin($_POST['username'] ?? '', $_POST['password'] ?? '')) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Nome utente o password non corretti.';
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Accedi — Gestione Kokedama</title>
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%F0%9F%8C%B1%3C/text%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?php echo APP_BASE; ?>/assets/css/admin.css?v=3">
</head>
<body class="login-page">
  <div class="login-box">
    <div class="mark"><i class="fas fa-leaf"></i></div>
    <h1>Gestione Kokedama</h1>
    <p class="sub">Accedi per gestire prenotazioni, contenuti e impostazioni.</p>

    <?php if ($error): ?>
    <div class="alert alert-error" role="alert">
      <i class="fas fa-circle-exclamation"></i>
      <span><?php echo e($error); ?></span>
    </div>
    <?php endif; ?>

    <form method="post">
      <?php echo csrfField(); ?>
      <div class="form-group">
        <label class="form-label" for="username">Nome utente</label>
        <input type="text" name="username" id="username" class="form-control" required autofocus autocomplete="username">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="fas fa-right-to-bracket"></i> Accedi</button>
    </form>

    <p class="login-back">
      <a href="<?php echo APP_URL; ?>/"><i class="fas fa-arrow-left"></i> Torna al sito</a>
    </p>
  </div>
</body>
</html>
