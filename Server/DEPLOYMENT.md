# Deployment Guide: Aiven MySQL + Render Laravel

## Architecture Overview

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    Vercel       │────▶│     Render      │────▶│     Aiven       │
│  Vue Frontend   │     │  Laravel API    │     │     MySQL       │
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

### Download CA Certificate:
- Go to **Overview** tab
- Click **Download CA Certificate**
- Save this file - you'll need it for Render

### Your Connection String Will Look Like:
```
mysql://avnadmin:YOUR_PASSWORD@mysql-xxxxx-taskmanagement1.j.aivencloud.com:xxxxx/defaultdb?ssl-mode=REQUIRED
```

---

## Step 2: Prepare .env.production File

Create a `.env.production` file with your Aiven credentials:

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.onrender.com
APP_KEY=your_app_key_here

# Database (Aiven MySQL)
DB_CONNECTION=mysql
DB_HOST=mysql-xxxxx-taskmanagement1.j.aivencloud.com
DB_PORT=xxxxx
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=YOUR_PASSWORD
DB_SSL_CA=/etc/ssl/certs/ca-certificates.crt
```

### Generate APP_KEY:
```bash
php artisan key:generate --show
```
Copy the output and paste as `APP_KEY` value.

---

## Step 3: Laravel SSL Configuration

**Aiven requires SSL connection.** Update `config/database.php`:

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

## Step 4: Render Laravel Backend Setup

**No credit card required for free tier**

1. **Go to** [render.com](https://render.com/)

2. **Sign up** with GitHub

3. **Click "New +" → Web Service**

4. **Connect your GitHub repository** (Laravel project)

5. **Configure:**

| Setting | Value |
|---------|-------|
| Name | `task-management-api` |
| Environment | **PHP** |
| Build Command | `composer install --no-dev --optimize-autoloader` |
| Start Command | `php artisan serve --host=0.0.0.0 --port=10000` |
| Plan | **Free** |

6. **Add Environment Variables:**

Paste all variables from your `.env.production` file:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://task-management-api.onrender.com
APP_KEY=base64:your_generated_key

DB_CONNECTION=mysql
DB_HOST=mysql-xxxxx-taskmanagement1.j.aivencloud.com
DB_PORT=xxxxx
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=YOUR_PASSWORD
DB_SSL_CA=/etc/ssl/certs/ca-certificates.crt
```

7. **Upload CA Certificate:**
   - Go to Render dashboard → **Settings**
   - Scroll to **Secret Files** → **Add File**
   - **Path:** `/etc/ssl/certs/ca-certificates.crt`
   - **Content:** Paste your downloaded Aiven CA certificate
   - Click **Save**

8. **Click "Create Web Service"**

---

## Step 5: Run Migrations on Render

After deployment completes:

1. Go to **Shell** tab in Render dashboard
2. Run:
```bash
php artisan migrate --force
```

---

## Step 6: Verify Deployment

**Check Laravel API:**
```
https://task-management-api.onrender.com/api/v1/tasks
```
Should return: `{"data":[]}`

---

## Environment Summary

| Service | URL | Credentials |
|---------|-----|-------------|
| **Aiven MySQL** | `mysql-xxxxx-taskmanagement1.j.aivencloud.com:xxxxx` | Username, Password, CA Cert |
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
- Verify SSL certificate path is `/etc/ssl/certs/ca-certificates.crt`
- Check DB_SSL_CA is set correctly
- Test in Render Shell: `php artisan tinker` → `DB::connection()->getPdo()`

**Migrations fail:**
- Verify Aiven host/port credentials
- Ensure SSL connection is working
- Run: `php artisan migrate --force`

**APP_KEY error:**
- Generate key: `php artisan key:generate --show`
- Add to Render environment variables
- Redeploy or run: `php artisan config:clear` in Shell