# Kokedama — Deploy su Render.com

## Metodo 1: Render.com (consigliato — dominio gratuito + facile)

1. Vai su [render.com](https://render.com) e registrati (gratis)
2. Clicca **New + → Web Service**
3. Collega il tuo repository GitHub (o usa "Deploy from image" se hai Docker Hub)
4. Se usi GitHub, pusha questa cartella su un repo privato:
   ```bash
   git init
   git add .
   git commit -m "Primo deploy Kokedama"
   git remote add origin https://github.com/TUO-USER/kokedama.git
   git push -u origin main
   ```
5. Su Render, seleziona il repo, lascia tutto di default (rileverà il Dockerfile)
6. Clicca **Create Web Service**
7. In 2-3 minuti avrai un URL tipo: `https://kokedama-xxx.onrender.com`

**Per aggiornare il sito:** modifica i file, fai `git push`, Render si aggiorna automaticamente.

**Per usare il TUO dominio:**
- Su Render, vai in **Settings → Custom Domains**
- Aggiungi il tuo dominio (es. `www.tuodominio.it`)
- Segui le istruzioni DNS che ti dà Render
- Fatto! Il sito risponderà sul tuo dominio.

---

## Metodo 2: Docker in locale (per test)

Se hai Docker Desktop installato:
```bash
cd C:\Users\mahdi\Desktop\kokedma_simona
docker-compose up --build
```
Poi apri: `http://localhost:8080`

---

## Metodo 3: PHP built-in (solo sviluppo locale)

```bash
cd C:\Users\mahdi\Desktop\kokedma_simona\app
php -S localhost:8000
```
Poi apri: `http://localhost:8000`

---

## Metodo 4: Hosting condiviso PHP (aruba, siteground, etc.)

1. Comprimi la cartella `app/` in un file ZIP
2. Caricala via FTP nella root del dominio
3. Estrai i file
4. Assicurati che il file `database.sqlite` abbia permessi di scrittura
5. Il sito dovrebbe funzionare immediatamente!

---

## ⚠️ Nota importante: database SQLite

Il database (`database.sqlite`) è **incluso nella cartella `app/`** e contiene già i dati di esempio. Quando fai il deploy, assicurati che il file abbia permessi di scrittura (`chmod 664` o `666`) altrimenti le prenotazioni e i messaggi non verranno salvati.

---

## 🔐 Credenziali area admin

- URL: `/admin`
- Username: `admin`
- Password: `kokedama2026`

**Cambiala subito dopo il primo accesso!**
