## Docker

Start the local stack:

```powershell
docker compose up -d --build
```

The app will be available at `http://localhost:18081`.

Run the first-time database setup:

```powershell
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --class=DatabaseSeeder --force
```

Useful commands:

```powershell
docker compose logs -f app
docker compose down
docker compose down -v
```

## Non-Docker

```powershell
composer update
php artisan db:seed --class=DatabaseSeeder
```

## DigitalOcean setup

- See `DIGITALOCEAN_SETUP.md`
- Run `powershell -ExecutionPolicy Bypass -File .\scripts\connect-digitalocean.ps1`
