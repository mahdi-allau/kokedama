# 🚀 Kokedama su Render.com — Guida Gratuita

> Tempo totale stimato: **10 minuti** | Costo: **0 €**

---

## ✅ Cosa otterrai

- Un sito online con dominio gratuito tipo: `https://kokedama-abc123.onrender.com`
- HTTPS automatico (il lucchetto verde nel browser)
- Possibilità di collegare il TUO dominio quando vuoi (es. `www.tuodominio.it`)

---

## ⚠️ Importante: database su Render

Render (piano gratuito) **resetta il filesystem ad ogni deploy**. Questo significa che:
- ✅ Il sito parte con i dati di esempio (servizi, progetti, eventi)
- ⚠️ Se qualcuno invia una prenotazione o un messaggio, QUESTA si perde al prossimo aggiornamento del sito
- 💡 **Soluzione:** per una vera produzione con prenotazioni reali, passa a un hosting PHP condiviso (Aruba, SiteGround, ecc.) oppure a un piano a pagamento su Render con disco persistente

> Per ora, come vetrina/demo, Render è perfetto e gratuito!

---

## 📋 Prerequisiti

- Un account email (qualsiasi)
- Questa cartella sul tuo PC (`C:\Users\mahdi\Desktop\kokedma_simona`)

---

## PASSO 1 — Crea un repository su GitHub

1. Vai su [github.com](https://github.com) e registrati (o accedi)
2. Clicca il pulsante verde **"New"** (in alto a sinistra)
3. Compila così:
   - **Repository name:** `kokedama` (o come preferisci)
   - **Description:** `Sito Kokedama Sculture Naturali`
   - **Public** o **Private** (come preferisci)
   - ✅ NON spuntare "Add a README file"
   - ✅ NON spuntare "Add .gitignore"
   - ✅ NON spuntare "Choose a license"
4. Clicca **"Create repository"**
5. Nella pagina che appare, copia l'URL che inizia con `https://github.com/...` (lo useremo al passo 2)

---

## PASSO 2 — Pusha il codice su GitHub

Apri **Git Bash** (o il terminale) nella cartella del progetto e incolla questi comandi:

```bash
cd C:/Users/mahdi/Desktop/kokedma_simona

# Collega il repository locale a GitHub
# SOSTITUISCI l'URL con il tuo! Esempio:
git remote add origin https://github.com/TUO-USERNAME/kokedama.git

# Invia tutto su GitHub
git push -u origin main
```

Quando ti chiede username e password, usa:
- **Username:** il tuo nome utente GitHub
- **Password:** una **Personal Access Token** (non la password normale!)
  - Per crearla: GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic) → Generate new token → spunta "repo" → copia il token

Se tutto va bene, vedrai scritto qualcosa come:
```
Enumerating objects: 121, done.
Writing objects: 100% (121/121), done.
```

✅ **Verifica:** vai su `https://github.com/TUO-USERNAME/kokedama` e dovresti vedere tutti i file.

---

## PASSO 3 — Deploy su Render.com

1. Vai su [render.com](https://render.com) e registrati (puoi usare "Sign up with GitHub")
2. Dalla dashboard, clicca **"New +"** → **"Web Service"**
3. Seleziona **"Build and deploy from a Git repository"**
4. Clicca **"Connect account"** accanto a GitHub e autorizza Render
5. Seleziona il repository `kokedama` dalla lista
6. Compila il form così:
   - **Name:** `kokedama` (sarà il tuo dominio: `kokedama-xxx.onrender.com`)
   - **Region:** Frankfurt (EU Central) — più vicino all'Italia
   - **Branch:** `main`
   - **Runtime:** `Docker`
   - **Plan:** `Free`
   - Tutto il resto lascia come sta (Render rileva automaticamente il Dockerfile)
7. Clicca in fondo **"Create Web Service"**

---

## PASSO 4 — Aspetta il deploy

Render inizierà a costruire il container. Vedrai dei log in tempo reale.

- **Build:** ~1-2 minuti
- **Deploy:** ~30 secondi

Quando lo stato diventa **"Live"**, clicca l'URL in alto (es. `https://kokedama-abc123.onrender.com`).

🎉 **Il tuo sito è online!**

---

## 🔐 Accesso area admin

- Vai su `https://kokedama-xxx.onrender.com/admin`
- Username: `admin`
- Password: `kokedama2026`
- **Cambiala subito!**

---

## 🌐 Come cambiare dominio (quando vuoi)

Quando avrai comprato il tuo dominio (es. `www.kokedamaferrara.it`):

1. Su Render, vai nel tuo Web Service → **Settings**
2. Scorri fino a **"Custom Domains"**
3. Clicca **"Add Custom Domain"**
4. Inserisci il tuo dominio (es. `www.kokedamaferrara.it`)
5. Render ti darà istruzioni DNS da inserire nel pannello del tuo registrar (Aruba, GoDaddy, ecc.)
6. Dopo qualche minuto, il tuo dominio punterà al sito

Il dominio gratuito `.onrender.com` resterà attivo, ma il principale sarà il tuo.

---

## 🔄 Come aggiornare il sito

Ogni volta che modifichi i file e vuoi aggiornare il sito:

```bash
cd C:/Users/mahdi/Desktop/kokedma_simona
git add .
git commit -m "Descrizione delle modifiche"
git push origin main
```

Render si aggiorna automaticamente in 2-3 minuti!

---

## 🆘 Se qualcosa non funziona

| Problema | Soluzione |
|----------|-----------|
| "Repository not found" su Render | Assicurati che il repo sia pubblico o che Render abbia accesso ai repo privati |
| Build fallisce | Controlla i log su Render, probabilmente è un problema di permessi sul `database.sqlite` |
| Pagina 404 | Aspetta 1 minuto in più, il deploy potrebbe essere ancora in corso |
| Password admin non funziona | Il database è quello del repo, quindi `kokedama2026` è corretta. Se hai modificato il DB in locale, ricordati di fare `git push` |

---

*Buon deploy! 🌱*
