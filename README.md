# Health ATM Dashboard (Vue) + API (Laravel) + MySQL

## Folders

- `backend/`: Laravel API (device upload + dashboard queries)
- `dashboard/`: Vue 3 dashboard (lists and displays uploaded measurements)

## Backend setup (Laravel)

```bash
cd "backend"
cp .env.example .env
php artisan key:generate
```

Update `.env` for MySQL:

- `DB_DATABASE=healthatm`
- `DB_USERNAME=...`
- `DB_PASSWORD=...`

Run migrations:

```bash
php artisan migrate
php artisan serve
```

### Device upload URL

- `POST /api/health-atm/measurements`
- Full example: `https://munyamatindike.co.zw/api/health-atm/measurements`

### Dashboard APIs

- `GET /api/measurements`
- `GET /api/measurements/latest`
- `GET /api/measurements/{id}`
- `GET /api/whiteList/channel/common/logoConfig`

## Frontend setup (Vue)

```bash
cd "dashboard"
cp .env.example .env
npm install
npm run dev
```

Set `VITE_API_BASE_URL` to your backend API base, for example:

- Local: `http://localhost:8000/api`
- Production: `https://munyamatindike.co.zw/api`

