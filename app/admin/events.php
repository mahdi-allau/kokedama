<?php
require_once __DIR__ . '/_crud.php';

crudRun([
    'table'     => 'events',
    'page'      => 'events',
    'title'     => 'Eventi & workshop',
    'singular'  => 'evento',
    'icon'      => 'fas fa-calendar-day',
    'uploadDir' => 'events',
    'slugFrom'  => 'title',
    'order'     => 'event_date DESC, event_time',
    'publicUrl' => fn($row) => APP_URL . '/event/' . $row['slug'],
    'intro'     => 'Gli eventi con data <strong>da oggi in poi</strong> compaiono nella pagina Eventi; '
                 . 'quelli passati finiscono automaticamente in archivio. Non serve cancellarli.',
    'emptyHint' => 'Inserisci il primo workshop: comparirà nel calendario del sito e sarà prenotabile.',

    'columns' => [
        ['label' => 'Evento', 'render' => crudColMedia('title', null, 'fas fa-calendar-day')],
        ['label' => 'Quando', 'render' => function ($r) {
            $past = $r['event_date'] && strtotime($r['event_date']) < strtotime(date('Y-m-d'));
            $html = '<span class="title">' . e(formatDate($r['event_date'])) . '</span>';
            $html .= '<small>' . e(formatTime($r['event_time']));
            $html .= $past ? ' · concluso' : '';
            $html .= '</small>';
            return $html;
        }],
        ['label' => 'Tipo',   'hideSm' => true, 'render' => fn($r) => '<span class="pill pill-off">' . e(ucfirst($r['event_type'] ?: '—')) . '</span>'],
        ['label' => 'Posti',  'hideSm' => true, 'render' => function ($r) {
            $db = getDB();
            $q = $db->prepare("SELECT COALESCE(SUM(participants),0) FROM bookings WHERE event_id = ? AND status IN ('pending','confirmed')");
            $q->execute([$r['id']]);
            $taken = (int)$q->fetchColumn();
            $max = (int)$r['max_participants'];
            if ($max <= 0) return e((string)$taken) . ' iscritti';
            $left = max(0, $max - $taken);
            $color = $left === 0 ? '#C0392B' : ($left <= 3 ? '#B8860B' : '#2E7D46');
            return '<strong style="color:' . $color . '">' . $left . '</strong> <span class="text-muted">liberi su ' . $max . '</span>';
        }],
        ['label' => 'Prezzo', 'hideSm' => true, 'render' => crudColPrice()],
        ['label' => 'Stato',  'render' => crudColPublished()],
    ],

    'fields' => [
        ['name' => 'title', 'label' => 'Titolo', 'type' => 'text', 'required' => true, 'full' => true,
         'placeholder' => 'Es. Workshop kokedama base'],

        ['name' => 'description', 'label' => 'Descrizione', 'type' => 'textarea', 'rows' => 6,
         'hint' => 'Racconta cosa si fa, a chi è adatto e cosa si porta a casa.'],

        ['name' => 'image', 'label' => 'Foto dell\'evento', 'type' => 'image'],

        ['name' => 'section_when', 'label' => 'Data e luogo', 'type' => 'section'],

        ['name' => 'event_date', 'label' => 'Data', 'type' => 'date', 'required' => true],
        ['name' => 'event_type', 'label' => 'Tipo', 'type' => 'select', 'default' => 'workshop',
         'options' => ['workshop' => 'Workshop', 'evento' => 'Evento', 'corso' => 'Corso']],

        ['name' => 'event_time', 'label' => 'Orario di inizio', 'type' => 'time'],
        ['name' => 'end_time',   'label' => 'Orario di fine',   'type' => 'time'],

        ['name' => 'location', 'label' => 'Luogo', 'type' => 'text', 'full' => true,
         'placeholder' => 'Piazza della Repubblica 11, Ferrara',
         'hint' => 'Se lasci vuoto, non viene mostrato nulla nella scheda.'],

        ['name' => 'section_org', 'label' => 'Posti, prezzo e visibilità', 'type' => 'section'],

        ['name' => 'max_participants', 'label' => 'Posti disponibili', 'type' => 'number', 'default' => 8,
         'hint' => 'Il sito calcola da solo i posti rimasti e avvisa quando stanno finendo.'],

        ['name' => 'price', 'label' => 'Prezzo a persona (€)', 'type' => 'price',
         'hint' => 'Lascia vuoto per «Su richiesta».'],

        ['name' => 'ordering', 'label' => 'Ordine', 'type' => 'number', 'default' => 0],

        ['name' => 'featured', 'label' => 'Mostra in home page', 'type' => 'checkbox'],
        ['name' => 'published', 'label' => 'Visibile nel sito', 'type' => 'checkbox', 'default' => 1],
    ],
]);
