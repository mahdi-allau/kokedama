<?php
$pageTitle = 'Privacy & Cookie';
$activePage = '';
$metaDescription = 'Informativa privacy e cookie policy di ' . getSetting('business_name') . '.';
$titolare = getSetting('privacy_owner', getSetting('business_name'));
require __DIR__ . '/../templates/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Percorso">
      <a href="<?php echo APP_URL; ?>/">Home</a><i class="fas fa-chevron-right"></i><span>Privacy</span>
    </nav>
    <h1 class="display-2">Privacy &amp; Cookie</h1>
    <p class="lead">Come trattiamo i dati che ci lasci, in parole semplici.</p>
  </div>
</section>

<section class="section">
  <div class="container-narrow">

    <div class="alert alert-info mb-5">
      <i class="fas fa-circle-info"></i>
      <span>Questo testo è un modello di base conforme al GDPR (Reg. UE 2016/679).
        Prima della pubblicazione fallo verificare da un consulente e completa i dati mancanti
        (partita IVA, eventuale responsabile del trattamento, hosting utilizzato).</span>
    </div>

    <h2 class="display-4 mb-3">1. Titolare del trattamento</h2>
    <p class="mb-5">
      <strong><?php echo e($titolare); ?></strong><br>
      <?php echo e(fullAddress()); ?><br>
      Email: <a href="mailto:<?php echo e(getSetting('business_email')); ?>"><?php echo e(getSetting('business_email')); ?></a><br>
      Telefono: <a href="tel:<?php echo e(telHref()); ?>"><?php echo e(getSetting('business_phone')); ?></a>
    </p>

    <h2 class="display-4 mb-3">2. Quali dati raccogliamo</h2>
    <ul class="feature-list mb-5">
      <li><i class="fas fa-check"></i> <strong>Modulo contatti:</strong>&nbsp;nome, email, telefono (facoltativo), oggetto e messaggio.</li>
      <li><i class="fas fa-check"></i> <strong>Modulo prenotazioni:</strong>&nbsp;nome, cognome, email, telefono, data e orario richiesti, numero di partecipanti, eventuali note.</li>
      <li><i class="fas fa-check"></i> <strong>Dati tecnici:</strong>&nbsp;indirizzo IP, registrato per motivi di sicurezza e prevenzione degli abusi.</li>
    </ul>

    <h2 class="display-4 mb-3">3. Perché li usiamo</h2>
    <p class="mb-3">Trattiamo i dati esclusivamente per:</p>
    <ul class="feature-list mb-5">
      <li><i class="fas fa-check"></i> rispondere alle richieste di informazioni;</li>
      <li><i class="fas fa-check"></i> gestire e confermare le prenotazioni di servizi e workshop;</li>
      <li><i class="fas fa-check"></i> adempiere agli obblighi di legge (contabili e fiscali).</li>
    </ul>
    <p class="mb-5">La base giuridica è il consenso (art. 6.1.a GDPR) per le richieste e l'esecuzione di misure precontrattuali (art. 6.1.b) per le prenotazioni. <strong>Non usiamo i tuoi dati per marketing</strong> senza un consenso separato e non li cediamo a terzi.</p>

    <h2 class="display-4 mb-3">4. Per quanto tempo li conserviamo</h2>
    <p class="mb-5">I messaggi di contatto sono conservati per 24 mesi dall'ultimo scambio. I dati delle prenotazioni sono conservati per il tempo necessario a erogare il servizio e, se emessa fattura, per i 10 anni previsti dalla normativa fiscale.</p>

    <h2 class="display-4 mb-3">5. I tuoi diritti</h2>
    <p class="mb-3">Puoi in qualsiasi momento chiedere l'accesso, la rettifica, la cancellazione o la limitazione dei tuoi dati, opporti al trattamento e richiederne la portabilità (artt. 15–22 GDPR). Puoi anche revocare il consenso già prestato.</p>
    <p class="mb-5">Scrivi a <a href="mailto:<?php echo e(getSetting('business_email')); ?>" class="link-underline"><?php echo e(getSetting('business_email')); ?></a>: rispondiamo entro 30 giorni. Se ritieni che il trattamento violi il GDPR puoi presentare reclamo al <a href="https://www.garanteprivacy.it" target="_blank" rel="noopener" class="link-underline">Garante per la protezione dei dati personali</a>.</p>

    <h2 class="display-4 mb-3">6. Cookie</h2>
    <p class="mb-3">Questo sito usa un numero minimo di cookie:</p>
    <ul class="feature-list mb-3">
      <li><i class="fas fa-check"></i> <strong>Cookie tecnici di sessione:</strong>&nbsp;necessari al funzionamento dei moduli e dell'area riservata. Non richiedono consenso e si cancellano alla chiusura del browser.</li>
      <li><i class="fas fa-check"></i> <strong>Memoria locale del browser:</strong>&nbsp;usata solo per ricordare la tua scelta sul banner cookie.</li>
    </ul>
    <p class="mb-5">Se in futuro verranno attivati strumenti di statistica o di terze parti, saranno caricati soltanto dopo il tuo consenso esplicito. Puoi eliminare i cookie in qualsiasi momento dalle impostazioni del tuo browser.</p>

    <h2 class="display-4 mb-3">7. Servizi esterni</h2>
    <p class="mb-5">Alcune risorse (font tipografici, icone e la mappa) sono caricate da fornitori esterni, che per necessità tecnica ricevono il tuo indirizzo IP. Se è presente una mappa, questa viene fornita da Google secondo la <a href="https://policies.google.com/privacy" target="_blank" rel="noopener" class="link-underline">privacy policy di Google</a>.</p>

    <p class="text-muted" style="font-size:.9rem">Ultimo aggiornamento: <?php echo date('d/m/Y'); ?></p>

    <div class="text-center mt-5">
      <a href="<?php echo APP_URL; ?>/contact" class="btn btn-kokedama"><i class="fas fa-envelope"></i> Hai una domanda? Scrivici</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../templates/footer.php'; ?>
