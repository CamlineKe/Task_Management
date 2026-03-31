# Task Management Application

A full-stack task management application with a Vue 3 frontend and Laravel API backend. Features task prioritization, status tracking, daily reports, and dark mode support.

**Repository:** https://github.com/CamlineKe/Task_Management

## Overview

This project consists of two main components:

- **Frontend** (`frontend/`) - Vue 3 SPA with Pinia state management
- **Backend** (`Server/`) - Laravel 13 RESTful API

## Getting Started

### Prerequisites

- **Node.js**: ^20.19.0 || >=22.12.0
- **PHP**: ^8.3
- **Composer**: 2.x
- **MySQL**: 8.0+ (or SQLite for local dev)

### Clone the Repository

```bash
git clone https://github.com/CamlineKe/Task_Management.git
cd Task_Management
```

### 1. Setup Backend

```bash
cd Server

# Install PHP dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database (choose MySQL or SQLite)
# Edit .env file with your database credentials

# Run migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed --class=TaskSeeder

# Start API server
php artisan serve
```

API will be available at `http://localhost:8000`

### 2. Setup Frontend

In a new terminal:

```bash
cd Task_Management/frontend

# Install dependencies
npm install

# Start dev server
npm run dev
```

Frontend will be available at `http://localhost:5173`

### For Forkers

If you forked this repository:

1. Fork on GitHub
2. Clone your fork: `git clone https://github.com/YOUR_USERNAME/Task_Management.git`
3. Follow the setup steps above
4. The project is ready to run locally - no additional configuration needed

### From ZIP File

If you have the project as a ZIP file:

#### Prerequisites Check

Before starting, verify you have the required software:

```bash
# Check PHP version (must be 8.3 or higher)
php -v

# Check Composer is installed
composer -v

# Check Node.js version (must be 20.19.0 or higher, or 22.12.0+)
node -v

# Check npm is installed
npm -v

# Check MySQL (optional, can use SQLite instead)
mysql -V
```

**Install any missing prerequisites:**
- **PHP 8.3**: https://www.php.net/downloads.php
- **Composer**: https://getcomposer.org/download/
- **Node.js**: https://nodejs.org/ (Download LTS version)
- **MySQL**: https://dev.mysql.com/downloads/

#### Setup Steps

1. **Extract the ZIP file:**
   ```bash
   unzip Task_Management.zip -d Task_Management
   cd Task_Management
   ```

2. **Setup Backend:**
   ```bash
   cd Server
   
   # Install PHP dependencies
   composer install
   
   # Create environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   
   # Configure database (see Database Options below)
   
   # Run migrations
   php artisan migrate
   
   # Optional: Seed with sample data
   php artisan db:seed --class=TaskSeeder
   
   # Start the API server
   php artisan serve
   ```

3. **Setup Frontend (in a new terminal):**
   ```bash
   cd Task_Management/frontend
   
   # Install Node.js dependencies
   npm install
   
   # Start the development server
   npm run dev
   ```

4. **Access the application:**
   - Frontend: http://localhost:5173
   - Backend API: http://localhost:8000

#### Database Setup (MySQL)

1. Create a MySQL database:
   ```sql
   CREATE DATABASE task_management;
   ```
2. Edit `Server/.env` file with your MySQL credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

#### Troubleshooting Common Issues

**"Could not find driver" error:**
- Install PHP MySQL PDO extension:
  - Linux: `sudo apt-get install php8.3-mysql`
  - Windows/Mac: Enable `extension=pdo_mysql` in `php.ini`

**"Permission denied" on storage/logs:**
```bash
cd Server
chmod -R 775 storage bootstrap/cache
```

**"No application encryption key":**
```bash
cd Server
php artisan key:generate
```

**Port already in use:**
- Backend: `php artisan serve --port=8080`
- Frontend: Edit `vite.config.js` to change port

**"composer install" fails with memory error:**
```bash
php -d memory_limit=-1 /usr/local/bin/composer install
```

## Project Structure

```
Task_Management/
├── frontend/               # Vue 3 Frontend
│   ├── src/
│   │   ├── components/     # Vue components
│   │   ├── stores/         # Pinia stores
│   │   ├── views/          # Page components
│   │   └── composables/    # Vue composables
│   └── README.md           # Frontend documentation
│
└── Server/                 # Laravel Backend
    ├── app/
    │   ├── Http/           # Controllers & Requests
    │   ├── Services/       # Business logic
    │   └── Models/         # Eloquent models
    ├── database/
    │   └── migrations/     # Database schema
    └── README.md           # Backend documentation
```

## Features

- **Task Management** - Create, read, update, delete tasks
- **Priority System** - High, medium, low priority levels
- **Status Workflow** - Pending → In Progress → Done
- **Daily Reports** - Task summary by date
- **Dark Mode** - Toggle between light/dark themes
- **Responsive Design** - Works on desktop and mobile
- **Pagination** - 15 tasks per page
- **Filtering** - Filter by status

## Tech Stack

### Frontend
- Vue 3 (Composition API)
- Pinia (State Management)
- Vue Router 5
- Vite 7
- Axios

### Backend
- Laravel 13
- PHP 8.3
- MySQL
- PHPUnit (Testing)

## Documentation

- **Frontend**: See [`frontend/README.md`](frontend/README.md)
- **Backend**: See [`Server/README.md`](Server/README.md)
- **API Docs**: See [`Server/API_DOCUMENTATION.md`](Server/API_DOCUMENTATION.md)
- **Deployment**: See [`Server/DEPLOYMENT.md`](Server/DEPLOYMENT.md)

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/tasks` | Create task |
| GET | `/api/v1/tasks` | List tasks |
| PATCH | `/api/v1/tasks/{id}/status` | Update status |
| DELETE | `/api/v1/tasks/{id}` | Delete task |
| GET | `/api/v1/tasks/report` | Daily report |

### Example API Requests

**Base URL:** `http://localhost:8000` (local) or `https://your-app.onrender.com` (deployed)

#### 1. Create a Task

```bash
curl -X POST http://localhost:8000/api/v1/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Complete project documentation",
    "due_date": "2026-04-15",
    "priority": "high"
  }'
```

**Response (201 Created):**
```json
{
  "id": 1,
  "title": "Complete project documentation",
  "due_date": "2026-04-15",
  "priority": "high",
  "status": "pending",
  "created_at": "2026-03-31T08:00:00Z",
  "updated_at": "2026-03-31T08:00:00Z"
}
```

#### 2. List All Tasks

```bash
curl http://localhost:8000/api/v1/tasks
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Complete project documentation",
      "due_date": "2026-04-15",
      "priority": "high",
      "status": "pending",
      "created_at": "2026-03-31T08:00:00Z",
      "updated_at": "2026-03-31T08:00:00Z"
    }
  ]
}
```

#### 3. Filter Tasks by Status

```bash
curl "http://localhost:8000/api/v1/tasks?status=pending"
```

#### 4. Update Task Status

```bash
curl -X PATCH http://localhost:8000/api/v1/tasks/1/status \
  -H "Content-Type: application/json" \
  -d '{"status": "in_progress"}'
```

**Response (200 OK):**
```json
{
  "id": 1,
  "title": "Complete project documentation",
  "due_date": "2026-04-15",
  "priority": "high",
  "status": "in_progress",
  "created_at": "2026-03-31T08:00:00Z",
  "updated_at": "2026-03-31T10:30:00Z"
}
```

#### 5. Get Daily Report

```bash
curl "http://localhost:8000/api/v1/tasks/report?date=2026-04-15"
```

**Response (200 OK):**
```json
{
  "date": "2026-04-15",
  "summary": {
    "high": {
      "pending": 2,
      "in_progress": 1,
      "done": 0
    },
    "medium": {
      "pending": 1,
      "in_progress": 0,
      "done": 1
    },
    "low": {
      "pending": 0,
      "in_progress": 0,
      "done": 0
    }
  }
}
```

#### 6. Delete a Completed Task

```bash
curl -X DELETE http://localhost:8000/api/v1/tasks/1
```

**Response (204 No Content)** - Empty body on success.

**Error Response (403 Forbidden):**
```json
{
  "message": "Only completed tasks can be deleted"
}
```

See [`Server/API_DOCUMENTATION.md`](Server/API_DOCUMENTATION.md) for complete API documentation.

### Running Tests

**Backend:**
```bash
cd Server
php artisan test
```

**Frontend:**
```bash
cd frontend
npm run test:unit
npm run test:e2e
```

### Linting

**Backend:**
```bash
cd Server
composer run lint
```

**Frontend:**
```bash
cd frontend
npm run lint
```

## Deployment

### Backend - Render (Docker)

Deploy the Laravel API using Render's free tier with Docker.

**Prerequisites:**
- [Aiven](https://aiven.io) account (free MySQL)
- [Render](https://render.com) account (free tier)
- GitHub repository with your code

**Step 1: Aiven MySQL Setup**

1. Go to [aiven.io](https://aiven.io) → Create service → **MySQL**
2. Plan: **Startup-1 (Free)**
3. Copy credentials: Host, Port, Database, Username, Password

> **Note:** The Aiven CA certificate is pre-included at `Server/docker/mysql/aiven-ca.pem`. If you need to replace it with your own certificate, simply overwrite this file before deployment.

**Step 2: Prepare Environment**

Create `Server/.env.production`:

```env
APP_NAME="Task Management API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.onrender.com
APP_KEY=base64:your_generated_key

LOG_CHANNEL=stderr
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=your-aiven-host.aivencloud.com
DB_PORT=your-port
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=your-password
DB_SSL_CA=/etc/ssl/certs/ca-certificates.crt
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

FRONTEND_URL=https://your-frontend-url.com
```

Generate `APP_KEY`:
```bash
cd Server
php artisan key:generate --show
```

**Step 3: Deploy to Render**

1. Go to [render.com](https://render.com) → **New +** → **Web Service**
2. Connect your GitHub repository
3. Configure:
   - **Name**: `task-management-api`
   - **Environment**: `Docker`
   - **Root Directory**: `Server` (if in subfolder)
   - **Dockerfile Path**: `./Dockerfile`
4. Add all environment variables from `.env.production`
5. Click **Create Web Service**

Render will automatically build, run migrations, and start the container.

**Verify Deployment:**
- Health Check: `https://your-app.onrender.com/health` → `{"status":"ok"}`
- API Test: `https://your-app.onrender.com/api/v1/tasks` → `{"data":[]}`

---

### Frontend - Vercel

Deploy the Vue 3 frontend to Vercel's free tier.

**Step 1: Prepare Frontend**

Update `frontend/.env.production`:
```env
VITE_API_BASE_URL=https://your-render-app.onrender.com/api/v1
```

**Step 2: Deploy**

1. Go to [vercel.com](https://vercel.com) → **Add New Project**
2. Import your GitHub repository
3. Configure:
   - **Framework Preset**: `Vite`
   - **Root Directory**: `frontend`
   - **Build Command**: `npm run build`
   - **Output Directory**: `dist`
4. Add Environment Variable: `VITE_API_BASE_URL`
5. Click **Deploy**

**Free Tier Limits:**
- **Aiven**: 1GB MySQL (free forever)
- **Render**: 750 hours/month (free forever, sleeps after idle)
- **Vercel**: Unlimited bandwidth (free for personal projects)

## License

Open-sourced software.

## Support

For issues or questions:
1. Check the respective README files in `frontend/` and `Server/`
2. Review API documentation in `Server/API_DOCUMENTATION.md`
