<?php
require_once __DIR__ . '/_crud.php';

crudRun([
    'table'     => 'services',
    'page'      => 'services',
    'title'     => 'Servizi',
    'singular'  => 'servizio',
    'icon'      => 'fas fa-spa',
    'uploadDir' => 'services',
    'slugFrom'  => 'title',
    'order'     => 'ordering, id',
    'publicUrl' => fn($row) => APP_URL . '/service/' . $row['slug'],
    'intro'     => 'I servizi compaiono nella pagina <strong>Servizi</strong> del sito. '
                 . 'Quelli contrassegnati come “in evidenza” appaiono anche in home page.',
    'emptyHint' => 'Aggiungi il primo servizio: comparirà subito nel sito, con prezzo e durata.',

    'columns' => [
        ['label' => 'Servizio', 'render' => crudColMedia('title', 'short_desc', 'fas fa-spa')],
        ['label' => 'Prezzo',   'render' => crudColPrice()],
        ['label' => 'Durata',   'hideSm' => true, 'render' => fn($r) => $r['duration'] ? (int)$r['duration'] . ' min' : '—'],
        ['label' => 'Home',     'hideSm' => true, 'render' => fn($r) => $r['featured'] ? '<i class="fas fa-star" style="color:#B96A4C"></i>' : '<span class="text-muted">—</span>'],
        ['label' => 'Stato',    'render' => crudColPublished()],
    ],

    'fields' => [
        ['name' => 'title', 'label' => 'Titolo', 'type' => 'text', 'required' => true, 'full' => true,
         'placeholder' => 'Es. Kokedama classica',
         'hint' => "L'indirizzo web viene generato in automatico dal titolo."],

        ['name' => 'short_desc', 'label' => 'Descrizione breve', 'type' => 'textarea', 'rows' => 2,
         'placeholder' => 'Una frase che compare nelle anteprime e nelle card.',
         'hint' => 'Consigliato: 100–130 caratteri.'],

        ['name' => 'description', 'label' => 'Descrizione completa', 'type' => 'textarea', 'rows' => 7,
         'hint' => 'Testo della pagina di dettaglio. Gli a capo vengono mantenuti.'],

        ['name' => 'image', 'label' => 'Foto del servizio', 'type' => 'image',
         'hint' => 'Se non carichi nulla, il sito mostra un elegante segnaposto verde.'],

        ['name' => 'section_meta', 'label' => 'Prezzo e organizzazione', 'type' => 'section'],

        ['name' => 'price', 'label' => 'Prezzo (€)', 'type' => 'price',
         'hint' => 'Lascia vuoto per mostrare «Su richiesta».'],

        ['name' => 'duration', 'label' => 'Durata (minuti)', 'type' => 'number',
         'placeholder' => '60'],

        ['name' => 'ordering', 'label' => 'Ordine', 'type' => 'number', 'default' => 0,
         'hint' => 'I numeri più bassi compaiono per primi.'],

        ['name' => 'featured', 'label' => 'Mostra in home page', 'type' => 'checkbox'],
        ['name' => 'published', 'label' => 'Visibile nel sito', 'type' => 'checkbox', 'default' => 1],
    ],
]);
