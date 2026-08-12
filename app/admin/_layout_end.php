    </div><!-- /.admin-content -->
  </div><!-- /.admin-main -->
</div><!-- /.admin-shell -->

<script>
(function () {
  'use strict';

  /* Menu laterale su schermi piccoli */
  var toggle = document.getElementById('sidebarToggle');
  if (toggle) {
    toggle.addEventListener('click', function () {
      document.body.classList.toggle('nav-open');
    });
    document.addEventListener('click', function (ev) {
      if (!document.body.classList.contains('nav-open')) return;
      if (ev.target.closest('#sidebar') || ev.target.closest('#sidebarToggle')) return;
      document.body.classList.remove('nav-open');
    });
  }

  /* Selezione multipla nelle tabelle + barra azioni di gruppo */
  document.querySelectorAll('[data-bulk-form]').forEach(function (form) {
    var master = form.querySelector('[data-check-all]');
    var boxes = form.querySelectorAll('[data-check]');
    var bar = form.querySelector('[data-bulk-bar]');
    var counter = form.querySelector('[data-bulk-count]');
    if (!boxes.length) return;

    function refresh() {
      var n = form.querySelectorAll('[data-check]:checked').length;
      if (bar) bar.classList.toggle('show', n > 0);
      if (counter) counter.textContent = n === 1 ? '1 elemento selezionato' : n + ' elementi selezionati';
      if (master) {
        master.checked = n === boxes.length && n > 0;
        master.indeterminate = n > 0 && n < boxes.length;
      }
    }

    if (master) {
      master.addEventListener('change', function () {
        boxes.forEach(function (b) { b.checked = master.checked; });
        refresh();
      });
    }
    boxes.forEach(function (b) { b.addEventListener('change', refresh); });
    refresh();
  });

  /* Conferma per le azioni distruttive */
  document.addEventListener('click', function (ev) {
    var el = ev.target.closest('[data-confirm]');
    if (!el) return;
    if (!window.confirm(el.getAttribute('data-confirm'))) {
      ev.preventDefault();
      ev.stopPropagation();
    }
  });

  /* Anteprima immediata dell'immagine scelta */
  document.querySelectorAll('[data-image-input]').forEach(function (input) {
    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      var target = document.querySelector(input.getAttribute('data-image-input'));
      if (!file || !target) return;
      var url = URL.createObjectURL(file);
      target.innerHTML = '<img alt="Anteprima">';
      target.querySelector('img').src = url;
    });
  });

  /* Filtri rapidi per stato */
  document.querySelectorAll('[data-filter-group]').forEach(function (group) {
    group.addEventListener('click', function (ev) {
      var chip = ev.target.closest('.chip-filter');
      if (!chip) return;
      var value = chip.getAttribute('data-status');
      group.querySelectorAll('.chip-filter').forEach(function (c) {
        c.classList.toggle('active', c === chip);
      });
      document.querySelectorAll('[data-row-status]').forEach(function (row) {
        row.style.display = (value === 'all' || row.getAttribute('data-row-status') === value) ? '' : 'none';
      });
    });
  });
})();
</script>
</body>
</html>
