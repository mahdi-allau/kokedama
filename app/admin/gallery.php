<?php
require_once __DIR__ . '/_crud.php';

crudRun([
    'table'     => 'gallery',
    'page'      => 'gallery',
    'title'     => 'Galleria foto',
    'singular'  => 'foto',
    'icon'      => 'fas fa-images',
    'uploadDir' => 'gallery',
    'slugFrom'  => null,
    'order'     => 'ordering, id',
    'publicUrl' => null,
    'intro'     => 'Le foto sono la cosa che vende di più: una galleria piena e curata vale più di qualsiasi testo. '
                 . 'Assegnando una <strong>categoria</strong>, i visitatori possono filtrare le immagini.',
    'emptyHint' => 'Carica le prime foto delle tue creazioni e dei workshop.',

    'columns' => [
        ['label' => 'Foto',      'render' => crudColMedia('title', 'description', 'fas fa-image')],
        ['label' => 'Categoria', 'hideSm' => true, 'render' => fn($r) => '<span class="pill pill-off">' . e(ucfirst($r['category'] ?: 'general')) . '</span>'],
        ['label' => 'Collegata a', 'hideSm' => true, 'render' => function ($r) {
            if (!empty($r['project_id'])) {
                $q = getDB()->prepare("SELECT title FROM projects WHERE id = ?");
                $q->execute([$r['project_id']]);
                if ($t = $q->fetchColumn()) return '<i class="fas fa-seedling text-muted"></i> ' . e($t);
            }
            if (!empty($r['event_id'])) {
                $q = getDB()->prepare("SELECT title FROM events WHERE id = ?");
                $q->execute([$r['event_id']]);
                if ($t = $q->fetchColumn()) return '<i class="fas fa-calendar-day text-muted"></i> ' . e($t);
            }
            return '<span class="text-muted">—</span>';
        }],
        ['label' => 'Stato', 'render' => crudColPublished()],
    ],

    'fields' => [
        ['name' => 'image', 'label' => 'Immagine', 'type' => 'image', 'required' => true,
         'hint' => 'Obbligatoria. Le foto quadrate o orizzontali rendono meglio nella griglia.'],

        ['name' => 'title', 'label' => 'Titolo', 'type' => 'text', 'required' => true, 'full' => true,
         'placeholder' => 'Es. Kokedama con felce',
         'hint' => 'Compare sotto la foto quando ci si passa sopra e serve anche ai motori di ricerca.'],

        ['name' => 'description', 'label' => 'Descrizione', 'type' => 'textarea', 'rows' => 2],

        ['name' => 'section_org', 'label' => 'Organizzazione', 'type' => 'section'],

        ['name' => 'category', 'label' => 'Categoria', 'type' => 'select', 'default' => 'general',
         'options' => [
             'kokedama' => 'Kokedama',
             'workshop' => 'Workshop',
             'atelier'  => 'Atelier',
             'eventi'   => 'Eventi',
             'general'  => 'Altro',
         ],
         'hint' => 'Diventa un filtro nella pagina Galleria.'],

        ['name' => 'ordering', 'label' => 'Ordine', 'type' => 'number', 'default' => 0],

        ['name' => 'project_id', 'label' => 'Progetto collegato', 'type' => 'select',
         'empty' => 'Nessuno',
         'options' => function () {
             $out = [];
             foreach (getDB()->query("SELECT id, title FROM projects ORDER BY ordering, title")->fetchAll() as $p) {
                 $out[$p['id']] = $p['title'];
             }
             return $out;
         },
         'hint' => 'La foto comparirà anche nella pagina del progetto.'],

        ['name' => 'event_id', 'label' => 'Evento collegato', 'type' => 'select',
         'empty' => 'Nessuno',
         'options' => function () {
             $out = [];
             foreach (getDB()->query("SELECT id, title, event_date FROM events ORDER BY event_date DESC")->fetchAll() as $ev) {
                 $out[$ev['id']] = $ev['title'] . ' (' . formatDate($ev['event_date']) . ')';
             }
             return $out;
         }],

        ['name' => 'published', 'label' => 'Visibile nel sito', 'type' => 'checkbox', 'default' => 1],
    ],
]);
