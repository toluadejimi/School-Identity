# School Identity NFC Platform

MIFARE/NFC student card platform for school identity, clinic check-in, attendance, exam pass validation, wallet funding, and bus fare payments.

## Stack

- **Backend:** Laravel 13, MySQL/SQLite, Sanctum, Filament Admin, Spatie Permissions
- **Mobile:** Flutter (Android & iOS NFC reading)
- **NFC model:** UID-only lookup; all sensitive data stays on the server

## Project Structure

```
School Identity/
├── backend/          # Laravel API + Filament admin (/admin)
├── mobile/           # Flutter NFC reader app
└── docker-compose.yml
```

## Quick Start (Local)

### Backend

```bash
cd backend
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

- **Admin panel:** http://127.0.0.1:8000/admin
- **Default admin:** `admin@school.local` / `password`

### Mobile App

```bash
cd mobile
flutter pub get
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```

For a physical device, use your computer's LAN IP instead of `127.0.0.1`.

**Demo NFC UID:** `04A1B2C3D4` (seeded for student STU-0001)

### Docker (MySQL)

```bash
docker compose up -d
cd backend
# Set DB_HOST=mysql in .env then:
php artisan migrate --seed
php artisan serve --host=0.0.0.0
```

## API Overview

All authenticated mobile routes require:

- `Authorization: Bearer {token}`
- `X-Device-UUID: {registered_device_uuid}`

| Endpoint | Description |
|----------|-------------|
| `POST /api/v1/auth/login` | Staff login |
| `POST /api/v1/identity/scan` | Verify student identity |
| `POST /api/v1/clinic/check-in` | Clinic visit check-in |
| `POST /api/v1/attendance/scan` | Record attendance |
| `POST /api/v1/exams/scan` | Validate exam pass |
| `POST /api/v1/bus-fare/scan` | Deduct bus fare from wallet |
| `GET /api/v1/bus-routes` | List active routes |

## Roles

| Role | Access |
|------|--------|
| admin | Full admin panel |
| finance | Wallet funding |
| clinic | Clinic module |
| attendance | Attendance module |
| exam | Exam gate checks |
| transport | Bus fare scanning |

## NFC Card Notes

- Cards are identified by **UID only** (hex string).
- For best **iOS + Android** support, use NFC Forum Type 2/4 or NDEF-compatible tags.
- MIFARE Classic works well on Android; iOS support may be limited depending on card type.

## Seeded Demo Accounts

| Email | Password | Role |
|-------|----------|------|
| admin@school.local | password | admin |
| clinic@school.local | password | clinic |
| transport@school.local | password | transport |

## Production Checklist

- Set `APP_ENV=production`, `APP_DEBUG=false`
- Use MySQL with backups
- Configure HTTPS (Nginx + Let's Encrypt)
- Run `php artisan config:cache route:cache`
- Set up queue worker for audit/async jobs
- Register real NFC card UIDs in admin panel
