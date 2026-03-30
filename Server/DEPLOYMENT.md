# Deployment Guide: Aiven MySQL + Render Docker

## Architecture Overview

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    Vercel       │────▶│     Render      │────▶│     Aiven       │
│  Vue Frontend   │     │  Laravel API    │     │     MySQL       │
│                 │     │   (Docker)      │     │                 │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

---

## Step 1: Aiven MySQL Setup

**No credit card required**

1. **Go to** [aiven.io](https://aiven.io/)

2. **Sign up** with email or GitHub

3. **Create new service:**
   - Service type: **MySQL**
   - Cloud: Choose closest region
   - Plan: **Startup-1 (Free)**
   - Name: `task-management-db`

4. **Click Create**

### After Creation - Get Credentials:
- **Host:** `mysql-xxxxx-taskmanagement1.j.aivencloud.com`
- **Port:** `xxxxx`
- **Database:** `defaultdb`
- **Username:** `avnadmin`
- **Password:** (shown once, copy and save)

### Your Connection String Will Look Like:
```
mysql://avnadmin:YOUR_PASSWORD@mysql-xxxxx-taskmanagement1.j.aivencloud.com:xxxxx/defaultdb?ssl-mode=REQUIRED
```

---

## Step 2: Laravel SSL Configuration

**Aiven requires SSL connection.** The `config/database.php` already has SSL configured:

```php
'mysql' => [
    // ... existing config ...
    'options' => [
        PDO::MYSQL_ATTR_SSL_CA => env('DB_SSL_CA'),
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ],
],
```

---

## Step 3: Prepare Environment Variables

Create a `.env.production` file with your Aiven credentials:

```env
# Application
APP_NAME="Task Management API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.onrender.com
APP_KEY=your_app_key_here

# Logging (Docker/Render compatible)
LOG_CHANNEL=stderr
LOG_LEVEL=info

# Database (Aiven MySQL)
DB_CONNECTION=mysql
DB_HOST=mysql-xxxxx-taskmanagement1.j.aivencloud.com
DB_PORT=xxxxx
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=YOUR_PASSWORD
DB_SSL_CA=/etc/ssl/certs/ca-certificates.crt
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt

# Stateless container settings
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

# Frontend URL
FRONTEND_URL=https://your-frontend-url.com
```

### Generate APP_KEY:
```bash
php artisan key:generate --show
```
Copy the output and paste as `APP_KEY` value.

---

## Step 4: Render Docker Deployment

**No credit card required for free tier**

1. **Go to** [render.com](https://render.com/)

2. **Sign up** with GitHub

3. **Click "New +" → Web Service**

4. **Connect your GitHub repository** (Laravel project)

5. **Configure:**

| Setting | Value |
|---------|-------|
| Name | `task-management-api` |
| Environment | **Docker** |
| Dockerfile Path | `./Dockerfile` |
| Plan | **Free** |

6. **Add Environment Variables:**

Paste all variables from your `.env.production` file in the Render dashboard under **Environment**:

```
APP_NAME=Task Management API
APP_ENV=production
APP_DEBUG=false
APP_URL=https://task-management-api.onrender.com
APP_KEY=base64:your_generated_key

LOG_CHANNEL=stderr
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=mysql-xxxxx-taskmanagement1.j.aivencloud.com
DB_PORT=xxxxx
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=YOUR_PASSWORD
DB_SSL_CA=/etc/ssl/certs/ca-certificates.crt
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

FRONTEND_URL=https://your-frontend-url.com
```

7. **Click "Create Web Service"**

Render will automatically:
- Build the Docker image
- Run `php artisan migrate --force`
- Start the container with health checks

---

## Step 5: Verify Deployment

**Health Check:**
```
https://task-management-api.onrender.com/health
```
Should return: `{"status":"ok"}`

**Check Laravel API:**
```
https://task-management-api.onrender.com/api/v1/tasks
```
Should return: `{"data":[]}`

---

## Docker Features

The Dockerfile includes:
- **Multi-stage build** for smaller image size (~80MB)
- **PHP 8.3 with OPcache** for optimal performance
- **Nginx + PHP-FPM** with supervisor process management
- **Pre-optimized Laravel** (config, route, view cached at build time)
- **Health checks** for container monitoring
- **Non-root user** for security

---

## Environment Summary

| Service | URL | Credentials |
|---------|-----|-------------|
| **Aiven MySQL** | `mysql-xxxxx-taskmanagement1.j.aivencloud.com:xxxxx` | Username, Password |
| **Render Laravel** | `https://task-management-api.onrender.com` | Auto-generated |

---

## Free Tier Limits

| Service | Limits | Duration |
|---------|--------|----------|
| **Aiven** | 1GB MySQL | Free forever |
| **Render** | 750 hours/month | Free forever (stops after idle) |

---

## Troubleshooting

**Render can't connect to Aiven:**
- Verify `MYSQL_ATTR_SSL_CA` is set to `/etc/ssl/certs/ca-certificates.crt`
- Check DB_SSL_CA environment variable
- Test in Render Shell: `php artisan tinker` → `DB::connection()->getPdo()`

**Container fails health check:**
- Verify `/health` route is accessible
- Check Render logs for PHP errors
- Ensure `LOG_CHANNEL=stderr` is set

**Migrations fail:**
- Verify Aiven host/port credentials
- Ensure SSL connection is configured
- Run manually in Render Shell: `php artisan migrate --force`

**APP_KEY error:**
- Generate key: `php artisan key:generate --show`
- Add to Render environment variables
- Redeploy the service