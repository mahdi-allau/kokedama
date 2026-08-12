<?php
require_once __DIR__ . '/_crud.php';

crudRun([
    'table'     => 'projects',
    'page'      => 'projects',
    'title'     => 'Progetti',
    'singular'  => 'progetto',
    'icon'      => 'fas fa-seedling',
    'uploadDir' => 'projects',
    'slugFrom'  => 'title',
    'order'     => 'ordering, id',
    'publicUrl' => fn($row) => APP_URL . '/project/' . $row['slug'],
    'intro'     => 'Il portfolio serve a far vedere <strong>cosa sai fare</strong>: è quello che convince chi non ti conosce. '
                 . 'Collegando un progetto a un servizio, comparirà anche nella pagina di quel servizio.',
    'emptyHint' => 'Aggiungi una creazione già realizzata: bastano una foto e due righe di descrizione.',

    'columns' => [
        ['label' => 'Progetto', 'render' => crudColMedia('title', 'description', 'fas fa-seedling')],
        ['label' => 'Piante',   'hideSm' => true, 'render' => crudColText('plant_type')],
        ['label' => 'Servizio', 'hideSm' => true, 'render' => function ($r) {
            if (empty($r['service_id'])) return '<span class="text-muted">—</span>';
            $q = getDB()->prepare("SELECT title FROM services WHERE id = ?");
            $q->execute([$r['service_id']]);
            $t = $q->fetchColumn();
            return $t ? e($t) : '<span class="text-muted">—</span>';
        }],
        ['label' => 'Home',  'hideSm' => true, 'render' => fn($r) => $r['featured'] ? '<i class="fas fa-star" style="color:#B96A4C"></i>' : '<span class="text-muted">—</span>'],
        ['label' => 'Stato', 'render' => crudColPublished()],
    ],

    'fields' => [
        ['name' => 'title', 'label' => 'Titolo', 'type' => 'text', 'required' => true, 'full' => true,
         'placeholder' => 'Es. Kokedama Ficus Ginseng'],

        ['name' => 'description', 'label' => 'Descrizione', 'type' => 'textarea', 'rows' => 5,
         'hint' => 'Racconta la richiesta del cliente e come l\'hai risolta: è quello che convince.'],

        ['name' => 'image', 'label' => 'Foto principale', 'type' => 'image'],

        ['name' => 'section_detail', 'label' => 'Dettagli tecnici', 'type' => 'section'],

        ['name' => 'plant_type', 'label' => 'Piante utilizzate', 'type' => 'text',
         'placeholder' => 'Ficus Ginseng'],

        ['name' => 'materials', 'label' => 'Materiali', 'type' => 'text',
         'placeholder' => 'Muschio vivo, filo di jute, terriccio'],

        ['name' => 'service_id', 'label' => 'Servizio collegato', 'type' => 'select',
         'empty' => 'Nessuno',
         'options' => function () {
             $out = [];
             foreach (getDB()->query("SELECT id, title FROM services ORDER BY ordering, title")->fetchAll() as $s) {
                 $out[$s['id']] = $s['title'];
             }
             return $out;
         },
         'hint' => 'Facoltativo: mostra questo progetto come esempio nella pagina del servizio.'],

        ['name' => 'ordering', 'label' => 'Ordine', 'type' => 'number', 'default' => 0],

        ['name' => 'featured', 'label' => 'Metti in evidenza', 'type' => 'checkbox'],
        ['name' => 'published', 'label' => 'Visibile nel sito', 'type' => 'checkbox', 'default' => 1],
    ],
]);
