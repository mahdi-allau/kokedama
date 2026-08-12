# Kokedama Sculture Naturali — Architettura Completa del Progetto

## 1. Riepilogo Brand

- **Nome:** Kokedama & Sculture Naturali
- **Sede:** Piazza della Repubblica 11, 44121 Ferrara (FE)
- **Contatti:** Tel. 0532 242503 — Email: anda22@gmail.com
- **Settore:** Arte botanica giapponese, kokedama, sculture vegetali, workshop
- **Tone of voice:** Naturale, artigianale, zen, elegante, rilassante
- **Colori suggeriti:** Verde muschio, terracotta, beige sabbia, bianco sporco, marrone legno

---

## 2. Tecnologie Scelte e Motivazioni

### 2.1 CMS Core: Joomla 5.x
| Aspetto | Motivazione |
|---------|-------------|
| **Joomla 5.x** | CMS PHP maturo, robusto, con ACL avanzata, gestione utenti nativa, multilingua, e framework per componenti custom molto strutturato. Perfetto per siti istituzionali con area admin complessa. |
| **PHP 8.2+** | Performance, type safety, attributi nativi, match expressions. Joomla 5 richiede PHP 8.1+, consigliato 8.2+. |
| **Bootstrap 5.3** | Già integrato in Joomla 5. Template custom basato su BS5 per responsività nativa senza dipendenze aggiuntive. |

### 2.2 Database: MySQL 8.0
| Aspetto | Motivazione |
|---------|-------------|
| **MySQL 8.0** | Joomla 5 richiede obbligatoriamente MySQL 8.0+ o MariaDB 10.4+. La scelta è vincolata dal CMS. MySQL 8 offre CTE, window functions, JSON support e performance superiori rispetto alle versioni precedenti. |
| **Motivo non-PostgreSQL** | Joomla ha supporto sperimentale PostgreSQL ma è sconsigliato per produzione; molte estensioni di terze parti non lo supportano. Per garantire compatibilità massima e stabilità, si usa MySQL 8. |

### 2.3 Web Server & Ambiente
| Aspetto | Motivazione |
|---------|-------------|
| **Apache 2.4+** con mod_rewrite | Compatibilità universale con hosting italiani. Joomla funziona meglio con .htaccess per URL SEF. |
| **HTTPS obbligatorio** | Certificato SSL per GDPR, SEO, e trust utente. |

### 2.4 Frontend
| Aspetto | Motivazione |
|---------|-------------|
| **Template custom Joomla** | Costruito da zero per aderire perfettamente al brand. Nessun template commerciale che richieda licenze o contenga bloat. |
| **SASS/SCSS** | Compilazione CSS modulare con variabili per i colori del brand. |
| **Vanilla JavaScript (ES6+)** | Nessuna dipendenza da framework JS pesanti (React/Vue non necessari per un sito corporate). Interattività leggera: lazy load immagini, swiper galleria, validazione form, animazioni CSS. |
| **Google Fonts** | Font eleganti (es. "Playfair Display" per titoli, "Lato" o "Inter" per body). |
| **Font Awesome 6** | Icone per servizi, contatti, UI. |

### 2.5 Backend & Componenti Custom
| Aspetto | Motivazione |
|---------|-------------|
| **Componente `com_kokedama`** | Unico componente Joomla custom che gestisce tutte le entità specifiche del business: Progetti, Galleria, Eventi, Servizi, Prenotazioni, Messaggi. Struttura MVC nativa Joomla. |
| **Plugin `plg_kokedama`** | Plugin di sistema per hook su eventi Joomla (invio email, notifiche, SEO). |
| **Moduli custom** | Moduli Joomla per homepage (hero, servizi in evidenza, prossimi eventi, ultimi progetti). |

### 2.6 Sicurezza
| Aspetto | Implementazione |
|---------|-----------------|
| **Autenticazione** | Sistema nativo Joomla (bcrypt hashing, sessioni sicure, 2FA opzionale). |
| **Autorizzazione** | Joomla ACL con gruppi: Public, Registered, Editor, Manager, Administrator, Super User. |
| **CSRF Protection** | Token Joomla (`JSession::checkToken()`) su ogni form admin e frontend. |
| **Input Validation** | Filtering e escaping con `JInput`, `JFilterInput`, prepared statements SQL. |
| **SQL Injection** | Esclusivamente query con `JDatabaseQuery` + prepared statements. |
| **XSS Protection** | Output escaping con `JHtml::_('string.truncate', ...)` e `htmlspecialchars`. |
| **Rate Limiting** | Limitazione tentativi login (plugin nativo Joomla + configurazione server). |
| **GDPR** | Cookie banner dinamico, consenso esplicito su form, privacy policy gestita da CMS, diritto all'oblio su richiesta. |

### 2.7 SEO
| Aspetto | Implementazione |
|---------|-----------------|
| **URL SEF** | Abilitato nativamente in Joomla + .htaccess Apache. |
| **Meta tag dinamici** | Plugin custom che popola Open Graph, Twitter Cards, meta description per ogni vista. |
| **Sitemap XML** | Componente nativo Joomla + plugin custom per includere entità custom. |
| **Structured Data** | JSON-LD per LocalBusiness, Event, Product/Service su pagine pertinenti. |
| **Performance** | Lazy loading immagini, WebP con fallback JPEG, CSS/JS minificati, caching Joomla attivo. |

### 2.8 Email & Notifiche
| Aspetto | Implementazione |
|---------|-----------------|
| **PHPMailer** | Incluso in Joomla, configurato con SMTP (es. Gmail/PEC aziendale). |
| **Notifiche prenotazione** | Email admin alla creazione prenotazione + email conferma cliente. |
| **Notifiche messaggio** | Email admin al ricevimento messaggio da form contatti. |
| **Template email** | Template HTML responsive per tutte le comunicazioni. |

---

## 3. Struttura del Database (Schema Logico)

### Tabelle Joomla Native (già presenti nell'installazione)
- `#__users` — utenti registrati
- `#__user_groups` — gruppi ACL
- `#__user_usergroup_map` — mapping utente-gruppo
- `#__categories` — categorie native (usate per blog/eventuali sezioni)
- `#__content` — articoli nativi Joomla (Home, Chi siamo, pagine statiche)
- `#__extensions` — estensioni installate
- `#__assets` — ACL assets
- `#__modules` — moduli
- `#__menu` — menu

### Tabelle Custom Componente `com_kokedama`
Tutte con prefisso `#__kokedama_`

#### 3.1 Servizi (`#__kokedama_services`)
| Campo | Tipo | Note |
|-------|------|------|
| id | INT PK AI | |
| title | VARCHAR(255) | Nome servizio |
| alias | VARCHAR(255) | Alias SEF |
| description | TEXT | Descrizione lunga |
| short_desc | VARCHAR(500) | Descrizione breve |
| price | DECIMAL(10,2) | Prezzo base (nullable) |
| duration | INT | Durata in minuti |
| image | VARCHAR(255) | Path immagine principale |
| gallery | TEXT | JSON array di path immagini |
| published | TINYINT(1) | 0/1 |
| featured | TINYINT(1) | In evidenza homepage |
| ordering | INT | Ordine manuale |
| created | DATETIME | |
| created_by | INT | FK `#__users` |
| modified | DATETIME | |
| meta_title | VARCHAR(255) | SEO |
| meta_desc | VARCHAR(500) | SEO |

#### 3.2 Progetti/Portfolio (`#__kokedama_projects`)
| Campo | Tipo | Note |
|-------|------|------|
| id | INT PK AI | |
| title | VARCHAR(255) | |
| alias | VARCHAR(255) | |
| description | TEXT | |
| materials | VARCHAR(255) | Materiali usati |
| plant_type | VARCHAR(100) | Tipo pianta |
| image | VARCHAR(255) | Cover |
| gallery | TEXT | JSON array path |
| service_id | INT | FK servizio correlato (nullable) |
| published | TINYINT(1) | |
| featured | TINYINT(1) | |
| created | DATETIME | |
| created_by | INT | |
| meta_title, meta_desc | VARCHAR | SEO |

#### 3.3 Galleria Foto (`#__kokedama_gallery`)
| Campo | Tipo | Note |
|-------|------|------|
| id | INT PK AI | |
| title | VARCHAR(255) | |
| alias | VARCHAR(255) | |
| image | VARCHAR(255) | Path file |
| thumbnail | VARCHAR(255) | Path thumbnail |
| category | VARCHAR(100) | Categoria (kokedama, workshop, eventi...) |
| description | TEXT | |
| project_id | INT | FK progetto (nullable) |
| event_id | INT | FK evento (nullable) |
| published | TINYINT(1) | |
| ordering | INT | |
| created | DATETIME | |

#### 3.4 Eventi & Workshop (`#__kokedama_events`)
| Campo | Tipo | Note |
|-------|------|------|
| id | INT PK AI | |
| title | VARCHAR(255) | |
| alias | VARCHAR(255) | |
| description | TEXT | |
| event_type | ENUM('workshop','evento','corso') | |
| event_date | DATE | Data evento |
| event_time | TIME | Orario inizio |
| end_time | TIME | Orario fine |
| location | VARCHAR(255) | Luogo |
| max_participants | INT | Max posti |
| price | DECIMAL(10,2) | Prezzo a persona |
| image | VARCHAR(255) | |
| published | TINYINT(1) | |
| featured | TINYINT(1) | |
| created | DATETIME | |
| meta_title, meta_desc | VARCHAR | SEO |

#### 3.5 Prenotazioni (`#__kokedama_bookings`)
| Campo | Tipo | Note |
|-------|------|------|
| id | INT PK AI | |
| booking_type | ENUM('servizio','evento') | Cosa si prenota |
| service_id | INT | FK servizio (nullable) |
| event_id | INT | FK evento (nullable) |
| booking_date | DATE | Data richiesta |
| booking_time | TIME | Orario richiesto |
| participants | INT | Numero partecipanti |
| first_name | VARCHAR(100) | |
| last_name | VARCHAR(100) | |
| email | VARCHAR(255) | |
| phone | VARCHAR(50) | |
| notes | TEXT | Note cliente |
| status | ENUM('pending','confirmed','rejected','cancelled','completed') | Default: pending |
| admin_notes | TEXT | Note interne admin |
| ip_address | VARCHAR(45) | GDPR: tracciamento IP |
| consent_gdpr | TINYINT(1) | Consenso privacy |
| created | DATETIME | |
| modified | DATETIME | |
| modified_by | INT | FK admin che ha gestito |

#### 3.6 Messaggi/Contatti (`#__kokedama_messages`)
| Campo | Tipo | Note |
|-------|------|------|
| id | INT PK AI | |
| name | VARCHAR(255) | |
| email | VARCHAR(255) | |
| phone | VARCHAR(50) | |
| subject | VARCHAR(255) | |
| message | TEXT | |
| status | ENUM('new','read','replied','archived') | Default: new |
| ip_address | VARCHAR(45) | |
| consent_gdpr | TINYINT(1) | |
| created | DATETIME | |
| read_at | DATETIME | |
| replied_at | DATETIME | |

---

## 4. Relazioni Principali (ER Diagram Logico)

```
SERVIZI (1) ──────< (N) PROGETTI
  │
  └──────< (N) PRENOTAZIONI (quando booking_type='servizio')

EVENTI (1) ───────< (N) PRENOTAZIONI (quando booking_type='evento')

PROGETTI (1) ─────< (N) GALLERY

EVENTI (1) ───────< (N) GALLERY

UTENTI (1) ───────< (N) PRENOTAZIONES (created_by su tabelle admin)
UTENTI (1) ───────< (N) PROGETTI (created_by)
UTENTI (1) ───────< (N) SERVIZI (created_by)
```

---

## 5. Struttura Joomla / Componenti

### 5.1 Componente Principale: `com_kokedama`
```
/administrator/components/com_kokedama/
  ├── kokedama.xml              # Manifest installazione
  ├── script.php                # Script post-installazione
  ├── access.xml                # Definizione ACL
  ├── config.xml                # Parametri componente
  ├── controller.php            # Controller base admin
  ├── controllers/              # Controller specifici
  │   ├── services.php
  │   ├── projects.php
  │   ├── gallery.php
  │   ├── events.php
  │   ├── bookings.php
  │   └── messages.php
  ├── models/
  │   ├── services.php
  │   ├── projects.php
  │   ├── gallery.php
  │   ├── events.php
  │   ├── bookings.php
  │   ├── messages.php
  │   └── forms/                # XML form Joomla
  │       ├── service.xml
  │       ├── project.xml
  │       ├── gallery.xml
  │       ├── event.xml
  │       ├── booking.xml
  │       └── message.xml
  ├── views/
  │   ├── services/
  │   │   ├── view.html.php
  │   │   └── tmpl/default.php
  │   ├── service/
  │   │   ├── view.html.php
  │   │   └── tmpl/edit.php
  │   ├── projects/
  │   ├── project/
  │   ├── gallery/
  │   ├── galleries/
  │   ├── events/
  │   ├── event/
  │   ├── bookings/
  │   ├── booking/
  │   ├── messages/
  │   └── message/
  ├── tables/
  │   ├── service.php
  │   ├── project.php
  │   ├── gallery.php
  │   ├── event.php
  │   ├── booking.php
  │   └── message.php
  └── helpers/
      └── kokedama.php          # Helper generico

/components/com_kokedama/
  ├── kokedama.php              # Entry point frontend
  ├── controller.php
  ├── controllers/
  │   ├── services.php          # Vista lista servizi
  │   ├── service.php           # Vista dettaglio servizio
  │   ├── projects.php
  │   ├── project.php
  │   ├── gallery.php
  │   ├── events.php
  │   ├── event.php
  │   ├── booking.php           # Creazione prenotazione
  │   ├── contact.php           # Invio messaggio
  │   └── ajax.php              # Endpoint AJAX
  ├── models/
  │   ├── services.php
  │   ├── service.php
  │   ├── projects.php
  │   ├── project.php
  │   ├── gallery.php
  │   ├── events.php
  │   ├── event.php
  │   ├── booking.php
  │   └── contact.php
  ├── views/
  │   ├── services/
  │   │   ├── view.html.php
  │   │   └── tmpl/default.php
  │   ├── service/
  │   ├── projects/
  │   ├── project/
  │   ├── gallery/
  │   ├── events/
  │   ├── event/
  │   ├── booking/
  │   │   ├── view.html.php
  │   │   └── tmpl/default.php  # Form prenotazione
  │   └── contact/
  └── helpers/
      └── route.php             # Router SEF custom
```

### 5.2 Plugin di Sistema: `plg_system_kokedama`
```
/plugins/system/kokedama/
  ├── kokedama.xml
  └── kokedama.php
  # Hook su: onAfterDispatch (SEO meta), onContentPrepareForm, onUserAfterSave
```

### 5.3 Moduli Frontend
```
/modules/mod_kokedama_services/     # Servizi in evidenza
/modules/mod_kokedama_events/       # Prossimi eventi
/modules/mod_kokedama_projects/     # Ultimi progetti
/modules/mod_kokedama_contact/      # Box contatto rapido
```

### 5.4 Template Frontend: `tpl_kokedama`
```
/templates/kokedama/
  ├── templateDetails.xml
  ├── index.php                     # Layout base
  ├── error.php
  ├── offline.php
  ├── component.php
  ├── html/                         # Override output core Joomla
  │   ├── com_content/
  │   ├── mod_custom/
  │   └── layouts/
  ├── css/
  │   ├── template.css              (compilato da SCSS)
  │   ├── custom.css
  │   └── bootstrap.override.css
  ├── scss/
  │   ├── template.scss
  │   ├── _variables.scss
  │   ├── _header.scss
  │   ├── _footer.scss
  │   ├── _home.scss
  │   ├── _services.scss
  │   ├── _gallery.scss
  │   ├── _forms.scss
  │   └── _responsive.scss
  ├── js/
  │   ├── template.js
  │   ├── gallery.js
  │   ├── booking.js
  │   └── lazyload.js
  ├── images/
  │   ├── logo.svg
  │   ├── favicon.ico
  │   └── bg/
  └── language/
      ├── it-IT/
      └── en-GB/
```

---

## 6. Struttura Frontend / Backend

### 6.1 Pagine Frontend (Site)
| Pagina | Vista Joomla | Note |
|--------|--------------|------|
| **Home** | Menu item → Featured Articles + Moduli custom | Hero full-screen, servizi in evidenza, prossimi eventi, CTA prenotazione |
| **Chi Siamo** | Menu item → Single Article | Storia, filosofia, team, foto atelier |
| **Servizi** | Menu item → com_kokedama/services | Griglia servizi con filtri |
| **Servizio Dettaglio** | Menu item → com_kokedama/service | Descrizione, prezzo, durata, galleria, CTA prenota |
| **Progetti** | Menu item → com_kokedama/projects | Portfolio filtrabile per categoria |
| **Progetto Dettaglio** | Menu item → com_kokedama/project | Descrizione, materiali, galleria |
| **Galleria** | Menu item → com_kokedama/gallery | Masonry/lightbox foto |
| **Eventi** | Menu item → com_kokedama/events | Calendario/lista eventi e workshop |
| **Evento Dettaglio** | Menu item → com_kokedama/event | Info, disponibilità, prenotazione |
| **Prenotazioni** | Menu item → com_kokedama/booking | Form multi-step: scelta servizio/evento → data/orario → dati personali → riepilogo |
| **Contatti** | Menu item → com_kokedama/contact | Form contatto + mappa Google + info |
| **Privacy Policy** | Menu item → Single Article | Testo GDPR |

### 6.2 Pagine Backend (Administrator)
| Sezione | Accesso | Funzioni |
|---------|---------|----------|
| **Dashboard Kokedama** | Manager+ | Widget riepilogativi: prenotazioni da gestire, messaggi non letti, prossimi eventi, statistiche |
| **Gestione Servizi** | Administrator+ | CRUD servizi, ordinamento, pubblicazione |
| **Gestione Progetti** | Administrator+ | CRUD progetti, associazione galleria |
| **Gestione Galleria** | Administrator+ | Upload multiplo, drag-drop ordinamento, categorie |
| **Gestione Eventi** | Administrator+ | CRUD eventi, gestione disponibilità |
| **Gestione Prenotazioni** | Manager+ | Vista tabellare con filtri (per stato, data, servizio), azioni bulk (conferma, rifiuta, cancella), esportazione CSV |
| **Gestione Messaggi** | Manager+ | Inbox messaggi, marca come letto/risposto, archivia, rispondi via email |
| **Configurazione** | Super User | Impostazioni email, SEO, GDPR, testi default |

### 6.3 Flusso Prenotazione (Frontend)
```
Utente → Pagina Servizi/Eventi
   → Seleziona servizio/evento
   → Vai a form prenotazione
   → Step 1: Seleziona data dal calendario (disponibilità dinamica via AJAX)
   → Step 2: Seleziona orario + numero partecipanti
   → Step 3: Inserisci nome, email, telefono, note
   → Step 4: Accetta privacy (checkbox obbligatoria)
   → Step 5: Riepilogo → Conferma
   → Salva in #__kokedama_bookings con status='pending'
   → Email conferma ricezione al cliente
   → Email notifica nuova prenotazione all'admin
```

### 6.4 Flusso Gestione Prenotazione (Backend)
```
Admin riceve email notifica
   → Accede a Dashboard → Vede prenotazione in "Pending"
   → Apre prenotazione
   → Azioni disponibili:
       ✓ Conferma → status='confirmed' → email conferma al cliente
       ✗ Rifiuta → status='rejected' → email rifiuto con motivazione
       ✎ Modifica → cambia data/orario/note → email aggiornamento
       ✕ Annulla → status='cancelled' → email annullamento
       ✓ Completa → status='completed' (post-evento)
```

---

## 7. Piano di Sviluppo Fasi

### Fase 1: Fondamenta
- Database schema + dati di esempio
- Manifest componente + installazione base
- Template Joomla struttura HTML/SCSS/JS

### Fase 2: Backend Admin
- CRUD completi per Servizi, Progetti, Galleria, Eventi
- Dashboard con widget
- Gestione Prenotazioni e Messaggi
- Configurazione globale

### Fase 3: Frontend Pubblico
- Pagine servizi, progetti, galleria, eventi
- Form prenotazione multi-step con AJAX
- Form contatti
- SEO e meta dinamici

### Fase 4: Integrazioni
- Email template HTML
- Notifiche automatiche
- GDPR (cookie banner, consensi)
- Sitemap XML
- Performance (lazy load, ottimizzazione immagini)

### Fase 5: Deploy
- Pacchetto di installazione completo (.zip)
- Documentazione installazione
- File SQL iniziale

---

## 8. Requisiti Server

| Requisito | Versione Minima |
|-----------|-----------------|
| PHP | 8.1 (consigliato 8.2+) |
| MySQL | 8.0+ |
| Apache | 2.4+ con mod_rewrite |
| mbstring | Sì |
| mysqli | Sì |
| openssl | Sì |
| intl | Sì |
| gd | Sì |
| fileinfo | Sì |
| max_upload_size | 8M+ (per upload foto) |
| memory_limit | 256M+ |

---

*Documento versione 1.0 — pronto per approvazione e sviluppo.*
