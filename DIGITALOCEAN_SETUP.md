# DigitalOcean Connection (Palmers CRM)

This project already contains DigitalOcean-related deployment/database settings.
Use the steps below to connect your local environment safely.

## 1) Install `doctl` (Windows)

```powershell
winget install DigitalOcean.doctl
```

Verify:

```powershell
doctl version
```

## 2) Set your access token for current shell

```powershell
$env:DIGITALOCEAN_ACCESS_TOKEN="dop_v1_xxxxxxxxxxxxxxxxx"
```

## 3) Connect using the project script

Account only:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\connect-digitalocean.ps1
```

Account + Container Registry login:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\connect-digitalocean.ps1 -RegistryName "sc-projects-repository"
```

Account + Registry + Kubernetes kubeconfig:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\connect-digitalocean.ps1 -RegistryName "sc-projects-repository" -KubernetesCluster "your-cluster-name"
```

## 4) Laravel storage on DigitalOcean Spaces (optional)

If you want file uploads in Spaces, update `.env`:

```dotenv
FILESYSTEM_DRIVER=s3
AWS_ACCESS_KEY_ID=your_spaces_key
AWS_SECRET_ACCESS_KEY=your_spaces_secret
AWS_DEFAULT_REGION=sgp1
AWS_BUCKET=your-space-name
AWS_URL=https://your-space-name.sgp1.digitaloceanspaces.com
```

Then clear config cache:

```powershell
php artisan config:clear
```

## Security Notes

- Do not commit plaintext tokens/passwords into tracked files.
- Rotate any token that has already been committed.
