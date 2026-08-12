# Kokedama Sculture Naturali — Sito Web Completo

**Versione:** 1.0.0  
**CMS:** Joomla 5.x  
**PHP:** 8.1+  
**Database:** MySQL 8.0+  
**Licenza:** GPL-2.0+

---

## 📦 Contenuto del pacchetto

| File | Descrizione |
|------|-------------|
| `pkg_kokedama_v1.0.0.zip` | Pacchetto di installazione completo (tutte le estensioni in un unico zip) |
| `packages/com_kokedama.zip` | Componente gestionale (servizi, progetti, galleria, eventi, prenotazioni, messaggi) |
| `packages/tpl_kokedama.zip` | Template frontend custom responsive |
| `packages/plg_system_kokedama.zip` | Plugin sistema (SEO meta dinamici) |
| `packages/mod_kokedama_services.zip` | Modulo homepage — Servizi in evidenza |
| `packages/mod_kokedama_events.zip` | Modulo homepage — Prossimi eventi |

---

## 🚀 Installazione

### Requisiti server
- PHP 8.1 o superiore
- MySQL 8.0+ o MariaDB 10.4+
- Apache 2.4+ con `mod_rewrite` abilitato
- Estensioni PHP: mbstring, mysqli, openssl, intl, gd, fileinfo

### Passaggi

1. **Installa Joomla 5** (se non già presente)
   - Scarica da [joomla.org](https://www.joomla.org)
   - Segui la procedura di installazione standard
   - Durante l'installazione, scegli **MySQLi** come driver database

2. **Installa il pacchetto Kokedama**
   - Vai in **Sistema → Installa → Estensioni**
   - Carica il file `pkg_kokedama_v1.0.0.zip`
   - Joomla installerà automaticamente tutte le estensioni contenute

3. **Imposta il template predefinito**
   - Vai in **Sistema → Stili del template del sito**
   - Clicca sulla stella accanto a **Kokedama** per renderlo predefinito

4. **Configura i dati aziendali**
   - Vai in **Componenti → Kokedama → Opzioni**
   - Inserisci: nome attività, indirizzo, telefono, email, social, embed Google Maps
   - Questi dati sono modificabili in qualsiasi momento dall'admin

5. **Crea le pagine del menu**
   - Vai in **Menu → Menu principale → Nuovo**
   - Crea le voci di menu collegate alle viste del componente Kokedama:
     - Home → Tipo: Articoli in evidenza
     - Servizi → Tipo: Kokedama → Lista servizi
     - Progetti → Tipo: Kokedama → Lista progetti
     - Galleria → Tipo: Kokedama → Galleria
     - Eventi → Tipo: Kokedama → Lista eventi
     - Prenotazioni → Tipo: Kokedama → Form prenotazione
     - Contatti → Tipo: Kokedama → Form contatti

6. **Pubblica i moduli homepage**
   - Vai in **Contenuto → Moduli del sito**
   - Cerca "Servizi in evidenza" e "Prossimi eventi"
   - Assegna entrambi alla posizione **main-top** o **main-bottom** del template

7. **Configura email**
   - Vai in **Configurazione globale → Server → Mail**
   - Imposta il server SMTP (Gmail, PEC, o altro)
   - Le notifiche prenotazioni e messaggi useranno questa configurazione

---

## 🗄️ Database

Il componente crea automaticamente le seguenti tabelle all'installazione:

- `#__kokedama_services` — Servizi offerti
- `#__kokedama_projects` — Portfolio progetti
- `#__kokedama_gallery` — Galleria fotografica
- `#__kokedama_events` — Eventi e workshop
- `#__kokedama_bookings` — Prenotazioni clienti
- `#__kokedama_messages` — Messaggi dal form contatti

**Dati di esempio inclusi:** 4 servizi, 3 progetti, 3 eventi, 5 immagini galleria.

---

## ⚙️ Funzionalità Admin

### Dashboard
- Widget riepilogativi: prenotazioni pending, messaggi nuovi, prossimi eventi, servizi attivi
- Ultimi 5 messaggi e prenotazioni in tempo reale

### Gestione Prenotazioni
- Tabella con filtri per stato e tipo
- Azioni bulk: Conferma, Rifiuta, Annulla, Completa
- Email automatiche al cliente al cambio di stato
- Note interne admin

### Gestione Messaggi
- Inbox con stati: Nuovo, Letto, Risposto, Archiviato
- Evidenziazione automatica messaggi nuovi
- Cambio stato in bulk

### Gestione Contenuti
- CRUD completo per Servizi, Progetti, Galleria, Eventi
- Upload immagini
- SEO meta per ogni elemento
- Pubblicazione / In evidenza / Ordinamento

### Configurazione Globale
- Dati aziendali (modificabili senza toccare il codice)
- Impostazioni prenotazioni (email, auto-conferma, max partecipanti)
- Impostazioni contatti
- SEO default
- GDPR (articolo privacy, testo consenso)

---

## 🔒 Sicurezza e GDPR

- **CSRF tokens** su ogni form
- **Input filtering** e prepared statements SQL
- **XSS protection** con output escaping
- **Cookie banner** dinamico con consenso esplicito
- **Checkbox GDPR obbligatoria** su form prenotazione e contatti
- **Tracciamento IP** solo per sicurezza (registrato in database)
- **Joomla ACL** con gruppi e permessi

---

## 🎨 Personalizzazione Template

Il template `tpl_kokedama` usa variabili CSS facilmente modificabili in `css/template.css`:

```css
:root {
  --koke-green: #5B7B5B;
  --koke-terracotta: #C67B5C;
  --koke-sand: #F5F0E8;
  --koke-cream: #FAFAF5;
  --koke-brown: #8B6F47;
}
```

Font: Playfair Display (titoli) + Lato (body)

---

## 📧 Notifiche Email

| Evento | Destinatario | Oggetto |
|--------|-------------|---------|
| Nuova prenotazione | Admin | Nuova prenotazione ricevuta |
| Nuova prenotazione | Cliente | Conferma ricezione prenotazione |
| Prenotazione confermata | Cliente | Prenotazione confermata |
| Prenotazione rifiutata | Cliente | Prenotazione non confermata |
| Nuovo messaggio | Admin | Nuovo messaggio dal sito |

---

## 🌐 SEO

- URL SEF nativi Joomla
- Meta title/description dinamici per ogni servizio/progetto/evento
- Open Graph tags automatici
- Plugin sistema per popolamento meta

---

## 🆘 Supporto

Per modificare l'indirizzo, telefono, email o altri dati aziendali:
**Componenti → Kokedama → Opzioni → Dati Aziendali**

Non è necessario modificare il codice.

---

*© 2026 Kokedama & Sculture Naturali — Piazza della Repubblica 11, 44121 Ferrara*
