<?php
/**
 * Database SQLite - Kokedama App
 */

require_once __DIR__ . '/../config.php';

function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->exec("PRAGMA foreign_keys = ON");
    }
    return $db;
}

function initDatabase() {
    $db = getDB();
    
    // Settings
    $db->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT
    )");
    
    // Services
    $db->exec("CREATE TABLE IF NOT EXISTS services (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        description TEXT,
        short_desc TEXT,
        price REAL,
        duration INTEGER,
        image TEXT,
        published INTEGER DEFAULT 1,
        featured INTEGER DEFAULT 0,
        ordering INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Projects
    $db->exec("CREATE TABLE IF NOT EXISTS projects (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        description TEXT,
        materials TEXT,
        plant_type TEXT,
        image TEXT,
        service_id INTEGER,
        published INTEGER DEFAULT 1,
        featured INTEGER DEFAULT 0,
        ordering INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Gallery
    $db->exec("CREATE TABLE IF NOT EXISTS gallery (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        image TEXT NOT NULL,
        category TEXT DEFAULT 'general',
        description TEXT,
        project_id INTEGER,
        event_id INTEGER,
        published INTEGER DEFAULT 1,
        ordering INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Events
    $db->exec("CREATE TABLE IF NOT EXISTS events (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        description TEXT,
        event_type TEXT DEFAULT 'workshop',
        event_date TEXT,
        event_time TEXT,
        end_time TEXT,
        location TEXT,
        max_participants INTEGER DEFAULT 10,
        price REAL,
        image TEXT,
        published INTEGER DEFAULT 1,
        featured INTEGER DEFAULT 0,
        ordering INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Bookings
    $db->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        booking_type TEXT NOT NULL,
        service_id INTEGER,
        event_id INTEGER,
        booking_date TEXT,
        booking_time TEXT,
        participants INTEGER DEFAULT 1,
        first_name TEXT NOT NULL,
        last_name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT,
        notes TEXT,
        status TEXT DEFAULT 'pending',
        admin_notes TEXT,
        ip_address TEXT,
        consent_gdpr INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        modified_at TEXT
    )");
    
    // Messages
    $db->exec("CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT,
        subject TEXT,
        message TEXT NOT NULL,
        status TEXT DEFAULT 'new',
        ip_address TEXT,
        consent_gdpr INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        read_at TEXT,
        replied_at TEXT
    )");
    
    // Impostazioni predefinite: sempre allineate, anche su database già esistenti.
    // INSERT OR IGNORE non tocca i valori che Simona ha già modificato.
    seedSettings($db);

    // Dati di esempio solo al primo avvio
    $stmt = $db->query("SELECT COUNT(*) FROM services");
    if ($stmt->fetchColumn() == 0) {
        insertSampleData($db);
    }
}

/**
 * Valori predefiniti delle impostazioni.
 * Aggiungere qui una chiave la rende disponibile anche ai database esistenti.
 */
function seedSettings($db) {
    $settings = [
        // Intestazione del sito (logo e nome mostrati in alto a sinistra)
        'brand_name'         => 'Kokedama',
        'brand_subtitle'     => 'Sculture Naturali',
        'brand_logo'         => '',

        // Dati attività
        'business_name'      => 'Kokedama & Sculture Naturali',
        'business_tagline'   => 'Arte botanica e sculture vegetali a Ferrara',
        'business_address'   => 'Piazza della Repubblica 11',
        'business_city'      => 'Ferrara',
        'business_zip'       => '44121',
        'business_phone'     => '0532 242503',
        'business_whatsapp'  => '',
        'business_email'     => 'anda22@gmail.com',
        'business_facebook'  => 'https://www.facebook.com/kokedamasculturenaturali/',
        'business_instagram' => '',
        'business_maps_embed' => '',
        'business_hours'     => "Lunedì|Chiuso\nMartedì - Venerdì|9:30 - 13:00 / 15:30 - 19:30\nSabato|9:30 - 19:30\nDomenica|Su appuntamento",

        // Contenuti modificabili senza toccare il codice
        'home_hero_title'    => 'Sculture naturali fatte a mano',
        'home_hero_subtitle' => 'Kokedama, composizioni vegetali e workshop creativi nel cuore di Ferrara. Ogni creazione è un pezzo unico, nato dalle mani e dalla terra.',
        'home_hero_image'    => '',
        'about_intro'        => "Kokedama & Sculture Naturali nasce dall'incontro tra la tradizione giapponese dell'arte botanica e la creatività italiana. Siamo un laboratorio artigianale con sede a Ferrara, specializzato nella creazione di kokedama, composizioni vegetali e terrari.",

        // Prenotazioni
        'booking_notice_days' => '2',
        'booking_intro'       => 'Compila il modulo: ti rispondiamo entro 24 ore con la conferma e tutti i dettagli. La richiesta non è vincolante.',

        // SEO
        'seo_description'    => 'Kokedama artigianali, composizioni vegetali e workshop creativi a Ferrara. Creazioni uniche fatte a mano e laboratori per principianti.',

        // Legale
        'gdpr_consent_text'  => "Ho letto e accetto l'informativa sulla privacy e il trattamento dei miei dati personali.",
        'privacy_owner'      => 'Kokedama & Sculture Naturali',
    ];
    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)");
    foreach ($settings as $k => $v) {
        $stmt->execute([$k, $v]);
    }
}

function insertSampleData($db) {

    // Services
    $db->exec("INSERT INTO services (title, slug, description, short_desc, price, duration, published, featured, ordering) VALUES
        ('Kokedama Classica', 'kokedama-classica', 'Una palla di muschio con una pianta scelta.', 'Creazione artigianale di kokedama con pianta a scelta', 35.00, 60, 1, 1, 1),
        ('Kokedama Sospesa', 'kokedama-sospesa', 'Kokedama pensata per essere appesa.', 'Kokedama da appendere per creare un giardino sospeso', 45.00, 90, 1, 1, 2),
        ('Laboratorio Kokedama', 'laboratorio-kokedama', 'Workshop pratico per imparare l''arte del kokedama.', 'Workshop pratico per imparare l''arte del kokedama', 55.00, 150, 1, 1, 3),
        ('Composizione Personalizzata', 'composizione-personalizzata', 'Composizioni su misura per eventi e arredamenti.', 'Composizioni vegetali personalizzate su richiesta', NULL, NULL, 1, 0, 4)
    ");
    
    // Projects
    $db->exec("INSERT INTO projects (title, slug, description, materials, plant_type, published, featured, ordering) VALUES
        ('Kokedama Ficus Ginseng', 'kokedama-ficus-ginseng', 'Realizzazione con Ficus Ginseng.', 'Muschio vivo, filo di jute, terriccio', 'Ficus Ginseng', 1, 1, 1),
        ('Giardino Sospeso', 'giardino-sospeso', 'Composizione di tre kokedame sospese.', 'Muschio, spago naturale, ghiaia', 'Pothos, Felce, Filodendro', 1, 1, 2),
        ('Centrotavola Evento', 'centrotavola-evento', 'Centrotavola per matrimonio rustico-naturale.', 'Muschio, tessuto naturale, base legno', 'Piante grasse assortite', 1, 0, 3)
    ");
    
    // Events
    $date1 = date('Y-m-d', strtotime('+14 days'));
    $date2 = date('Y-m-d', strtotime('+21 days'));
    $date3 = date('Y-m-d', strtotime('+30 days'));
    $db->exec("INSERT INTO events (title, slug, description, event_type, event_date, event_time, end_time, location, max_participants, price, published, featured, ordering) VALUES
        ('Workshop Kokedama Base', 'workshop-kokedama-base', 'Workshop introduttivo.', 'workshop', '$date1', '15:00', '17:30', 'Piazza della Repubblica 11, Ferrara', 8, 55.00, 1, 1, 1),
        ('Kokedama e Aperitivo', 'kokedama-e-aperitivo', 'Serata creativa con aperitivo bio.', 'evento', '$date2', '18:30', '21:00', 'Piazza della Repubblica 11, Ferrara', 12, 45.00, 1, 1, 2),
        ('Corso Avanzato Terrari', 'corso-avanzato-terrari', 'Corso di due incontri.', 'corso', '$date3', '16:00', '19:00', 'Piazza della Repubblica 11, Ferrara', 6, 90.00, 1, 0, 3)
    ");
    
    // Gallery
    $db->exec("INSERT INTO gallery (title, image, category, description, published, ordering) VALUES
        ('Kokedama Classica Verde', 'gallery-1.jpg', 'kokedama', 'Kokedama con felce', 1, 1),
        ('Kokedama Sospesa', 'gallery-2.jpg', 'kokedama', 'Kokedama da appendere', 1, 2),
        ('Workshop in corso', 'gallery-3.jpg', 'workshop', 'Momenti del laboratorio', 1, 3),
        ('Dettaglio muschio', 'gallery-4.jpg', 'general', 'Dettaglio sfera di muschio', 1, 4),
        ('Atelier', 'gallery-5.jpg', 'atelier', 'Il nostro atelier a Ferrara', 1, 5)
    ");
}
