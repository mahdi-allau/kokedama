import sqlite3
import os

os.chdir(r'C:\Users\mahdi\OneDrive - unife.it\Documenti\Kimi\Workspaces\kokedma_simona\app')

db = sqlite3.connect('database.sqlite')
db.execute("PRAGMA foreign_keys = ON")

# Settings
db.execute("""CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
)""")

# Services
db.execute("""CREATE TABLE IF NOT EXISTS services (
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
)""")

# Projects
db.execute("""CREATE TABLE IF NOT EXISTS projects (
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
)""")

# Gallery
db.execute("""CREATE TABLE IF NOT EXISTS gallery (
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
)""")

# Events
db.execute("""CREATE TABLE IF NOT EXISTS events (
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
)""")

# Bookings
db.execute("""CREATE TABLE IF NOT EXISTS bookings (
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
)""")

# Messages
db.execute("""CREATE TABLE IF NOT EXISTS messages (
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
)""")

# Insert sample data if empty
cur = db.execute("SELECT COUNT(*) FROM services")
if cur.fetchone()[0] == 0:
    settings = [
        ('business_name', 'Kokedama & Sculture Naturali'),
        ('business_address', 'Piazza della Repubblica 11'),
        ('business_city', 'Ferrara'),
        ('business_zip', '44121'),
        ('business_phone', '0532 242503'),
        ('business_email', 'anda22@gmail.com'),
        ('business_facebook', 'https://www.facebook.com/kokedamasculturenaturali/'),
        ('business_instagram', ''),
        ('gdpr_consent_text', "Ho letto e accetto l'informativa sulla privacy e il trattamento dei miei dati personali."),
    ]
    db.executemany("INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)", settings)

    import datetime
    d1 = (datetime.date.today() + datetime.timedelta(days=14)).isoformat()
    d2 = (datetime.date.today() + datetime.timedelta(days=21)).isoformat()
    d3 = (datetime.date.today() + datetime.timedelta(days=30)).isoformat()

    services = [
        ('Kokedama Classica', 'kokedama-classica', 'Una palla di muschio con una pianta scelta.', 'Creazione artigianale di kokedama con pianta a scelta', 35.00, 60, 1, 1, 1),
        ('Kokedama Sospesa', 'kokedama-sospesa', 'Kokedama pensata per essere appesa.', 'Kokedama da appendere per creare un giardino sospeso', 45.00, 90, 1, 1, 2),
        ('Laboratorio Kokedama', 'laboratorio-kokedama', 'Workshop pratico per imparare l arte del kokedama.', 'Workshop pratico per imparare l arte del kokedama', 55.00, 150, 1, 1, 3),
        ('Composizione Personalizzata', 'composizione-personalizzata', 'Composizioni su misura per eventi e arredamenti.', 'Composizioni vegetali personalizzate su richiesta', None, None, 1, 0, 4),
    ]
    db.executemany("INSERT INTO services (title, slug, description, short_desc, price, duration, published, featured, ordering) VALUES (?,?,?,?,?,?,?,?,?)", services)

    projects = [
        ('Kokedama Ficus Ginseng', 'kokedama-ficus-ginseng', 'Realizzazione con Ficus Ginseng.', 'Muschio vivo, filo di jute, terriccio', 'Ficus Ginseng', 1, 1, 1),
        ('Giardino Sospeso', 'giardino-sospeso', 'Composizione di tre kokedame sospese.', 'Muschio, spago naturale, ghiaia', 'Pothos, Felce, Filodendro', 1, 1, 2),
        ('Centrotavola Evento', 'centrotavola-evento', 'Centrotavola per matrimonio rustico-naturale.', 'Muschio, tessuto naturale, base legno', 'Piante grasse assortite', 1, 0, 3),
    ]
    db.executemany("INSERT INTO projects (title, slug, description, materials, plant_type, published, featured, ordering) VALUES (?,?,?,?,?,?,?,?)", projects)

    events = [
        ('Workshop Kokedama Base', 'workshop-kokedama-base', 'Workshop introduttivo.', 'workshop', d1, '15:00', '17:30', 'Piazza della Repubblica 11, Ferrara', 8, 55.00, 1, 1, 1),
        ('Kokedama e Aperitivo', 'kokedama-e-aperitivo', 'Serata creativa con aperitivo bio.', 'evento', d2, '18:30', '21:00', 'Piazza della Repubblica 11, Ferrara', 12, 45.00, 1, 1, 2),
        ('Corso Avanzato Terrari', 'corso-avanzato-terrari', 'Corso di due incontri.', 'corso', d3, '16:00', '19:00', 'Piazza della Repubblica 11, Ferrara', 6, 90.00, 1, 0, 3),
    ]
    db.executemany("INSERT INTO events (title, slug, description, event_type, event_date, event_time, end_time, location, max_participants, price, published, featured, ordering) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", events)

    gallery = [
        ('Kokedama Classica Verde', 'gallery-1.jpg', 'kokedama', 'Kokedama con felce', 1, 1),
        ('Kokedama Sospesa', 'gallery-2.jpg', 'kokedama', 'Kokedama da appendere', 1, 2),
        ('Workshop in corso', 'gallery-3.jpg', 'workshop', 'Momenti del laboratorio', 1, 3),
        ('Dettaglio muschio', 'gallery-4.jpg', 'general', 'Dettaglio sfera di muschio', 1, 4),
        ('Atelier', 'gallery-5.jpg', 'atelier', 'Il nostro atelier a Ferrara', 1, 5),
    ]
    db.executemany("INSERT INTO gallery (title, image, category, description, published, ordering) VALUES (?,?,?,?,?,?)", gallery)

db.commit()
db.close()
print('Database SQLite creato con successo!')
