# Project Tracker

Project Tracker eshte nje aplikacion web i ndertuar me Laravel per menaxhimin e projekteve, detyrave/issues, komenteve dhe etiketave. Aplikacioni ka autentikim per perdoruesit, dashboard, CRUD per projekte dhe issues, si dhe opsione per te organizuar punen me status, prioritet, deadline dhe tags.

## Funksionalitetet

- Regjistrim, login, logout dhe resetim i fjalekalimit.
- Dashboard per perdorues te autentikuar.
- Krijim, shfaqje, ndryshim dhe fshirje te projekteve.
- Krijim, shfaqje, ndryshim dhe fshirje te issues/detyrave.
- Vendosje e statusit, prioritetit dhe dates se perfundimit per issues.
- Komente per secilen issue.
- Tags me emer dhe ngjyre per organizim me te mire.
- Profil i perdoruesit me mundesi ndryshimi dhe fshirjeje.

## Teknologjite

- PHP 8.1+
- Laravel 10
- Laravel Breeze
- MySQL ose SQLite
- Blade
- Tailwind CSS
- Vite
- Bootstrap

## Instalimi

1. Klono projektin ose hape folderin e projektit:

```bash
cd tracker-temp
```

2. Instalo paketat e PHP:

```bash
composer install
```

3. Instalo paketat e frontend-it:

```bash
npm install
```

4. Krijo file-in `.env` nga shembulli:

```bash
cp .env.example .env
```

Ne Windows PowerShell mund te perdoret:

```powershell
Copy-Item .env.example .env
```

5. Gjenero application key:

```bash
php artisan key:generate
```

6. Konfiguro databazen ne `.env`, per shembull:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_tracker
DB_USERNAME=root
DB_PASSWORD=
```

7. Ekzekuto migrimet:

```bash
php artisan migrate
```

## Startimi i projektit

Starto serverin e Laravel:

```bash
php artisan serve
```

Starto Vite per asset-et e frontend-it:

```bash
npm run dev
```

Pastaj hape aplikacionin ne browser:

```text
http://127.0.0.1:8000
```

## Build per production

Per te pergatitur asset-et per production:

```bash
npm run build
```

## Rruget kryesore

- `/register` - regjistrimi i perdoruesit
- `/login` - hyrja ne sistem
- `/dashboard` - dashboard-i kryesor
- `/projects` - menaxhimi i projekteve
- `/issues` - menaxhimi i issues/detyrave
- `/tags` - menaxhimi i etiketave
- `/profile` - profili i perdoruesit

## Autori

Ky projekt eshte krijuar si aplikacion per menaxhimin e projekteve dhe detyrave.
