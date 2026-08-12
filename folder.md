# Kokedama & Sculture Naturali — Documentazione Progetto

> **Sito web completo** per l'attività artigianale di kokedama e sculture vegetali di Ferrara.
> **Scopo del file:** poterlo passare a un altro sviluppatore (o a un'altra AI) e riprendere il lavoro senza dover indovinare nulla.

**Ultimo aggiornamento:** 12 agosto 2026 — *revisione 3: nuovo design, pannello di gestione completo, correzioni di sicurezza.*

---

## 1. Panoramica

Sito full-stack in PHP puro + SQLite, senza framework né dipendenze da installare.

- **Sito pubblico:** Home, Chi siamo, Servizi, Progetti, Galleria, Eventi, Contatti, Prenotazioni, Privacy
- **Pannello di gestione:** accesso protetto, riepilogo, prenotazioni, messaggi e **gestione completa dei contenuti** (servizi, eventi, progetti, foto) con caricamento immagini
- **Database:** un solo file SQLite, zero configurazione
- **Design:** sistema "Atelier" — palette naturale, tipografia editoriale, mobile-first

---

## 2. Cosa è stato fatto nella revisione 3

Il punto di partenza aveva tre problemi che ne impedivano l'uso reale.

### 2.1 Problemi risolti (erano bloccanti)

| Problema | Perché era grave | Soluzione |
|---|---|---|
| **Su smartphone il menu spariva del tutto** (`.navbar-nav { display:none }` senza alternativa) | La maggior parte dei visitatori di un'attività locale arriva da telefono: il sito era di fatto inutilizzabile | Menu a tutta pagina con pulsante hamburger + barra azioni fissa in basso (Chiama / WhatsApp / Mappa / Prenota) |
| **4 voci su 9 del menu admin puntavano a pagine inesistenti** (`services.php`, `events.php`, `projects.php`, `gallery.php`) | Simona non poteva aggiungere un workshop, un servizio o una foto: il sito era congelato sui dati di esempio | Create tutte e quattro, con motore CRUD condiviso e caricamento immagini |
| **Nessun supporto alle immagini reali** — solo icone Font Awesome | Per un'attività artigianale le foto *sono* il prodotto: senza, il sito non convince | Ogni contenuto ha il suo campo immagine; se manca, compare un segnaposto botanico curato invece di un buco |

### 2.2 Altri difetti corretti

- **`database.sqlite` era scaricabile dal web** — conteneva email e telefoni dei clienti. Ora bloccato da `.htaccess`.
- **Router vulnerabile a inclusione file**: `pages/$request.php` senza filtro. Ora l'indirizzo è validato con espressione regolare.
- **Router rotto sugli URL di dettaglio installando il sito in radice**: `str_replace('/', '', ...)` trasformava `/service/mio-slug` in `servicemio-slug`. Ora si toglie solo il prefisso reale.
- **`APP_URL` sbagliato nelle pagine admin**: era calcolato su `dirname(SCRIPT_NAME)`, quindi dentro `/admin` puntava a `.../admin` e il CSS del pannello dava 404. Ora è calcolato sulla posizione della cartella `app`.
- **Il banner cookie non compariva mai** (mancava l'aggiunta della classe `.show`).
- **`admin/messages.php` era rotto**: la query di aggiornamento concatenava una variabile al posto dello stato e non associava i parametri. "Segna come letto" andava in errore.
- **`config.php` calcolava un hash bcrypt a ogni richiesta** (~50–100 ms sprecati per pagina). Ora l'hash è salvato in database e si genera una volta sola.
- **Testo invisibile** sul pulsante "Prenota" nella navbar (regola CSS più specifica che sovrascriveva il colore).
- **`truncate()` tagliava male le lettere accentate** (usava `strlen`/`substr` invece delle versioni multibyte).
- Mancavano `pages/project.php` e `pages/privacy.php`: entrambe erano collegate ma davano 404.

### 2.3 Aggiunte

**Design**
- Nuovo sistema CSS v3 (`style.css`), palette più profonda, tipografia Playfair Display + Jost
- Hero editoriale a due colonne con foto, badge e riga di rassicurazioni
- Sezione "Come funziona" in tre passaggi, fascia numeri, recensioni, banda CTA
- Comparsa progressiva dei blocchi allo scroll (`data-reveal`), rispettosa di `prefers-reduced-motion`
- Galleria con filtri per categoria e lightbox (apribile anche da tastiera)
- Pagine di dettaglio ripensate per servizi, progetti ed eventi

**Funzionalità**
- **Posti disponibili calcolati in automatico** sugli eventi, con avviso "ultimi N posti" e blocco a esaurimento
- **Pulsante WhatsApp** ovunque, attivabile scrivendo il numero nelle impostazioni
- **Orari di apertura** modificabili, con evidenziazione automatica del giorno corrente
- **Mappa Google** incorporabile incollando il codice dal sito di Google
- Meta description, Open Graph e dati strutturati `LocalBusiness` per Google
- Modulo prenotazione con validazione lato server, dati conservati in caso di errore e preselezione del servizio/evento di provenienza
- Email automatiche più complete, con firma e riepilogo

**Sicurezza**
- Token CSRF su tutti i moduli, campo trappola anti-spam
- Verifica reale del tipo di immagine caricata (non ci si fida dell'estensione), limite 6 MB
- Cartella `assets/uploads` che non esegue script
- Freno ai tentativi di accesso ripetuti, rigenerazione dell'ID di sessione, scadenza dopo 8 ore
- Cookie di sessione `httponly` + `samesite`
- Intestazioni di sicurezza in `.htaccess`

---

## 3. Tecnologie

| Livello | Tecnologia |
|---|---|
| Backend | PHP 8.0+ (vanilla, nessun framework) |
| Database | SQLite 3 (file `database.sqlite`) |
| Frontend | HTML5, CSS3, JavaScript vanilla |
| Font | Google Fonts: Playfair Display (titoli) + Jost (testo) |
| Icone | Font Awesome 6.5.1 (CDN) |
| Server | Apache con `mod_rewrite`, oppure server PHP integrato |

> PHP 8 è richiesto: il codice usa `match`, `str_contains`, `str_starts_with` e l'espansione di array.

---

## 4. Struttura cartelle

```
app/
├── .htaccess                  ← Rewrite + protezione database + intestazioni sicurezza
├── index.php                  ← Router (valida l'indirizzo prima di caricare la pagina)
├── config.php                 ← Costanti, APP_URL, sessione, modalità debug
├── database.sqlite            ← Database completo (NON accessibile dal web)
├── init_db.py                 ← Script per ricreare il DB da zero (opzionale)
│
├── includes/
│   ├── db.php                 ← Connessione PDO, initDatabase(), seedSettings(), dati di esempio
│   ├── functions.php          ← Helper condivisi (vedi § 6)
│   └── auth.php               ← Login, cambio password, freno ai tentativi
│
├── pages/                     ← Pagine pubbliche (mai raggiungibili direttamente: si passa dal router)
│   ├── home.php               ├── services.php     ├── service.php
│   ├── about.php              ├── projects.php     ├── project.php
│   ├── gallery.php            ├── events.php       ├── event.php
│   ├── booking.php            ├── contact.php      └── privacy.php
│
├── admin/
│   ├── index.php              ← Accesso
│   ├── logout.php
│   ├── _layout.php            ← Intestazione condivisa + menu laterale con contatori
│   ├── _layout_end.php        ← Chiusura + JavaScript del pannello
│   ├── _crud.php              ← Motore CRUD condiviso (vedi § 8)
│   ├── dashboard.php          ← Riepilogo + suggerimenti "per migliorare il sito"
│   ├── bookings.php           ← Prenotazioni: filtri, azioni di gruppo, note interne
│   ├── messages.php           ← Messaggi: filtri, azioni di gruppo, risposta rapida
│   ├── services.php           ┐
│   ├── events.php             │ 4 file di sola configurazione
│   ├── projects.php           │ che usano _crud.php
│   ├── gallery.php            ┘
│   └── settings.php           ← Dati attività, orari, testi home, SEO, password
│
├── templates/
│   ├── header.php             ← <head>, SEO, barra info, navbar, menu mobile
│   └── footer.php             ← Footer, barra azioni mobile, WhatsApp, cookie, JavaScript
│
└── assets/
    ├── css/
    │   ├── style.css          ← Design del sito pubblico (~1300 righe, in sezioni numerate)
    │   └── admin.css          ← Design del pannello
    └── uploads/               ← Immagini caricate (.htaccess: nessuno script eseguibile)
        ├── services/  ├── events/  ├── projects/  ├── gallery/  └── site/
```

**Nella radice del progetto** (fuori da `app/`):
- `preview.html` + `preview-admin.html` — anteprima statica del design, collegata al CSS reale. Si aprono nel browser senza PHP, utili per valutare la grafica.
- `README.md`, `ARCHITETTURA.md`, `build.py`, `packages/`, `pkg_kokedama*.zip` — **riguardano la vecchia versione Joomla**, non questa applicazione. Vedi § 12.

---

## 5. Configurazione (`config.php`)

| Costante | Significato |
|---|---|
| `APP_ROOT` | Cartella `app` |
| `APP_BASE` | `''` in radice, `'/sottocartella'` se installato dentro una cartella |
| `APP_URL` | Indirizzo base completo. **Calcolato sulla posizione di `app`, non dello script**, altrimenti le pagine in `/admin` romperebbero i link |
| `DB_PATH` | `APP_ROOT/database.sqlite` |
| `UPLOADS_DIR` / `UPLOADS_URL` | Cartella e indirizzo delle immagini caricate |
| `ADMIN_USER` | `admin` |
| `ADMIN_PASS_DEFAULT` | `kokedama2026` — **vale solo al primo accesso**, poi l'hash sta nel database |
| `APP_DEBUG` | `true` in locale, `false` online (nasconde gli errori ai visitatori) |

Se APP_URL venisse calcolato male (proxy, alias Apache particolari), si può forzare a mano: la riga è già pronta commentata.

---

## 6. Funzioni utili (`includes/functions.php`)

| Funzione | A cosa serve |
|---|---|
| `e($s)` | Stampa sicura (anti-XSS). **Usare sempre** sui dati che arrivano dal database |
| `getSetting($k, $def)` / `setSetting($k, $v)` | Impostazioni. Lette tutte una volta sola per richiesta e tenute in memoria |
| `renderMedia($img, $alt, $icona)` | Stampa l'immagine se esiste davvero su disco, altrimenti il segnaposto botanico |
| `mediaUrl()` / `mediaExists()` | Indirizzo dell'immagine / verifica presenza del file |
| `uploadImage($file, $sottocartella, &$errore)` | Caricamento con verifica reale del formato, limite 6 MB, messaggi in italiano |
| `deleteUpload($img)` | Cancella un'immagine restando dentro `uploads/` |
| `whatsappLink($messaggio)` | Link WhatsApp precompilato, `''` se il numero non è configurato |
| `telHref()` / `fullAddress()` / `mapsDirectionsUrl()` | Contatti pronti all'uso |
| `mapsEmbedSrc()` | Estrae il solo indirizzo dall'`<iframe>` di Google: nel sito non finisce mai HTML arbitrario |
| `openingHours()` / `todayHours()` | Orari; `todayHours()` riconosce sia i giorni singoli sia gli intervalli ("Martedì - Venerdì") |
| `formatPrice()` / `formatDate($d, $lungo)` / `formatTime()` / `relativeDay()` | Formattazione in italiano |
| `truncate($testo, $n)` | Taglio sicuro con le lettere accentate |
| `uniqueSlug($tabella, $titolo, $escludiId)` | Slug garantito univoco |
| `csrfField()` / `requireCsrf()` | Protezione dei moduli |
| `honeypotField()` / `honeypotTriggered()` | Anti-spam senza captcha |
| `flash($msg, $tipo)` / `takeFlash()` | Messaggi di conferma tra una pagina e l'altra |
| `sendEmail()` / `emailSignature()` | Invio email con oggetto codificato UTF-8 |

---

## 7. Router (`index.php`)

1. Prende il percorso da `REQUEST_URI`
2. Toglie `APP_BASE` **solo se è realmente il prefisso** (non con `str_replace`, che romperebbe gli slug)
3. Valida con espressione regolare: sono ammessi solo `pagina` o `sezione/slug` in minuscolo
4. Cerca `pages/{richiesta}.php`
5. Altrimenti prova `service/{slug}`, `project/{slug}`, `event/{slug}`
6. Altrimenti pagina 404

**Su Apache serve `mod_rewrite`.** Senza `.htaccess` funziona solo la home.

---

## 8. Il motore CRUD (`admin/_crud.php`)

Le quattro sezioni di gestione contenuti condividono un unico motore: `services.php`, `events.php`, `projects.php` e `gallery.php` contengono **solo configurazione**, nessuna logica.

Un file di sezione è fatto così:

```php
require_once __DIR__ . '/_crud.php';

crudRun([
    'table'     => 'services',            // deve essere in CRUD_TABLES
    'page'      => 'services',            // voce evidenziata nel menu laterale
    'title'     => 'Servizi',
    'singular'  => 'servizio',            // usato nei messaggi di conferma
    'icon'      => 'fas fa-spa',
    'uploadDir' => 'services',            // sottocartella di assets/uploads
    'slugFrom'  => 'title',               // null se la tabella non ha slug
    'order'     => 'ordering, id',
    'publicUrl' => fn($r) => APP_URL . '/service/' . $r['slug'],   // o null
    'intro'     => 'Testo di aiuto mostrato in cima',
    'columns'   => [ ... ],               // colonne dell'elenco
    'fields'    => [ ... ],               // campi del modulo
]);
```

**Tipi di campo disponibili:** `text`, `textarea`, `number`, `price`, `date`, `time`, `select`, `checkbox`, `image`, `section` (semplice intestazione, non è una colonna del database).

**Aiutanti per le colonne:** `crudColMedia()` (miniatura + titolo), `crudColPublished()`, `crudColText()`, `crudColPrice()`. Per casi particolari si passa una funzione propria in `render`.

**Per aggiungere una nuova sezione gestionale:**
1. Aggiungi la tabella a `CRUD_TABLES` in `_crud.php`
2. Crea la tabella in `includes/db.php` → `initDatabase()`
3. Crea `admin/nuova.php` con la sola chiamata a `crudRun([...])`
4. Aggiungi la voce a `$adminNav` in `admin/_layout.php`

> Le tabelle non arrivano mai dalla richiesta HTTP: sono confrontate con l'elenco `CRUD_TABLES`. Non introdurre nomi di tabella o colonna presi da `$_GET` / `$_POST`.

---

## 9. Database

Tabelle: `settings`, `services`, `projects`, `gallery`, `events`, `bookings`, `messages`.
Schema completo in `includes/db.php` → `initDatabase()`.

**Relazioni:** `bookings.service_id` → `services.id` · `bookings.event_id` → `events.id` · `projects.service_id` → `services.id` · `gallery.project_id` → `projects.id` · `gallery.event_id` → `events.id`

### Impostazioni disponibili (`seedSettings()`)

Aggiungere una chiave lì la rende disponibile **anche ai database già esistenti**: `INSERT OR IGNORE` non tocca i valori già modificati da Simona.

| Gruppo | Chiavi |
|---|---|
| Attività | `business_name`, `business_tagline`, `business_address`, `business_city`, `business_zip`, `business_phone`, `business_whatsapp`, `business_email`, `business_facebook`, `business_instagram`, `business_maps_embed`, `business_hours` |
| Contenuti | `home_hero_title`, `home_hero_subtitle`, `home_hero_image`, `about_intro` |
| Prenotazioni | `booking_notice_days`, `booking_intro` |
| SEO e legale | `seo_description`, `gdpr_consent_text`, `privacy_owner` |
| Interno | `admin_pass_hash` (non modificare a mano) |

**Formato degli orari:** una riga per giorno, `Giorno|Orario`.
```
Lunedì|Chiuso
Martedì - Venerdì|9:30 - 13:00 / 15:30 - 19:30
Sabato|9:30 - 19:30
```

---

## 10. Design system (`assets/css/style.css`)

Il file è diviso in 20 sezioni numerate; i colori sono tutti variabili CSS in cima.

```css
--green-600: #3F6247;   /* primario */
--green-900: #1A2A1F;   /* fondi scuri */
--clay-600:  #B96A4C;   /* accento terracotta */
--sand:      #F2EDE3;
--cream:     #FBF9F4;   /* sfondo pagina */
--ink:       #1F2A21;   /* titoli */
--body:      #4C5348;   /* testo */
--muted:     #7C8376;
```

I vecchi nomi (`--koke-green`, ecc.) esistono ancora come alias, così gli `style=""` scritti a mano nelle pagine continuano a funzionare.

**Componenti principali:** `.hero` / `.hero-grid` · `.page-hero` · `.section` (+ `.bg-light`, `.bg-green`) · `.section-header` · `.eyebrow` · `.card` + `.card-media` + `.card-link` · `.media-placeholder` · `.feature-row` (+ `.reverse`) · `.steps` · `.stats-grid` · `.testimonial` · `.cta-band` · `.gallery-grid` + `.lightbox` · `.contact-grid` · `.booking-form` / `.form-aside` · `.choice-group` · `.mobile-drawer` · `.mobile-bar` · `.fab-stack` · `.empty-state`

**Punti di rottura:** 1100px (navbar più stretta) · **991px** (menu hamburger, hero in colonna) · **767px** (barra azioni in basso, griglie a una colonna) · 480px

**Animazione allo scroll:** basta aggiungere `data-reveal` a un elemento. Per sfalsare: `style="--reveal-delay:.1s"`.

---

## 11. Come avviare il progetto

### Server PHP integrato (il più rapido)
```bash
cd app
php -S localhost:7100
```

### XAMPP
1. Abilita `mod_rewrite` in `httpd.conf` (togli il `#` da `LoadModule rewrite_module`)
2. Assicurati che ci sia `AllowOverride All` per `htdocs`
3. Copia `app` in `C:\xampp\htdocs\` (rinominala, es. `kokedama`)
4. Riavvia Apache e apri `http://localhost/kokedama/`

### Anteprima del solo design (senza PHP)
Apri `preview.html` nel browser: mostra sito e pannello con il CSS reale.
Se il browser blocca il caricamento del CSS da `file://`, servi la cartella con un server statico qualsiasi.

### Primo accesso al pannello
`/admin/` — utente `admin`, password `kokedama2026`.
**Cambiala subito** da Impostazioni → Password di accesso.

---

## 12. Rapporto con la vecchia versione Joomla

In questa cartella convivono **due progetti distinti**:

| Cosa | Stato |
|---|---|
| `app/` | ✅ **Il progetto attivo.** Applicazione PHP + SQLite autonoma |
| `packages/`, `build.py`, `pkg_kokedama*.zip`, `pkg_kokedama.xml`, `README.md`, `ARCHITETTURA.md` | ⚠️ Vecchia versione come estensione Joomla 5 + MySQL. **Non aggiornata nella revisione 3** |

`README.md` descrive l'installazione Joomla e **non vale per `app/`**. Se la versione Joomla non serve più, quei file si possono archiviare o eliminare: `app/` non li usa in alcun modo.

---

## 13. Prima di pubblicare online

- [ ] Cambiare la password dal pannello
- [ ] Verificare che `APP_DEBUG` risulti `false` (automatico fuori da localhost)
- [ ] Controllare che `https://iltuosito.it/database.sqlite` dia errore 403 — se lo scarica, `.htaccess` non viene letto: serve `AllowOverride All`
- [ ] Configurare l'invio email: `mail()` spesso non funziona sugli hosting. Valutare PHPMailer con SMTP
- [ ] Far rivedere il testo della privacy da un consulente e completare i dati mancanti
- [ ] Caricare le foto vere: è la cosa che incide di più sul risultato
- [ ] Impostare numero WhatsApp, orari e mappa
- [ ] Fare una copia periodica di `database.sqlite`

---

## 14. Cosa si potrebbe fare dopo

- [ ] **Email affidabili con PHPMailer/SMTP** — la più utile: oggi `mail()` può fallire in silenzio
- [ ] **Ridimensionamento automatico delle immagini** al caricamento (oggi si accettano file fino a 6 MB così come sono)
- [ ] **Sitemap XML** e `robots.txt`
- [ ] **Backup automatico** del database
- [ ] Riordino dei contenuti trascinandoli, invece del campo numerico "Ordine"
- [ ] Vista calendario degli eventi
- [ ] Recensioni gestibili dal pannello (oggi le tre in home sono scritte in `pages/home.php`)
- [ ] Pagamento online (Stripe/PayPal) per le prenotazioni
- [ ] Area clienti per rivedere le proprie prenotazioni

---

## 15. Note per chi mette mano al progetto

- **Non scrivere i dati dell'attività nel codice**: si modificano da Impostazioni. Per aggiungere un dato nuovo, inseriscilo in `seedSettings()` (`includes/db.php`) e poi in `admin/settings.php`.
- **Ogni valore che arriva dal database va stampato con `e()`.** Unica eccezione consapevole: la mappa, che passa da `mapsEmbedSrc()` e viene ricostruita da noi.
- **Ogni modulo deve avere `csrfField()`** e, lato server, `requireCsrf()`.
- **Le immagini si stampano con `renderMedia()`**, mai con un `<img>` scritto a mano: gestisce il caso "foto non ancora caricata".
- **Per aggiungere una pagina pubblica:** crea `pages/nomepagina.php`, poi aggiungi la voce a `$navItems` in `templates/header.php`. Il router la trova da solo.
- **Lo stile è tutto in due file CSS.** Nessun framework: si modifica direttamente lì.
- **Provare sempre sotto i 768px** dopo ogni modifica: è da lì che arriva la maggior parte dei visitatori.
- Il pannello mostra in automatico una lista di suggerimenti (foto mancanti, WhatsApp non impostato, nessun evento in calendario): è in `admin/dashboard.php`, variabile `$todo`.

---

## 16. Verifiche non ancora eseguite

**PHP non è installato sulla macchina di sviluppo**, quindi il codice della revisione 3 **non è stato eseguito né sottoposto a lint**. È stato riletto a mano, ma prima di considerarlo pronto va provato dal vivo:

1. Avvia il sito e apri tutte le pagine pubbliche
2. Invia una prenotazione (servizio ed evento) e un messaggio dai contatti
3. Nel pannello: crea, modifica ed elimina un elemento per ognuna delle 4 sezioni, caricando un'immagine
4. Conferma e rifiuta una prenotazione, verificando lo stato
5. Segna un messaggio come letto/risposto/archiviato
6. Salva le impostazioni e cambia la password
7. Ripeti i passaggi principali da smartphone

Il **design** è invece stato verificato visivamente nel browser (desktop 1280px e mobile 375px) tramite `preview.html`.
