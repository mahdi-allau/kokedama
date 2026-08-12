</main>

<?php
/**
 * Come header.php: le variabili qui usano il prefisso "tpl" perché il file
 * viene incluso dentro le pagine e ne condivide lo spazio dei nomi.
 */
$tplWa    = whatsappLink();
$tplHours = openingHours();
?>

<footer class="footer">
  <div class="footer-grid">
    <div>
      <h4><?php echo e(getSetting('business_name', 'Kokedama & Sculture Naturali')); ?></h4>
      <p><?php echo e(getSetting('business_tagline', 'Arte botanica e sculture vegetali a Ferrara')); ?>. Ogni creazione è un pezzo unico fatto a mano.</p>
      <?php if (getSetting('business_facebook') || getSetting('business_instagram') || $tplWa): ?>
      <div class="social-links">
        <?php if (getSetting('business_facebook')): ?><a href="<?php echo e(getSetting('business_facebook')); ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
        <?php if (getSetting('business_instagram')): ?><a href="<?php echo e(getSetting('business_instagram')); ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a><?php endif; ?>
        <?php if ($tplWa): ?><a href="<?php echo e($tplWa); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a><?php endif; ?>
      </div>
      <?php endif; ?>
    </div>

    <div>
      <h4>Naviga</h4>
      <ul class="footer-links">
        <li><a href="<?php echo APP_URL; ?>/about">Chi siamo</a></li>
        <li><a href="<?php echo APP_URL; ?>/services">Servizi</a></li>
        <li><a href="<?php echo APP_URL; ?>/projects">Progetti</a></li>
        <li><a href="<?php echo APP_URL; ?>/gallery">Galleria</a></li>
        <li><a href="<?php echo APP_URL; ?>/events">Eventi & workshop</a></li>
        <li><a href="<?php echo APP_URL; ?>/booking">Prenota</a></li>
      </ul>
    </div>

    <div>
      <h4>Contatti</h4>
      <p>
        <?php echo nl2br(e(getSetting('business_address', 'Piazza della Repubblica 11'))); ?><br>
        <?php echo e(getSetting('business_zip', '44121')); ?> <?php echo e(getSetting('business_city', 'Ferrara')); ?><br>
        <a href="tel:<?php echo e(telHref()); ?>"><?php echo e(getSetting('business_phone')); ?></a><br>
        <a href="mailto:<?php echo e(getSetting('business_email')); ?>"><?php echo e(getSetting('business_email')); ?></a>
      </p>
      <p style="margin-top:.75rem"><a href="<?php echo e(mapsDirectionsUrl()); ?>" target="_blank" rel="noopener" class="link-underline"><i class="fas fa-diamond-turn-right"></i> Come raggiungerci</a></p>
    </div>

    <?php if ($tplHours): ?>
    <div>
      <h4>Orari</h4>
      <ul class="footer-links" style="gap:.35rem">
        <?php foreach ($tplHours as $tplRow): ?>
        <li style="display:flex;justify-content:space-between;gap:1rem;">
          <span><?php echo e($tplRow['label']); ?></span>
          <span style="opacity:.75;text-align:right;"><?php echo e($tplRow['value']); ?></span>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>

  <div class="footer-bottom">
    <p>&copy; <?php echo date('Y'); ?> <?php echo e(getSetting('business_name')); ?> — <?php echo e(getSetting('business_city', 'Ferrara')); ?></p>
    <nav>
      <a href="<?php echo APP_URL; ?>/privacy">Privacy &amp; Cookie</a>
      <a href="<?php echo APP_URL; ?>/contact">Contatti</a>
      <a href="<?php echo APP_URL; ?>/admin/">Area riservata</a>
    </nav>
  </div>
</footer>

<!-- Barra azioni rapide: solo su smartphone, sempre a portata di pollice -->
<div class="mobile-bar">
  <a href="tel:<?php echo e(telHref()); ?>"><i class="fas fa-phone"></i>Chiama</a>
  <?php if ($tplWa): ?>
  <a href="<?php echo e($tplWa); ?>" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i>WhatsApp</a>
  <?php endif; ?>
  <a href="<?php echo e(mapsDirectionsUrl()); ?>" target="_blank" rel="noopener"><i class="fas fa-location-dot"></i>Mappa</a>
  <a href="<?php echo APP_URL; ?>/booking" class="primary"><i class="fas fa-calendar-check"></i>Prenota</a>
</div>

<div class="fab-stack">
  <?php if ($tplWa): ?>
  <a href="<?php echo e($tplWa); ?>" target="_blank" rel="noopener" class="fab fab-whatsapp" aria-label="Scrivici su WhatsApp"><i class="fab fa-whatsapp"></i></a>
  <?php endif; ?>
  <button class="fab fab-top" id="backToTop" aria-label="Torna su"><i class="fas fa-arrow-up"></i></button>
</div>

<div class="cookie-banner" id="cookieBanner" role="dialog" aria-label="Preferenze cookie">
  <p>Usiamo cookie tecnici necessari al funzionamento del sito e, solo con il tuo consenso, cookie statistici.
     <a href="<?php echo APP_URL; ?>/privacy">Leggi l'informativa</a>.</p>
  <div class="cookie-btns">
    <button class="btn btn-kokedama btn-sm" data-cookie="accepted">Accetta</button>
    <button class="btn btn-outline-kokedama btn-sm" data-cookie="rejected">Solo necessari</button>
  </div>
</div>

<figure class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Immagine ingrandita">
  <button class="lightbox-close" data-lightbox-close aria-label="Chiudi"><i class="fas fa-xmark"></i></button>
  <div>
    <img src="" alt="">
    <figcaption></figcaption>
  </div>
</figure>

<script>
(function () {
  'use strict';

  /* --- Menu mobile ------------------------------------------------------- */
  var toggle = document.getElementById('navToggle');
  var drawer = document.getElementById('mobileDrawer');

  function closeMenu() {
    document.body.classList.remove('menu-open');
    if (toggle) {
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', 'Apri il menu');
    }
  }

  if (toggle && drawer) {
    toggle.addEventListener('click', function () {
      var open = document.body.classList.toggle('menu-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Chiudi il menu' : 'Apri il menu');
    });
    drawer.addEventListener('click', function (ev) {
      if (ev.target.closest('a')) closeMenu();
    });
  }

  document.addEventListener('keydown', function (ev) {
    if (ev.key === 'Escape') {
      closeMenu();
      closeLightbox();
    }
  });

  /* --- Navbar compatta allo scroll + pulsante "torna su" ----------------- */
  var navbar = document.getElementById('navbar');
  var backTop = document.getElementById('backToTop');

  function onScroll() {
    var y = window.scrollY;
    if (navbar) navbar.classList.toggle('scrolled', y > 20);
    if (backTop) backTop.classList.toggle('show', y > 500);
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  if (backTop) {
    backTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* --- Comparsa progressiva dei blocchi allo scroll ---------------------- */
  var revealables = document.querySelectorAll('[data-reveal]');
  if (revealables.length) {
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            io.unobserve(entry.target);
          }
        });
      }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
      revealables.forEach(function (el) { io.observe(el); });
    } else {
      revealables.forEach(function (el) { el.classList.add('is-visible'); });
    }
  }

  /* --- Cookie banner ----------------------------------------------------- */
  var banner = document.getElementById('cookieBanner');
  if (banner) {
    var stored = null;
    try { stored = localStorage.getItem('koke_cookies'); } catch (err) { stored = 'accepted'; }
    if (!stored) {
      setTimeout(function () { banner.classList.add('show'); }, 1200);
    }
    banner.addEventListener('click', function (ev) {
      var btn = ev.target.closest('[data-cookie]');
      if (!btn) return;
      try { localStorage.setItem('koke_cookies', btn.getAttribute('data-cookie')); } catch (err) {}
      banner.classList.remove('show');
    });
  }

  /* --- Lightbox galleria ------------------------------------------------- */
  var lightbox = document.getElementById('lightbox');

  function closeLightbox() {
    if (lightbox) lightbox.classList.remove('is-open');
  }

  if (lightbox) {
    var lbImg = lightbox.querySelector('img');
    var lbCap = lightbox.querySelector('figcaption');

    document.addEventListener('click', function (ev) {
      var item = ev.target.closest('[data-lightbox]');
      if (item) {
        var src = item.getAttribute('data-lightbox');
        if (!src) return;              // placeholder senza foto: non aprire nulla
        ev.preventDefault();
        lbImg.src = src;
        lbImg.alt = item.getAttribute('data-caption') || '';
        lbCap.textContent = item.getAttribute('data-caption') || '';
        lightbox.classList.add('is-open');
        return;
      }
      if (ev.target.closest('[data-lightbox-close]') ||
          (lightbox.classList.contains('is-open') && ev.target === lightbox)) {
        closeLightbox();
      }
    });

    // Le figure della galleria non sono <button>: apriamole anche da tastiera.
    document.addEventListener('keydown', function (ev) {
      if (ev.key !== 'Enter' && ev.key !== ' ') return;
      var item = ev.target.closest('[data-lightbox]');
      if (!item || !item.getAttribute('data-lightbox')) return;
      ev.preventDefault();
      item.click();
    });
  }

  /* --- Filtri galleria --------------------------------------------------- */
  var filterBar = document.querySelector('[data-filter-bar]');
  if (filterBar) {
    filterBar.addEventListener('click', function (ev) {
      var chip = ev.target.closest('.filter-chip');
      if (!chip) return;
      var cat = chip.getAttribute('data-filter');
      filterBar.querySelectorAll('.filter-chip').forEach(function (c) {
        c.classList.toggle('is-active', c === chip);
      });
      document.querySelectorAll('[data-category]').forEach(function (el) {
        var show = cat === 'all' || el.getAttribute('data-category') === cat;
        el.style.display = show ? '' : 'none';
      });
    });
  }
})();
</script>
</body>
</html>
